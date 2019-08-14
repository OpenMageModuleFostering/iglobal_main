<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 5/9/13
 * Time: 3:24 PM
 * To change this template use File | Settings | File Templates.
 */

class Iglobal_Stores_Model_Observer
{
    public function __construct()
    {
        //Just constrcutor for fun
    }


    public function orderCreated($observer)
    {
        //$observer contains the object returns in the event.
        $event = $observer->getEvent();
        $order = $event->getOrder();
		$table = "sales_flat_order"; 
		$tableName = Mage::getSingleton("core/resource")->getTableName($table); 
        //mage::log($order->getId());

        $array = $order->getData();
		
		if (isset($array['relation_parent_id'])){
			$parentId = $array['relation_parent_id'];
        }
		
		if (isset($parentId)) {

            $parentOrder = Mage::getModel('sales/order')->load($parentId);
            $parentData = $parentOrder->getData();
			
			if($parentData['ig_order_number']){
				
				$igcOrderId = $parentData['ig_order_number'];
				
				if ($parentData['iglobal_test_order'] == '1') {
					//Set the international_order flag and the ig_order_number on the order and mark as a test order
					$query = "UPDATE `" . $tableName . "` SET `international_order` = 1, `ig_order_number` = '{$igcOrderId}', `iglobal_test_order` = 1 WHERE `entity_id` = '{$array['entity_id']}'";
				} else {
					//Set the international_order flag and the ig_order_number on the order
					$query = "UPDATE `" . $tableName . "` SET `international_order` = 1, `ig_order_number` = '{$igcOrderId}' WHERE `entity_id` = '{$array['entity_id']}'";	
				}
				
				Mage::getSingleton('core/resource')->getConnection('core_write')->query($query);
				
			}
        }
        return $this;
    }
	
/**
 * Added jQuery library.
 *
 * @param Varien_Event_Observer $observer
 *
 * @return string
 */
    public function prepareLayoutBefore(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('stores')->isEnabled()) {
            return $this;
        }

        /* @var $block Mage_Page_Block_Html_Head */
        $block = $observer->getEvent()->getBlock();

        if ("head" == $block->getNameInLayout()) {
            foreach (Mage::helper('stores')->getFiles() as $file) {
                $block->addJs(Mage::helper('stores')->getJQueryPath($file));
            }
        }

        return $this;
    }
    
    public function logme(){
	Mage::log("TA-DA!!!!!!!!");
    }
    
    public function orderRec () {	
	
		//get array with all orders in past
		$rest = Mage::getModel('stores/rest_order'); // get rest model		
			
			//fetch all orders for the store from iGlobal server		
			$data = $rest->getAllOrdersSinceDate('20140526');
			$orderData = $data['order'];		
			$restOrders = array();
			
			//build array of orders with keypairs "ig_order_number" => "the number as a string"
			foreach($orderData as $row => $order){
				if($data['testOrder'] == "false"){
					$newId = $order['id'];
					array_push($restOrders, $newId);
				}
			}
			
			//build array of orders currently in magento	
			$reader = Mage::getSingleton('core/resource')->getConnection('core_read'); // get our connection to the DB
			$importedIgOrdersQuery = "Select `ig_order_number` from `sales_flat_order` where `international_order` = 1 AND `ig_order_number` IS NOT NULL"; //select rows that are ig orders
			$importedIgOrders = $reader->fetchAll($importedIgOrdersQuery);	//fetch them all

			//fix teh array so it matches  our array of all orders
			$magentoOrders = array();
			foreach ($importedIgOrders as $importedIgOrder) {
				$newId = $importedIgOrder['ig_order_number'];
				array_push($magentoOrders, $newId);
			}
		
			//compare arrays, removing orders already in magento from list of all orders, remainder are orders that didn't import
			$missedOrders = array_diff($restOrders, $magentoOrders);
			
		if (count($missedOrders) > 0) {
			//build email to send
			if (count($missedOrders) == 1) {				
				$body = '<div style=" border-top: 5px solid #88d600; border-bottom: 5px solid #88d600;"><div style="background-color: #ebebea; position: relative; height: 80px; border-bottom: 2px solid #414c50; padding: 15px;"><a style="float: left;" href="https://account.iglobalstores.com"><img src="https://checkout.iglobalstores.com/images/iglobal-exports.png" alt="iGlobal Stores Logo" /></a><h2 style="font-family: arial,sans-serif; color: #414c50; padding-left: 20px; text-align: left;float: left;font-size: 28px;">You\'ve Got An Order!</h2></div><div style="clear: both; padding: 20px; font-family: arial,sans-serif; color: #414c50;"><p>It looks like you\'ve received an interanational order but it weren\'t successfully imported into your system. The following iGlobal Stores order failed to import:</p><ul style="color: black;"> <li>' . join('</li><li>', $missedOrders) . '</li></ul><p>We recommend that you review the orders and enter them manually. These orders have already been paid for. You can always <a href="http://www.iglobalstores.com/contact-us.html">contact us</a> with any questions. Thanks!</p><p>Sincerely,<br />The iGlobal Stores team</p></div></div>';
			} else {
				$body = '<div style=" border-top: 5px solid #88d600; border-bottom: 5px solid #88d600;"><div style="background-color: #ebebea; position: relative; height: 80px; border-bottom: 2px solid #414c50; padding: 15px;"><a style="float: left;" href="https://account.iglobalstores.com"><img src="https://checkout.iglobalstores.com/images/iglobal-exports.png" alt="iGlobal Stores Logo" /></a><h2 style="font-family: arial,sans-serif; color: #414c50; padding-left: 20px; text-align: left;float: left;font-size: 28px;">You\'ve Got Orders!</h2></div><div style="clear: both; padding: 20px; font-family: arial,sans-serif; color: #414c50;"><p>It looks like you\'ve received some interanational orders but they weren\'t successfully imported into your system. The following iGlobal Stores orders failed to import:</p><ul style="color: black;"> <li>' . join('</li><li>', $missedOrders) . '</li></ul><p>We recommend that you review the orders and enter them manually. These orders have already been paid for. You can always <a href="http://www.iglobalstores.com/contact-us.html">contact us</a> with any questions. Thanks!</p><p>Sincerely,<br />The iGlobal Stores team</p></div></div>';
			}			
			$mail = Mage::getModel('core/email');
			$mail->setToName('iGlobal Customer');
			if (Mage::getStoreConfig('iglobal_integration/apireqs/admin_email')) {
				$mail->setBcc('monitoring@iglobalstores.com');
				$mail->setCc('magentomonitoring@iglobalstores.com');
				$mail->setToEmail(Mage::getStoreConfig('iglobal_integration/apireqs/admin_email')); 
			} else {
				$mail->setToEmail('monitoring@iglobalstores.com');
			}
			$mail->setBody($body);
			$mail->setSubject('International orders that need your help being processed: Your Attention Required');
			$mail->setFromEmail('support@iglobalstores.com');
			$mail->setFromName("iGlobal Import Error");
			$mail->setType('html');

			//send email that includes list of  orders that didn't import, and log results of send
			 try {
				$mail->send();
				Mage::log("Email notification of needed order reconciliation successfully sent.  Effected orders are: " . join("," ,$missedOrders), null, 'iglobal.log');
			} catch (Exception $e) {
				Mage::log("EMAIL SEND FAILURE - Merchant was not notified of needed order reconciliation! Missed order numbers are: " . join("," ,$missedOrders), Zend_Log::ERR, 'iglobal.log', true);
			} 
		}
    }
}
