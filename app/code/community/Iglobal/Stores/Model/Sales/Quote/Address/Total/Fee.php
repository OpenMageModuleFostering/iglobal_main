<?php
class Iglobal_Stores_Model_Sales_Quote_Address_Total_Fee extends Mage_Sales_Model_Quote_Address_Total_Tax
{
	public function collect(Mage_Sales_Model_Quote_Address $address)
	{

		$fee = Mage::registry('duty_tax');
		if ($fee) {
			// Do not override taxes if there is no duty_tax set.
			$this->_setAddress($address);
			$this->_setAmount($fee);
			$this->_setBaseAmount($fee);
		} else {
			parent::collect($address);
		}
	}
}