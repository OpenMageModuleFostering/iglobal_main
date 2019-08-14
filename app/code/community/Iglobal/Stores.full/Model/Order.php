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
        } elseif ($status == self::STATUS_IN_PROCESS && $order->canUnhold()) {
            $order->unhold();
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
        if ($this->iglobal_order->merchantOrderId)
        {
           //return false;
        }
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
        $order = $this->createOrder();
        Mage::unregister('duty_tax');
        Mage::unregister('shipping_cost');
        Mage::unregister('shipping_carriertitle');
        Mage::unregister('shipping_methodtitle');
        return $order;
    }
    protected function regionId($state, $countryCode){
      $region = Mage::getModel('directory/region')->loadbyName($state, $countryCode);
      if (!$region->getId())
      {
        // Lookup region from iGlobalstores
        $regionId = $this->rest->getRegionId($countryCode, $state, $this->iglobal_order_id);
        $region->load($regionId->magentoRegionId);
        if (!$region->getId())
        {
          try {
          // Create a new region
          $region->setData(array('country_id' => $countryCode, 'code'=> $regionId->isoCode,'default_name' => $state))->save();
          } catch (Exception $e) {
            return null;
          }
        }
      }
      return $region->getId();
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

        $addressData = array(
            'firstname' => $name_first,
            'lastname' => $name_last,
            'street' => $street,
            'city' => $this->iglobal_order->city,
            'postcode' => $this->iglobal_order->zip,
            'telephone' => $this->iglobal_order->phone,
            'region' => $this->iglobal_order->state,
            'region_id' => $this->regionId($this->iglobal_order->state, $this->iglobal_order->countryCode),
            'country_id' => $this->iglobal_order->countryCode,
            'company' => $this->iglobal_order->company,
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
                'region' => $this->iglobal_order->billingState,
                'region_id' => $this->regionId($this->iglobal_order->billingState, $this->iglobal_order->billingCountryCode),
                'country_id' => $this->iglobal_order->billingCountryCode,
                'company' => $this->iglobal_order->company,
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
            // $this->quote->removeItem($item->getId());
        }
    }

    protected function setShipping($shippingAddress)
    {
        $shippers = Mage::getModel("stores/carrier")->getAllowedMethods();
        $carrierMethod = $this->iglobal_order->shippingCarrierServiceLevel;
        if (!$carrierMethod || !array_key_exists($carrierMethod, $shippers)) {
            $carrierMethod = 'ig';
        }
        $shippingMethod = $this->iglobal_order->customerSelectedShippingName;
        if(!$shippingMethod) {
            $shippingMethod = "International shipping";
        }

        //Add things to the register so they can be used by the shipping method
        Mage::register('shipping_cost', $this->iglobal_order->shippingTotal);
        Mage::register('shipping_carriertitle', $carrierMethod);
        Mage::register('shipping_methodtitle', $shippingMethod);
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('ig_'.$carrierMethod);
        return $shippingAddress;
    }

    protected function setPayment($address)
    {
        $address->setPaymentMethod('iGlobalCreditCard');

        //updates payment type in Magento Admin area
        if (isset($this->iglobal_order->paymentProcessing)) {
            $data = (array) $this->iglobal_order->paymentProcessing;
        } else {
            $data = array();
        }
        $data['ccType'] = 'AMEX';
        if(isset( $this->iglobal_order->paymentProcessing->paymentGateway)) {
            $paymentMethod = $this->iglobal_order->paymentProcessing->paymentGateway;
        } else if (isset($this->iglobal_order->paymentProcessing->cardType)) {
            $paymentMethod = $this->iglobal_order->paymentProcessing->cardType;
        } else {
            $paymentMethod = 'iGlobal';
        }
        switch($paymentMethod) {
            case 'iGlobal_CC':
            case 'AUTHORIZE_NET':
            case 'BRAINTREE':
            case 'CYBERSOURCE':
            case 'INTERPAY':
            case 'STRIPE':
            case 'USA_EPAY':
                $data['method'] = 'iGlobalCreditCard';
                break;
            case 'iGlobal PayPal':
            case 'INTERPAY_PAYPAL':
            case 'PAYPAL_EXPRESS':
                $data['method'] = 'iGlobalPaypal';
                break;
            default:
                $data['method'] = 'iGlobal';
        }
        $this->quote->getPayment()->importData($data);
    }

    protected function setTransactionInfo($order){
        //add trans ID
        try {
            $transaction_id = $this->iglobal_order->paymentProcessing->transactionId;
        } catch (Exception $e){
            $transaction_id = '34234234234';
        }
        if(!$transaction_id) {
            $transaction_id = '34234234234';
        }
        $transaction = Mage::getModel('sales/order_payment_transaction');
        $transaction->setOrderPaymentObject($order->getPayment());
        $transaction->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
        $transaction->setTxnId($transaction_id);
        if(isset($this->iglobal_order->paymentProcessing)) {
            $transaction->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, (array)$this->iglobal_order->paymentProcessing);
            if ($this->iglobal_order->paymentProcessing->transactionType == "AUTH_CAPTURE") {
                $transaction->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE);
            }
        }
        $transaction->save();
    }

    protected function createOrder()
    {

        $this->quote->collectTotals()->save();
        $this->quote->setIsActive(0)->save();

        $service = Mage::getModel('stores/service_quote', $this->quote);
        $service->submitAll();
        $order = $service->getOrder();
        if($order) {
          // cleaning up
          Mage::getSingleton('checkout/session')->clear();
        } else {
          $this->quote->setIsActive(1)->save();
          return;
        }


        $id = $order->getEntityId();

        if (Mage::getStoreConfig('iglobal_integration/apireqs/send_order_email')) {
                $order->sendNewOrderEmail();
        }
        Mage::getSingleton('checkout/session')->setLastOrderId($order->getId());
        Mage::getSingleton('checkout/session')->setLastRealOrderId($order->getIncrementId());


        $transId = Mage::getSingleton('checkout/session')->getLastRealOrderId($order->getIncrementId());
        //Save Order Invoice as paid
        $commentMessage = 'Order automatically imported from iGlobal order ID: '. $this->iglobal_order_id;
        try {
            $order = Mage::getModel("sales/order")->load($id);
            $this->setTransactionInfo($order);

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

            // add customer notes
            if($this->iglobal_order->notes){
                foreach ($this->iglobal_order->notes as $note) {
                  if($note->customerNote) {
                      $order->addStatusHistoryComment($note->note, false);
                  }
                }
            }
            $extraNote = "";
            if($this->iglobal_order->birthDate) {
                $extraNote .= "Birthdate: " . $this->iglobal_order->birthDate . "\n";
            }
            if($this->iglobal_order->nationalIdentifier) {
                $extraNote .= "National Identifier: " . $this->iglobal_order->nationalIdentifier . "\n";
            }
            if($this->iglobal_order->boxCount) {
                $extraNote .= "Boxes: " . $this->iglobal_order->boxCount . "\n";
            }

            if($extraNote) {
                $order->addStatusHistoryComment($extraNote, false);
            }
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
