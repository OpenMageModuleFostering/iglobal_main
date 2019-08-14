<?php

class Iglobal_Stores_AjaxController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{

		echo Mage::getBaseUrl();
	    $this->loadLayout();
	    $this->renderLayout();
	}

	public function icedataAction () {

		//storeID for ice 
		$iceData = array ();
		 if  (Mage::getStoreConfig('iglobal_integration/apireqs/iglobalid')) {
			$iceData['storeId'] = Mage::getStoreConfig('iglobal_integration/apireqs/iglobalid');
		 }
		 //subdomain for iCE
		 if  (Mage::getStoreConfig('iglobal_integration/apireqs/igsubdomain')) {
			$iceData['subdomain'] = Mage::getStoreConfig('iglobal_integration/apireqs/igsubdomain');
		 }

		//Cart Url for redirects
		$iceData['cartUrl'] = Mage::getUrl('checkout/cart');
		//echo $iceData['cartUrl'];

		//Zend_Debug::dump($iceData);
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($iceData));

	}

	public function matdataAction () {
		$this->getResponse()->setBody(Mage::helper('stores')->getJavascriptVars());
	}
}