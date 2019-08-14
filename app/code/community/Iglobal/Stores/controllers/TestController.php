

<?php

class Iglobal_Stores_TestController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {

	echo "in the controller <br/>";
	
	    //   $_order = "902-111111";
		   $_order = $this->getRequest()->getParam('orderId', null);
			
            $quote = Mage::getSingleton('checkout/session')->getQuote()->setStoreId(Mage::app()->getStore()->getId());//if more than one store, then this should be set dynamically
		$table = "sales_flat_order"; 
		$tableName = Mage::getSingleton("core/resource")->getTableName($table); 
		$existsQuery = "SELECT `entity_id` FROM `" . $tableName. "` WHERE `ig_order_number` = '{$_order}'";
		$orderExists = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchRow($existsQuery);

		echo 'My Query: ' . $existsQuery . '<br />';
		echo 'My Query result: ' . $orderExists . '<br />';
		Zend_Debug::dump($orderExists);
		
        if(!$_order ){ 
			echo 'Order is not defined. <br />'; 
		}else { 
			echo 'Order is defined <br />';
		}
		
        if($orderExists){ 
			echo 'Order Exists. <br />'; 
		}else { 
			echo 'Order does not exist. <br />';
		}
   
		if(!$_order || $orderExists){
			echo "I'm going to stop this right here.";
		}else {
			echo "I'll let you through.";
		};

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

