

<?php

class Iglobal_Stores_TestController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {

	echo "in the controller <br/>";
	

        $_order = '902-107604'; // has different addresses
	 //$_order = '902-107620'; // has same addresses

            $rest = Mage::getModel('stores/rest_order');
            $data = $rest->getOrder($_order);
echo "Data in controller: <br />";
Zend_Debug::dump($data);
			
            $_name = explode(' ', $data['name'], 2);
	     if ($data['testOrder'] == "true") {
		     $name_first = "TEST ORDER! DO NOT SHIP! - " . array_shift($_name);
		     $name_last = array_pop($_name);
		} else {
		     $name_first = array_shift($_name);
		     $name_last = array_pop($_name);		
		}

            $street = $data['address2'] ?  array($data['address1'], $data['address2']) : $data['address1'];
			
			// to fix error with countries w/o zip codes
			if (is_array($data['zip'])){
				$igcZipCode = ' ';
			}else {
				$igcZipCode = $data['zip'];
			}

            $addressData = array(
                'firstname' => $name_first,
                'lastname' => $name_last,
                'street' => $street,
                'city' => $data['city'],
                'postcode' => $igcZipCode,
                'telephone' => $data['phone'],
		  'region' => $data['state'],
		  //'region' => 'utah',
		  //'region_id' => 'ut',
                'country_id' => $data['countryCode'],
		  'company' => $data['company'],
            );
	     
		$billingCheckVar = $data['billingAddress1'];
		if (!empty($billingCheckVar)){ 
			$_nameBilling = explode(' ', $data['billingName'], 2);
		     if ($data['testOrder'] == "true") {
			     $name_first_billing = "TEST ORDER! DO NOT SHIP! - " . array_shift($_nameBilling);
			     $name_last_billing = array_pop($_nameBilling);
			} else {
			     $name_first_billing = array_shift($_nameBilling);
			     $name_last_billing = array_pop($_nameBilling);		
			}
			

		     $streetBilling = $data['billingAddress2'] ? array($data['billingAddress1'], $data['billingAddress2']) : $data['billingAddress1'];
				
				// to fix error with countries w/o zip codes
				if (is_array($data['billingZip'])){
					$igcZipCodeBilling = ' ';
				}else {
					$igcZipCodeBilling = $data['billingZip'];
				}
		     
		     $billingAddressData = array(
			  'firstname' => $name_first_billing,
			  'lastname' => $name_last_billing,
			  'street' => $streetBilling,
			  'city' => $data['billingCity'],
			  'postcode' => $igcZipCodeBilling,
			  'telephone' => $data['billingPhone'],
			  'region' => $data['billingState'],
			  'country_id' => $data['billingCountryCode'],
		     );
	     } else {
			$billingAddressData = $addressData;
	     }
	echo "Address Data: <br />";
	Zend_Debug::dump($addressData);
	
	
	echo "Billing Address Data: <br />";
	Zend_Debug::dump($billingAddressData);
/*
// Load the session
$session = Mage::getModel('checkout/cart');
// Array to hold the final result
$finalResult = array();
// Loop through all items in the cart
foreach ($session->getQuote()->getAllItems() as $item)
{
  // Array to hold the item's options
  $result = array();
  // Load the configured product options
  $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
  echo 'Options: <br />';
  Zend_Debug::dump($options);
  // Check for options
  if ($options)
  {
    if (isset($options['options']))
    {
      $result = array_merge($result, $options['options']);
    }
     echo 'result: <br />';
    Zend_Debug::dump($result);
    
    		$igOptionList = '<dl class="item-options">';
		foreach ($result as $optionDetails) {
			//$_formatedOptionValue = $this->getFormatedOptionValue($optionDetails);
			$label = '<dt>' . $optionDetails['label'] . '</dt>';
			$value = '<dd>' . $optionDetails['value']  .'</dd>';
			$igOptionList = $igOptionList . $label . $value;
		}
		$igOptionList = $igOptionList  . '</dl>';
	
	echo 'my list: <br />';
	Zend_Debug::dump($igOptionList);	
		
    echo 'my result: '.$result['label'] .':'.$result['value'];
    
 /*   if (isset($options['additionaloptionDetailss']))
    {
      $result = array_merge($result, $options['additionaloptionDetailss']);
    }
    if (!empty($options['attributes_info']))
    {
      $result = array_merge($options['attributes_info'], $result);
    }
  }
  $finalResult = array_merge($finalResult, $result);
}
// Now you have the final array of all configured options
 echo 'final result: <br />';
Zend_Debug::dump($finalResult);



		echo '<div class="igTestDiv">';
	if ($optionDetailss = Mage::getModel('catalog/product')->load($item->getProductId())->getOptionList()) {
		$igOptionList = '<dl class="item-options">';
		foreach ($optionDetailss as $optionDetails) {
			$_formatedOptionValue = $this->getFormatedOptionValue($optionDetails);
			$label = '<dt>' . $this->escapeHtml($optionDetails['label']) . '</dt>';
			$value = '<dd>' . $_formatedOptionValue['full_view'] .'</dd>';
			$igOptionList = $igOptionList . $lable . $value;
		}
		$igOptionList = $igOptionList  . '</dl>';
	}
	echo $igOptionList;
echo '</div>';*/

    }


}

