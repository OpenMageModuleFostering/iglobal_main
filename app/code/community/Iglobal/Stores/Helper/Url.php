<?php
class Iglobal_Stores_Helper_Url extends Mage_Checkout_Helper_Url
{

    function getCheckoutUrl()
    {
        // check if the country is international
        // $countryCode = (isset($_COOKIE['igCountry']) ? $_COOKIE['igCountry'] : "");
        // $domesticCountries = explode(",", Mage::getStoreConfig('general/country/ig_domestic_countries'));
        // $isDomestic = in_array ($countryCode, $domesticCountries) ? 1 : 0;

        // return the url
        if (Mage::getStoreConfig('iglobal_integration/igmat/welcome_mat_active')
            && Mage::getStoreConfig('iglobal_integration/apireqs/ice_toggle')
            && Mage::getStoreConfig('iglobal_integration/apireqs/use_iframe')) {
            //  check for welcome mat  //!$isDomestic && Mage::getStoreConfig('iglobal_integration/apireqs/ice_toggle')){ // add checks for other admin configs

            // make this a javascript url
            return 	"javascript:if(!ig_isDomesticCountry()){window.location.replace (' " . $this->_getUrl('iglobal/checkout') . "');} else {window.location.replace ('" . $this->_getUrl('checkout/onepage') . "');}";
            //return "javascript:if(!ig_isDomesticCountry()){window.location.replace ('{$this->_getUrl('iglobal/checkout')}')return false;} else {window.location.replace ('{$this->_getUrl('checkout/onepage')}";
            //return $this->_getUrl('iglobal/checkout');
        } else {
            return $this->_getUrl('checkout/onepage');
        }
    }

}
