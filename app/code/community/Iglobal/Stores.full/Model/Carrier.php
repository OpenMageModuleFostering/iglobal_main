<?php
class Iglobal_Stores_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface {
	protected $_code = 'ig';

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
			$method->setMethod(Mage::registry('shipping_carriertitle'));
			$method->setCarrierTitle($this->_code);
			$method->setMethodTitle(Mage::registry('shipping_methodtitle'));
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
        return array($this->_code =>$this->getConfigData('name'),
            'DHL_EXPRESS' => 'Express',
            'DHL_GLOBAL_MAIL' => 'Global Mail',
            'FEDEX_ECONOMY' => 'Economy',
            'FEDEX_GROUND' => 'Ground',
            'FEDEX_IPD' => 'FedEx IPD',
            'FEDEX_PRIORITY' => 'Priority',
            'UPS_2ND_DAY_AIR' => 'UPS 2 Day Air',
            'UPS_3_DAY_AIR' => 'UPS 3 Day Air',
            'UPS_3_DAY_SELECT' => 'UPS_3_DAY_SELECT',
            'UPS_EXPEDITED' => 'Expedited',
            'UPS_EXPRESS' => 'Express',
            'UPS_EXPRESS_SAVER' => 'Express Saver',
            'UPS_FREIGHT' => 'UPS Freight',
            'UPS_GROUND' => 'Canada Ground',
            'UPS_NEXT_DAY_AIR_SAVER' =>'UPS Next Day Air Saver',
            'UPS_SAVER' => 'UPS_SAVER',
            'UPS_STANDARD' => 'Canada Standard',
            'UPS_WORLDEASE' => 'UPS WorldEase',
            'UPS_WORLDWIDE_EXPEDITED' => 'Expedited',
            'UPS_WORLDWIDE_EXPRESS' => 'Express',
            'USPS_EXPRESS_1' => 'Express 1 Mail',
            'USPS_FIRST_CLASS' => 'First Class Mail',
            'USPS_FIRST_CLASS_MAIL_INTERNATIONAL' => 'First Class Mail, International',
            'USPS_FIRST_CLASS_PACKAGE_INTL_SERVICE' => 'First Class Mail, International',
            'USPS_PRIORITY' => 'USPS Priority',
            'USPS_PRIORITY_DOMESTIC' =>'USPS Priority Domestic',
            'USPS_PRIORITY_EXPRESS' => 'USPS Priority Express',
            'USPS_PRIORITY_EXPRESS_INTL' => 'USPS Priority Express',
            'USPS_PRIORITY_INTL' => 'USPS Priority',
            'USPS_PRIORITY_MAIL_EXPRESS_INTERNATIONAL' => 'Priority Mail Express, International',
            'USPS_PRIORITY_MAIL_INTERNATIONAL' => 'Priority Mail, International',
        );

	}
}
