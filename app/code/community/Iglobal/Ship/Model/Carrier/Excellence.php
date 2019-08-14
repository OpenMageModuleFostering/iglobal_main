<?php
class Iglobal_Ship_Model_Carrier_Excellence extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface {
	protected $_code = 'excellence';

	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
			return false;
		}
		
		$price = $this->getConfigData('price'); // set a default shipping price maybe 0

		$result = Mage::getModel('shipping/rate_result');
		$show = Mage::registry('shipping_cost');
		if($show){

			$method = Mage::getModel('shipping/rate_result_method');
			$method->setCarrier($this->_code);
			$method->setMethod($this->_code);
			$method->setCarrierTitle(Mage::registry('shipping_carriertitle'));
			$method->setMethodTitle(Mage::registry('shipping_methodtitle'));
//			$method->setCarrierTitle($this->getConfigData('title'));
//			$method->setMethodTitle($this->getConfigData('name'));
			if(Mage::registry('shipping_cost')){
				$method->setPrice(Mage::registry('shipping_cost'));
				$method->setCost(Mage::registry('shipping_cost'));
			} else {
				$method->setPrice($price);
				$method->setCost($price);
			}
			$result->append($method);

		}else{
			$error = Mage::getModel('shipping/rate_result_error');
			$error->setCarrier($this->_code);
			$error->setCarrierTitle($this->getConfigData('name'));
			$error->setErrorMessage($this->getConfigData('specificerrmsg'));
			$result->append($error);
		}
		return $result;
	}
	public function getAllowedMethods()
	{
		return array('excellence'=>$this->getConfigData('name'));
	}
}