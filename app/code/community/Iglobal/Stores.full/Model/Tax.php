<?php
class Iglobal_Stores_Model_Tax extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	protected $_code = 'tax';
	public function setCode($code)
    {
		// store the international fee in the tax field.
		return $this;
    }
	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		if(Mage::getStoreConfig('iglobal_integration/apireqs/ice_toggle'))
		{
			$fee = Mage::registry('duty_tax');
			if ($fee)
			{
				$this->_setAddress($address);
				$this->_setAmount($fee);
				$this->_setBaseAmount($fee);
			}
		}
		return $this;
	}
}

