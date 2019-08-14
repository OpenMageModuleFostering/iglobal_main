<?php


class Iglobal_Stores_Model_International_International extends Mage_Core_Model_Abstract
{
	public function getTempCartId ()
	{

		//get all the items in the cart
		$cart = Mage::getModel('checkout/cart')->getQuote();
		$items = array();

		foreach ($cart->getAllVisibleItems() as $index=>$item) {
			$product = $item->getProduct();
			$sku = $item->getSku();
			$dimUnits = $product->getIgDemesionUnits();
			$dim2inch = ['cm' => 2.54, 'in' => 1, '' => 1];
			$weight = $product->getIgWeight();
			if (empty($weight))
			{
				$weight = $item->getWeight();
			}

			$items[] = [
				"description" => $item->getName(),
				"productId" => $item->getProductId(),
				"sku" => $product->getTypeId() == 'bundle' ? substr($sku, strpos($sku, '-') + 1) : $sku,
				"unitPrice" => $item->getPrice(),
				"quantity" => $item->getQty(),
				"length" => ceil(floatval($product->getIgLength()) / $dim2inch[$dimUnits]),
				"width" => ceil(floatval($product->getIgWidth()) / $dim2inch[$dimUnits]),
				"height" => ceil(floatval($product->getIgHeight()) / $dim2inch[$dimUnits]),
				"weight" => $weight,
				"weightUnits" => strtoupper($product->getIgWeightUnits()),
				"itemURL" => $product->getProductUrl(),
				"imageURL" => str_replace("http:", "https:", Mage::helper('catalog/image')->init($product, 'thumbnail')),
				"itemDescriptionLong" => $product->getDescription(),

			];
		}

		$rest = Mage::getModel('stores/rest');
		$response = $rest->createTempCart([
			"storeId" =>Mage::getStoreConfig('iglobal_integration/apireqs/iglobalid'),
			"items" => $items,]);
		return $response->tempCartUUID;
	}
}
?>