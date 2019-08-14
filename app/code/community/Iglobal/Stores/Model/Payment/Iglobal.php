<?php

class Iglobal_Stores_Model_Payment_Iglobal extends Mage_Payment_Model_Method_Checkmo
{

    protected $_code  = 'iGlobal';
    protected $_canUseInternal = true;
    protected $_canUseCheckout = false;
    protected $_canUseForMultishipping = false;

    public function getPayableTo()
    {
        return false;
    }

    public function getMailingAddress()
    {
        return false;
    }
}