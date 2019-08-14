

<?php

class Iglobal_Stores_TestController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {

	echo "in the controller <br/>";
	

	$cart = Mage::getModel('checkout/cart')->getQuote()->getAllItems();
	Zend_Debug::dump($cart);

	
	
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

