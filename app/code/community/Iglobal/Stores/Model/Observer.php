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
        //Just constructor for fun
    }


    public function orderCreated($observer)
    {
        //$observer contains the object returns in the event.
        $event = $observer->getEvent();
        $order = $event->getOrder();
		if($order->getRelationParentId()){
            $parentOrder = Mage::getModel('sales/order')->load($order->getRelationParentId());
			if($parentOrder->getIgOrderNumber()){
				$order->setIglobalTestOrder($parentOrder->getIglobalTestOrder());
				$order->setIgOrderNumber($parentOrder->getIgOrderNumber());
				$order->setInternationalOrder(1);
				$order->save();
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
    
    public function orderRec () {

        //build array of orders currently in magento
        $magentoOrders = array();
		$orders = Mage::getModel('sales/order')->getCollection()
			->addFilter('international_order', 1)
			->addFieldToFilter('ig_order_number', array('notnull'=> true))
			->getItems();

        foreach ($orders as $order) {
			$magentoOrders[$order->getIgOrderNumber()] = $order;
        }

		//get array with all orders in past
		$data = Mage::getModel('stores/rest')->getAllOrdersSinceDate('20151006');
		foreach ($data->orders as $igOrder)
        {

            if ($igOrder->testOrder)
            {
                continue;
            }
            if(array_key_exists($igOrder->id, $magentoOrders)) {
                // check status
                Mage::getModel('stores/order')->checkStatus($magentoOrders[$igOrder->id]);

            } else {
                try {
                    // re-import the order
                    // Mage::getModel('stores/order')->processOrder($igOrder->id);
                }
                catch(Exception $e)
                {
                    mail('monitoring@iglobalstores.com, magentomissedorders@iglobalstores.com',
                        'Magento Integration Error - International order failed to import',
                        'International order# '. $igOrder->id .'.'. ' Exception Message: '.$e->getMessage());
                    Mage::log("International order #{$igOrder->id} failed to import!"  .$e, Zend_Log::ERR, 'iglobal.log', true);
                }

            }
        }
    }
}
