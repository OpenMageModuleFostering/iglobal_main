<?php


class Iglobal_Stores_Model_International_International extends Mage_Core_Model_Abstract
{
	public function getTempCartId ()
	{

		//get all the items in the cart
		$cart = Mage::getModel('checkout/cart')->getQuote();
		$items = array();
		$helper = Mage::helper('stores');
		foreach ($cart->getAllVisibleItems() as $item) {
			$items[] = $helper->getItemDetails($item);
		}
		// Check for discounts to add as a negative line item
		$totals = $cart->getTotals();
		if (isset($totals['discount']))
		{
			$items[] = array(
				'description' => $totals['discount']->getTitle(),
				'quantity' => 1,
				'unitPrice' => $totals['discount']->getValue()
			);
		}
		$rest = Mage::getModel('stores/rest');
		$response = $rest->createTempCart(array(
			"storeId" => Mage::getStoreConfig('iglobal_integration/apireqs/iglobalid'),
			"referenceId" => $cart->getId(),
			"externalConfirmationPageURL" => MAge::getUrl('iglobal/success', array('_secure'=> true)),
			"misc6" => "iGlobal v".Mage::getConfig()->getModuleConfig("Iglobal_Stores")->version. ", Magento v".Mage::getVersion(),
			"items" => $items,));
		return $response->tempCartUUID;
	}
}
