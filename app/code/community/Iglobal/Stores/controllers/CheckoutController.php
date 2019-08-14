<?php
//
// Load the iGlobal hosted checkout in an iframe, cause it's awesome
//
class Iglobal_Stores_CheckoutController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        //todo: add a check to see if they are domestic and then redirect to domestic checkout
		if (Mage::getStoreConfig('iglobal_integration/apireqs/force_login') && !Mage::getSingleton('customer/session')->isLoggedIn()) {
			Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('iglobal/checkout'));
			Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('customer/account/login'));
		}

        $cartQty = (int) Mage::getModel('checkout/cart')->getQuote()->getItemsQty();
        if (!$cartQty) {
            $this->_redirect('checkout/cart');
           die();
		   //echo "The Cart is Empty";
        }

        $tempcart = Mage::getModel('stores/international_international');

		if (!$tempcart) {
			echo "No tempcart!";
			die();
		}
		
		$cartId = $tempcart->getTempCartId();
		
		// echo out the html that will build the iframe
		$domCode = 	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> <html xmlns="http://www.w3.org/1999/xhtml"> <head> <title>International Checkout</title> <style type="text/css"> body, html {margin: 0; padding: 0; height: 100%; overflow: hidden;} #content{position:absolute; left: 0; right: 0; bottom: 0; top: 0;} </style></head><body><div id="content"><iframe width="100%" height="100%" frameborder="0" src="';
        //this is where we build the url for the checkout
		$subdomain = (Mage::getStoreConfig('iglobal_integration/apireqs/igsubdomain') ? Mage::getStoreConfig('iglobal_integration/apireqs/igsubdomain') : "checkout");
		$storeNumber = (Mage::getStoreConfig('iglobal_integration/apireqs/iglobalid') ? Mage::getStoreConfig('iglobal_integration/apireqs/iglobalid') : "3");
		$countryCode = (isset($_COOKIE['igCountry']) ? $_COOKIE['igCountry'] : "");
		
		$iframeUrl = 'https://' . $subdomain . '.iglobalstores.com/?store=' . $storeNumber . '&tempCartUUID=' . $cartId . '&country=' . $countryCode;

        $domCode = $domCode . $iframeUrl . '"/></div></body></html>';

        echo $domCode;

	}
	
}