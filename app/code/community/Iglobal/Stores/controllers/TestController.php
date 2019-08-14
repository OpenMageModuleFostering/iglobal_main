<?php

class Iglobal_Stores_TestController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {

	echo "in the controller <br/>";
	echo Mage::getStoreConfig('iglobal_integration/igmat/flag_code');

       /* $_order = $this->getRequest()->getParam('orderId', null);

	
 
            $rest = Mage::getModel('stores/rest_order');
            $data = $rest->getOrder($_order);
	     
	     //Mage::log($data['testOrder'], null, 'iglobal.log'); 
	     
	     	 
	$var = $data['dutyTaxesTotal'];
	
	echo 'ddp: ' . $var . '<br />';
	    
	 $email = $data['email'];    
	 $country = $data['countryCode'];
	 $test = $data['testOrder'];
	     
	 echo "email: " . $email . "<br />";
	 echo "country: " . $country . "<br />";
	 echo "test order: " . $test . "<br />";
	    
	    if ($data['testOrder'] = true) {
		echo "stuff <br />";
	     }
*/
    }

}
