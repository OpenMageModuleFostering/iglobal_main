<?php
class Iglobal_Stores_Helper_Url extends Mage_Checkout_Helper_Url
{

    function getCheckoutUrl()
    {
        // return the url
        if (Mage::getStoreConfig('iglobal_integration/igmat/welcome_mat_active')
            && Mage::getStoreConfig('iglobal_integration/apireqs/ice_toggle')) {
            // send them to the iglobal checkout
            return 	$this->_getUrl('iglobal/checkout');
        } else {
            return $this->_getUrl('checkout/onepage');
        }
    }

}
