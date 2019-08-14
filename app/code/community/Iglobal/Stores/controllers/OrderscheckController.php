

<?php

class Iglobal_Stores_OrderscheckController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
	
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
		$table = "sales_flat_order"; 
		$tableName = Mage::getSingleton("core/resource")->getTableName($table); 
		$reader = Mage::getSingleton('core/resource')->getConnection('core_read'); // get our connection to the DB
		$importedIgOrdersQuery = "Select * from `" . $tableName . "` where `international_order` = 1 AND `ig_order_number` IS NOT NULL"; //select rows that are ig orders
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
			echo '<div style="width:50%;background-color:#c1c1c1;overflow:hidden;">Missed Orders: <br />';
			foreach ($missedOrders as $missedOrder){
				echo $missedOrder . '<br />';
			}
			//var_dump($missedOrders);
			echo '</div>';
		} else {
			echo '<div style="width:50%;background-color:#c1c1c1;overflow:hidden;">There are no orders that have not been imported<br /></div>';
		}
	
	/*	if (count($restOrders) > 0) {
			echo '<div style="width:50%;background-color:#e0e0e0;overflow:hidden;float:left;">Rest Orders: <br />';
			var_dump($restOrders);
			echo '</div>';
		} else {
			echo '<div style="width:50%;background-color:#e0e0e0;overflow:hidden;float:left;">All orders from iGlobal are test orders.<br />';
		}
		
		if (count($magentoOrders) > 0) {
			echo '<div style="width:50%;background-color:#e0e0e0;overflow:hidden;float:left;">Magento Orders: <br />';
			var_dump($magentoOrders);
			echo '</div>';
		} else {
			echo '<div style="width:50%;background-color:#e0e0e0;overflow:hidden;float:left;">You don\'t have any orders from iGlobal imported yet.<br />';
		} */
		
		
	//create array of duplicate orders
		$allOrders = array();
		foreach ($importedIgOrders as $importedIgOrder) {
			$order = Mage::getModel('sales/order')->load($importedIgOrder['entity_id']);
			$Incrementid = $importedIgOrder['entity_id'];
			$magentoOrderId = $order->getIncrementId(); 
			$allOrders[$magentoOrderId] = $importedIgOrder['ig_order_number'];
		}
		
		//create a complicated way to show duplicates, because PHP only makes it easy to find unique values.  for shame, php.  for shame.
		$duplicateOrders = array_diff_assoc($allOrders,array_unique($allOrders)); 

		echo '<table style="width:50%;background-color:#e0e0e0;border: 1px solid black;"><thead><tr><th>Orders duplicated in Magento (does not include the actual order, only the duplicates)</th></tr><tr><th>Magento Order Number</th><th>iGlobal Order Number</th></tr></thead><tbody>';
			
			foreach ($duplicateOrders as $key=>$value) {
				echo '<tr><td>' . $key . '</td><td>' . $value . '</td></tr>';
			}
		echo '</tbody></table>' ;
		
/*			//get array of all
			$dupsQuery = "SELECT *, COUNT(*) as count FROM " . $tableName ." GROUP BY ig_order_number HAVING COUNT(*) > 1";
			$duplicatedIgOrders = $reader->fetchAll($dupsQuery);
			//var_dump($duplicatedIgOrders);
			
			//foreach to get $orderId
			foreach ($duplicatedIgOrders as $order){
				$igNumber = $order['ig_order_number'];
				$magentoNumber = $order['entity_id'];
				//array_push($magentoOrders, $newId);			
				echo $igNumber . ":" . $magentoNumber;
				//echo $igNumber;
			}
			
			//remove from array if no duplicate
			
			//echo unique order numbers, paired with ig_order number
		$order = Mage::getModel('sales/order')->load($orderid);
		$Incrementid = $order->getIncrementId();
*/
    }


}

