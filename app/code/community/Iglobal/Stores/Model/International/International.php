<?php


class Iglobal_Stores_Model_International_International extends Mage_Core_Model_Abstract
{
	protected $_entryPoint = null;
    
    protected function _construct()
    {	
 
		
    }
	
	public function setParameters ($client) {
		
		//get all the items in the cart
		$cart = Mage::getModel('checkout/cart')->getQuote();
		//start a loop to set each one
		//$i = 0;
		foreach ($cart->getAllVisibleItems() as $index=>$item) {
			$itemNumber = ++$index;
			// This is because Magento fails to load the custom attributes hence we have to go back to the DB.
			$weight = "";
			$length = "";
			$width = "";
			$height = "";

			try {
				$allItemData = Mage::getModel('catalog/product')->load($item['product_id']);
				$weightUnits = "";
				$dimUnits = "";

				try {
					$weightUnits = "";
					if (!empty($allItemData['ig_weight_units'])) {
						$weightUnits = $allItemData->getAttributeText('ig_weight_units');
					}
				} catch (Exception $e) {
					$weightUnits = "";
				}

				try {
					$weight = "";
					if (!empty($allItemData['ig_weight'])) {
						$weight = $allItemData->getData('ig_weight');
					} else if (!empty($allItemData['weight'])) {
						$weight = $item->getWeight();
					}

					if (!empty($weight)) {
						if ($weightUnits=="kg") {
							$weight = round(floatval($weight) / 0.453592, 2);
						} else if ($weightUnits=="oz") {
							$weight = round(floatval($weight) / 16, 2);
						} else if ($weightUnits=="g") {
							$weight = round(floatval($weight) / 453.592, 2);
						} else {//Default is lbs
							$weight = round(floatval($weight), 2);
						}
					} else {
						$weight = "";
					}
				} catch(Exception $e) {
					$weight = "";
				}

				try {
					$dimUnits = "";
					if (!empty($allItemData['ig_dimension_units'])) {
						$dimUnits = $allItemData->getAttributeText('ig_dimension_units');
					}
				} catch (Exception $e) {
					$dimUnits = "";
				}
				try {
					$length = "";
					if (!empty($allItemData['ig_length'])) {
						$length = $allItemData->getData('ig_length');
					}
						$width = "";
					if (!empty($allItemData['ig_width'])) {
						$width = $allItemData->getData('ig_width');
					}
						$height = "";
					if (!empty($allItemData['ig_height'])) {
						$height = $allItemData->getData('ig_height');
					}
					if (!empty($length) && !empty($width) && !empty($height)) {
						if ($dimUnits=="cm") {
							$length = ceil(floatval($length) / 2.54);
							$width = ceil(floatval($width) / 2.54);
							$height = ceil(floatval($height) / 2.54);
						} else {//Default is inches
							$length = ceil(floatval($length));
							$width = ceil(floatval($width));
							$height = ceil(floatval($height));
						}
					} else {
						$length = "";
						$width = "";
						$height = "";
					}
				} catch(Exception $e) {
					$length = "";
					$width = "";
					$height = "";
				}
			} catch (Exception $outerE) {

			}

			$itemId = $item->getProductId();
			$itemName = $item->getName();
			$itemSku = $item->getProduct()->getTypeId() == 'bundle' ? substr($item->getSku(), strpos($item->getSku(), '-')+1) : $item->getSku();
			$itemQty = $item->getQty();
			$itemPrice = $item->getPrice();
			$itemProductUrl = Mage::getModel('catalog/product')->load($item->getProductId())->getProductUrl();
			$itemImageUrl = str_replace("http:", "https:",Mage::helper('catalog/image')->init($item->getProduct(), 'thumbnail'));
			$itemDescription = Mage::getModel('catalog/product')->load($item->getProductId())->getDescription();
			$itemShortDescription = Mage::getModel('catalog/product')->load($item->getProductId())->getDescription();
			$itemMageWeight = $item->getWeight();
			$itemIgWeight = $weight;
			$itemIgLength = $length;
			$itemIgWidth = $width;
			$itemIgHeight = $height;
			
			if($itemId) $client->setParameterPost('itemProductId'.$itemNumber, $itemId);
			if($itemName) $client->setParameterPost('itemDescription'.$itemNumber, $itemName);
			if($itemSku) $client->setParameterPost('itemSku'.$itemNumber, $itemSku);
			if($itemQty) $client->setParameterPost('itemQuantity'.$itemNumber, $itemQty);
			if($itemPrice) $client->setParameterPost('itemUnitPrice'.$itemNumber, $itemPrice);
			if($itemProductUrl) $client->setParameterPost('itemURL'.$itemNumber, $itemProductUrl);
			if($itemImageUrl) $client->setParameterPost('itemImageURL'.$itemNumber, $itemImageUrl);
			if($itemDescription) $client->setParameterPost('itemDescription'.$itemNumber, $itemDescription);
			//if($itemShortDescription) $client->setParameterPost('itemProductIdN'.$itemNumber, $itemShortDescription);
			if($itemIgLength) $client->setParameterPost('itemLength'.$itemNumber, $itemIgLength);
			if($itemIgWidth) $client->setParameterPost('itemWidth'.$itemNumber, $itemIgWidth);
			if($itemIgHeight) $client->setParameterPost('itemHeight'.$itemNumber, $itemIgHeight);
			if($itemIgWeight) {
				$client->setParameterPost('itemWeight'.$itemNumber, $itemIgWeight);
            } elseif ($itemMageWeight) {
				$client->setParameterPost('itemWeight'.$itemNumber, $itemMageWeight);
			}

		}
	}
    
	public function getTempCartId ()
	{
		//build a POST and return the response
		
		$storeNumber = Mage::getStoreConfig('iglobal_integration/apireqs/iglobalid');
		
		$client = new Varien_Http_Client('https://checkout.iglobalstores.com/iglobalstores/services/TempCartService');
		$client->setMethod(Varien_Http_Client::POST);
		
		$client->setParameterPost('store', $storeNumber);
		$this->setParameters($client);
		
		//$client->setParameterPost('itemDescription1', 'My item for testing temp carts');
		//$client->setParameterPost('itemQuantity1', '1');
		//$client->setParameterPost('itemUnitPrice1', '123.45');
		//set the parameters in a loop
		
		//$client->setParameterPost('address', '123 fake st');//$address);
		//$client->setParameterPost('address', $address);
		
		//more parameters
		//Zend_Debug::dump($client);
		
		Mage::log("the client data" . print_r($client, true), null, 'international.log', true);
		
		try{
			$response = $client->request();
			if ($response->isSuccessful()) {
				return $response->getBody();
			}
		} catch (Exception $e) {
			die($e);
		}
		
		
		echo "I'm in the function!";
	}
    
}
?>