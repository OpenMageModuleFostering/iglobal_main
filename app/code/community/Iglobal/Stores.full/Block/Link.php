<?php

//this block override returns the url to be used on the primary checkout buttons on the cart page.
//If you are using a custom checkout then this may or may not still work.
//If not, you will need to find how the checkout determines the url, and apply a similar method.
// also refer to ../Helper/Url.php

class Iglobal_Stores_Block_Link extends Mage_Checkout_Block_Onepage_Link
{
    function getCheckoutUrl()
    {
        //return the url
        if (Mage::getStoreConfig('iglobal_integration/igmat/welcome_mat_active')
            && Mage::getStoreConfig('iglobal_integration/apireqs/ice_toggle')){
            return $this->getUrl('iglobal/checkout', array('_secure'=>true));
        } else {
            return $this->getUrl('checkout/onepage', array('_secure'=>true));
        }
    }
}