<?php

class Iglobal_Stores_Model_Order extends Mage_Core_Model_Abstract
{

    const STATUS_FRAUD      = 'IGLOBAL_FRAUD_REVIEW';
    const STATUS_IN_PROCESS = 'IGLOBAL_ORDER_IN_PROCESS';
    const STATUS_HOLD       = 'IGLOBAL_ORDER_ON_HOLD';
    const STATUS_CANCELED   = 'IGLOBAL_ORDER_CANCELED';

    protected $quote = null;
    protected $iglobal_order_id = null;
    protected $iglobal_order = null;
    protected $rest = null;

    public function setQuote($quote)
    {
        $this->quote = $quote;
    }

    public function checkStatus($order)
    {
        if (!$this->iglobal_order)
        {
            $this->setIglobalOrder($order->getIgOrderNumber());
        }
        $status = $this->iglobal_order->orderStatus;
        if (($status == self::STATUS_FRAUD || $status == self::STATUS_HOLD || $status == self::STATUS_CANCELED) && $order->canHold()) {
            $order->hold();
            $order->addStatusHistoryComment("Order Set to {$status} by iGlobal", false);
            $order->save();
        } elseif ($status == self::STATUS_IN_PROCESS && $order->canUnHold()) {
            $order->unHold();
            $order->addStatusHistoryComment("Order Set to {$status} by iGlobal", false);
            $order->save();
        }
    }
    public function setIglobalOrder($orderid)
    {
        $this->iglobal_order_id = $orderid;
        if (!$this->iglobal_order)
        {
            $this->rest = Mage::getModel('stores/rest');
            $this->iglobal_order = $this->rest->getOrder($this->iglobal_order_id)->order;
        }
    }

    public function processOrder($orderid, $quote=NULL)
    {
        $this->setIglobalOrder($orderid);

        // check the if this is the same quote that was sent.
        if ($quote)
        {
            $this->quote = $quote;
        } elseif (!$this->quote) {
            $this->quote = Mage::getSingleton('checkout/session')->getQuote();
        }

        if ($this->iglobal_order->referenceId && $this->iglobal_order->referenceId != $this->quote->getId())
        {
            $this->quote->load($this->iglobal_order->referenceId);
        }

        // Set the duty_tax for the address total collection to use
        Mage::register('duty_tax', $this->iglobal_order->dutyTaxesTotal);
        $shippingAddress = $this->setContactInfo();
        $this->setItems();
        $shippingAddress = $this->setShipping($shippingAddress);
        $this->setPayment($shippingAddress);
        return $this->createOrder();
    }

    protected function setContactInfo()
    {
        //set customer info
        $this->quote->setCustomerEmail($this->iglobal_order->email);

        $_name = explode(' ', $this->iglobal_order->name, 2);
        if ($this->iglobal_order->testOrder) {
            $name_first = "TEST ORDER! DO NOT SHIP! - " . array_shift($_name);
            $name_last = array_pop($_name);
        } else {
            $name_first = array_shift($_name);
            $name_last = array_pop($_name);
        }

        $this->quote->setCustomerFirstname($name_first);
        $this->quote->setCustomerLastname($name_last);

        $street = $this->iglobal_order->address1;
        if ($this->iglobal_order->address2)
        {
            $street = array($street, $this->iglobal_order->address2);
        }

        $region = Mage::getModel('directory/region')
            ->loadbyName($this->iglobal_order->state, $this->iglobal_order->countryCode);
        if (!$region->getId())
        {
            // Lookup region from iGlobalstores
            $regionId = $this->rest->getRegionId(
                $this->iglobal_order->countryCode,
                $this->iglobal_order->state,
                $this->iglobal_order_id);
            $region->load($regionId->magentoRegionId);
            if (!$region->getId())
            {
                // Create a new region
                $region->setData(array(
                    'country_id' => $this->iglobal_order->countryCode,
                    'defalt_name' => $this->iglobal_order->state
                ))->save();

            }
        }

        $addressData = array(
            'firstname' => $name_first,
            'lastname' => $name_last,
            'street' => $street,
            'city' => $this->iglobal_order->city,
            'postcode' => $this->iglobal_order->zip,
            'telephone' => $this->iglobal_order->phone,
            'region' => $this->iglobal_order->state,
            'region_id' => $region->getId(),
            'country_id' => $this->iglobal_order->countryCode,
            'company' => '',//$this->iglobal_order->company,
        );

        if (!empty($this->iglobal_order->billingAddress1)) {
            $_nameBilling = explode(' ', $this->iglobal_order->billingName, 2);
            if ($this->iglobal_order->testOrder) {
                $name_first_billing = "TEST ORDER! DO NOT SHIP! - " . array_shift($_nameBilling);
                $name_last_billing = array_pop($_nameBilling);
            } else {
                $name_first_billing = array_shift($_nameBilling);
                $name_last_billing = array_pop($_nameBilling);
            }

            $streetBilling = $this->iglobal_order->billingAddress1;
            if ($this->iglobal_order->billingAddress2) {
                $streetBilling = array($streetBilling, $this->iglobal_order->billingAddress2);
            }

            $billingAddressData = array(
                'firstname' => $name_first_billing,
                'lastname' => $name_last_billing,
                'street' => $streetBilling,
                'city' => $this->iglobal_order->billingCity,
                'postcode' => $this->iglobal_order->billingZip,
                'telephone' => $this->iglobal_order->billingPhone,
                'region' => $this->iglobal_order->state,
                'region_id' => $region->getId(),
                'country_id' => $this->iglobal_order->billingCountryCode,
            );

        } else {
            $billingAddressData = $addressData;
        }

        if (Mage::getStoreConfig('iglobal_integration/igjq/iglogging'))
        {
            Mage::log('address data for order {$this->iglobal_order_id}: ' . print_r($addressData, true), null, 'iglobal.log', true);
            Mage::log('billing address data for order {$this->iglobal_order_id}: ' . print_r($billingAddressData, true), null, 'iglobal.log', true);
        }

        $this->quote->getBillingAddress()->addData($billingAddressData);
        $shippingAddress = $this->quote->getShippingAddress()->addData($addressData);
        return $shippingAddress;
    }

    protected function setItems()
    {
        $quote_items = array();
        $ig_items = array();
        foreach($this->quote->getAllVisibleItems() as $item) {
            $quote_items[$item->getProductId()] = $item;
        }
        foreach ($this->iglobal_order->items as $item) {
            if ($item->productId) { // discounts do not have a productId set
                $ig_items[$item->productId] = $item;
            }
        }

        $missing = array_diff_key($ig_items, $quote_items);
        $extra = array_diff_key($quote_items, $ig_items);
        foreach ($missing as $pid => $item)
        {
            // Add the product to the quote
            $product = Mage::getModel("catalog/product")->load($pid);
            if ($product->getId())
            {
                $this->quote->addProduct($product, new Varien_Object(array('qty'=> $item->quantity)));
            } else {
                Mage::log("Missing sku `{$item->sku}' for {$this->iglobal_order_id}", null, 'iglobal.log', true);
            }
        }
        foreach($extra as $item)
        {
            $this->quote->removeItem($item->getId());
        }
    }

    protected function setShipping($shippingAddress)
    {
        //Figure out shipping carrier name etc.
        $shippers = array(
            'DHL_EXPRESS' =>          array('DHL', 'Express - iGlobal'),
            'DHL_GLOBAL_MAIL'=>       array('DHL', 'Global Mail - iGlobal'),
            'FEDEX_ECONOMY'=>         array('FedExl', 'Economy - iGlobal'),
            'FEDEX_GROUND'=>          array('FedEx', 'Ground - iGlobal'),
            'FEDEX_PRIORITY'=>        array('FedEx', 'Priority - iGlobal'),
            'UPS_EXPEDITED'=>         array('UPS', 'Expedited - iGlobal'),
            'UPS_EXPRESS'=>           array('UPS', 'Express - iGlobal'),
            'UPS_EXPRESS_SAVER' =>    array('UPS', 'Express Saver - iGlobal'),
            'UPS_GROUND' =>           array('UPS', 'Canada Ground - iGlobal'),
            'UPS_STANDARD'=>          array('UPS', 'Canada Standard - iGlobal'),
            'USPS_FIRST_CLASS_MAIL_INTERNATIONAL'=>      array('USPS', 'First Class Mail, International - iGlobal'),
            'USPS_PRIORITY_MAIL_EXPRESS_INTERNATIONAL'=> array('USPS', 'Priority Mail Express, International - iGlobal'),
            'USPS_PRIORITY_MAIL_INTERNATIONAL'=>         array('USPS', 'Priority Mail, International - iGlobal'),
            'APC_EXPEDITED_MAIL'=>    array('UPS', 'APC Expedited 3-5 Days - iGlobal'),
            'APC_PRIORITY_MAIL'=>     array('UPS', 'APC Priority Mail 4-9 Days - iGlobal'),
            'CANADA_POST_EXPEDITED'=> array('UPS', 'Canada Post Expedited - iGlobal'),
            'FEDEX_IPD'=>             array('UPS', 'FedEx IPD - iGlobal'),
            'UPS_2ND_DAY_AIR'=>       array('UPS', 'UPS 2 Day Air - iGlobal'),
            'UPS_3_DAY_AIR'=>         array('UPS', 'UPS 3 Day Air - iGlobal'),
            'UPS_FREIGHT'=>           array('UPS', 'UPS Freight - iGlobal'),
            'UPS_MAIL_INNOVATIONS'=>  array('UPS', 'Bodyguardz - UPS Mail Innovations - iGlobal'),
            'UPS_NEXT_DAY_AIR_SAVER'=>array('UPS', 'UPS Next Day Air Saver - iGlobal'),
            'UPS_WORLDEASE'=>         array('UPS', 'UPS WorldEase - iGlobal'),
            'USPS_EPACKET'=>          array('UPS', 'USPS ePacket - iGlobal'),
            'USPS_EXPRESS_1'=>        array('UPS', 'Express 1 Mail - iGlobal'),
            'USPS_IPA'=>              array('UPS', 'USPS IPA - iGlobal'),
            'LANDMARK_LGINTREGU' =>   array('iGlobal', 'Landmark'),
            'LANDMARK_LGINTSTD' =>    array('iGlobal', 'Landmark'),
            'LANDMARK_LGINTSTDU' =>   array('iGlobal', 'Landmark'),
            'MSI_PARCEL' =>           array('iGlobal', 'Landmark'),
            'MSI_PRIORITY' =>         array('iGlobal', 'Landmark'),
            'default' =>              array('iGlobal', 'International Shipping'),
        );
        $carrierMethod = $this->iglobal_order->shippingCarrierServiceLevel;
        if (!isset($shippers[$carrierMethod]))
        {
          $carrierMethod = 'default';
        }
        $shipper = $shippers[$carrierMethod];

        //Add things to the register so they can be used by the shipping method
        Mage::register('shipping_cost', $this->iglobal_order->shippingTotal);
        Mage::register('shipping_carriertitle', $shipper[0]);
        Mage::register('shipping_methodtitle', $shipper[1]);
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('excellence_excellence');
        return $shippingAddress;
    }

    protected function setPayment($address)
    {
        $address->setPaymentMethod('iGlobalCreditCard');

        //updates payment type in Magento Admin area
        $paymentMethod = $this->iglobal_order->paymentProcessing->paymentGateway;
        if($paymentMethod === 'iGlobal_CC'){
            $paymentType = 'iGlobalCreditCard';
        } else if ($paymentMethod === 'iGlobal PayPal') {
            $paymentType = 'iGlobalPaypal';
        } else {
            $paymentType = 'iGlobal';
        }

        $this->quote->getPayment()->importData(array('method' => $paymentType));
    }

    protected function createOrder()
    {

        $this->quote->collectTotals()->save();
        $this->quote->setIsActive(0)->save();

        $service = Mage::getModel('stores/service_quote', $this->quote);
        $service->submitAll();
        $order = $service->getOrder();

        // cleaning up
        Mage::getSingleton('checkout/session')->clear();

        $id = $order->getEntityId();

        if (!Mage::helper('sales')->canSendNewOrderEmail() && Mage::getStoreConfig('iglobal_integration/apireqs/send_order_email')) {
                $order->sendNewOrderEmail();
        }
        Mage::getSingleton('checkout/session')->setLastOrderId($order->getId());
        Mage::getSingleton('checkout/session')->setLastRealOrderId($order->getIncrementId());


        $transId = Mage::getSingleton('checkout/session')->getLastRealOrderId($order->getIncrementId());
        //Save Order Invoice as paid
        $commentMessage = 'Order automatically imported from iGlobal order ID: '. $this->iglobal_order_id;

        try {
            $order = Mage::getModel("sales/order")->load($id);
            //add trans ID
            try {
                $transaction_id = $this->iglobal_order->paymentProcessing->transactionId;
            } catch (Exception $e){
                $transaction_id = '34234234234';
            }
            $transaction = Mage::getModel('sales/order_payment_transaction');
            $transaction->setOrderPaymentObject($order->getPayment());
            $transaction->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
            $transaction->setTxnId($transaction_id);
            $transaction->save();

            $invoices = Mage::getModel('sales/order_invoice')->getCollection()->addAttributeToFilter('order_id', array('eq'=>$order->getId()));
            $invoices->getSelect()->limit(1);
            if ((int)$invoices->count() == 0 && $order->getState() == Mage_Sales_Model_Order::STATE_NEW) {
                if(!$order->canInvoice()) {
                    $order->addStatusHistoryComment($commentMessage, false);
                    $order->addStatusHistoryComment('iGlobal: Order cannot be invoiced', false);
                    $order->save();
                } else {
                    $order->addStatusHistoryComment($commentMessage, false);
                    $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
                    $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                    $invoice->register();
                    $invoice->getOrder()->setCustomerNoteNotify(false);
                    $invoice->getOrder()->setIsInProcess(true);
                    $order->addStatusHistoryComment('Automatically INVOICED by iGlobal', false);
                    $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                      ->addObject($invoice->getOrder());
                    $transactionSave->save();
                }
            }
            $this->checkStatus($order);
        } catch (Exception $e) {
            $order->addStatusHistoryComment('iGlobal Invoicer: Exception occurred during automatically invoicing. Exception message: '.$e->getMessage(), false);
            $order->save();
        }
        if ($this->iglobal_order->testOrder) {
            $order->setIglobalTestOrder(1);
        }
        $order->setIgOrderNumber($this->iglobal_order_id);
        $order->setInternationalOrder(1);
        $order->save();

        //Send the magento id to iGlobal
        $this->rest->sendMagentoOrderId($this->iglobal_order_id, $order->getIncrementId());
        return $order;
    }


}
