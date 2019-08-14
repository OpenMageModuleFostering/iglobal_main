<?php

class Iglobal_Stores_SuccessController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {


        $_order = $this->getRequest()->getParam('orderId', null);
        try
        {
            $quote = Mage::getSingleton('checkout/session')->getQuote()->setStoreId(Mage::app()->getStore()->getId());
            $table = "sales_flat_order";
            $tableName = Mage::getSingleton("core/resource")->getTableName($table);
            $existsQuery = "SELECT `entity_id` FROM `" . $tableName. "` WHERE `ig_order_number` = '{$_order}'";
            $orderExists = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchRow($existsQuery);

            if(!$_order || $orderExists) {
                header('Location: /');
                die();
            }

            $order = Mage::getModel('stores/order');
            $order->setQuote($quote);
            $order->processOrder($_order);
        }
        catch(Exception $e)
        {
            $adminEmail = false;
            //die($e);
            if (Mage::getStoreConfig('iglobal_integration/apireqs/admin_email')) {
                $adminEmail = Mage::getStoreConfig('iglobal_integration/apireqs/admin_email');
            }
            mail('monitoring@iglobalstores.com', 'Magento Integration Error - International order failed to import', 'International order# '. $_order .'.'. ' Exception Message: '.$e->getMessage());
            mail('magentomissedorders@iglobalstores.com', 'Magento Integration Error - International order failed to import', 'International order# '. $_order .'.'. ' Exception Message: '.$e->getMessage());
            if ($adminEmail) {
                mail($adminEmail, 'iGlobal Import Error - International order failed to import', 'iGlobal International order# '. $_order . " failed to import properly.  We've already received notice of the problem, and are probably working on it as you read this.  Until then, you may manually enter the order, or give us a call for help at 1-800-942-0721." );
            }
            Mage::log("International order #{$_order} failed to import!"  .$e, Zend_Log::ERR, 'iglobal.log', true);
        }

        $this->loadLayout();

        $lastOrderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));

        $this->renderLayout();
    }

}
