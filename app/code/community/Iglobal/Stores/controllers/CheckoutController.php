<?php
//
// Load the iGlobal hosted checkout in an iframe, cause it's awesome
//
class Iglobal_Stores_CheckoutController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		//check to see if they are domestic and then redirect to domestic checkout
		$helper = Mage::helper('stores');
		if($helper->isDomestic()){
			$this->_redirect('checkout/onepage');
			return;
		}

		if (Mage::getStoreConfig('iglobal_integration/apireqs/force_login') && !Mage::getSingleton('customer/session')->isLoggedIn()) {
			Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('iglobal/checkout'));
			Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('customer/account/login'));
		}

        $cartQty = (int) Mage::getModel('checkout/cart')->getQuote()->getItemsQty();
        if (!$cartQty) {
            $this->_redirect('checkout/cart');
           return;
        }

        $tempcart = Mage::getModel('stores/international_international');

		if (!$tempcart) {
			echo "No tempcart!";
			die();
		}
		
		$cartId = $tempcart->getTempCartId();
		$url = $helper->getCheckoutUrl($cartId);

		if(Mage::getStoreConfig('iglobal_integration/apireqs/use_iframe')) {
			// echo out the html that will build the iframe
			$domCode = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
			$domCode .= '<html xmlns="http://www.w3.org/1999/xhtml"> <head> <title>International Checkout</title>';
			$domCode .= '<style type="text/css"> body, html {margin: 0; padding: 0; height: 100%; overflow: hidden;} #content{position:absolute; left: 0; right: 0; bottom: 0; top: 0;} </style>';
			$domCode .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
			$domCode .= '</head><body>';
			$domCode .= '<div id="content"><iframe width="100%" height="100%" frameborder="0" src="';
			$domCode .= $url . '"/></div></body></html>';
			echo $domCode;
		} else {
			$this->_redirectUrl($url);
		}
	}
	
}