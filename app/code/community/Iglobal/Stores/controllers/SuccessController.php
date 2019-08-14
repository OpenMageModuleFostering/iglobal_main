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


			$rest = Mage::getModel('stores/rest_order');
			$data = $rest->getOrder($_order);
			//Mage::log("the data in the controller for order {$_order}:" . print_r($data, true), null, 'mattscustom.log', true);	
			//set ddp
			$quote->setFeeAmount($data['dutyTaxesTotal']); 
			
			//set customer info
			$quote->setCustomerEmail($data['email']);

			$_name = explode(' ', $data['name'], 2);
			if ($data['testOrder'] == "true") {
				 $name_first = "TEST ORDER! DO NOT SHIP! - " . array_shift($_name);
				 $name_last = array_pop($_name);
			} else {
				 $name_first = array_shift($_name);
				 $name_last = array_pop($_name);		
			}
			
			$quote->setCustomerFirstname($name_first);
			$quote->setCustomerLastname($name_last);

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
			

			//Mage::log('address data for order {$_order}: ' . print_r($addressData, true), null, 'mattscustom.log', true);
		
			//Mage::log('billing address data for order {$_order}: ' . print_r($billingAddressData, true), null, 'mattscustom.log', true);	

			//Figure out shipping carrier name etc.
			$shippingRate = $data['shippingTotal'];
			$shippingCarrierMethod = $data['shippingCarrierServiceLevel'];
			switch ($shippingCarrierMethod) {
				case 'DHL_EXPRESS' :
					$shipper = 'excellence_excellence';
					$shippingCarrierTitle = 'DHL';
					$shippingMethodTitle = 'Express - iGlobal';
					break;
				case 'DHL_GLOBAL_MAIL' :
					$shipper = 'excellence_excellence';
					$shippingCarrierTitle = 'DHL';
					$shippingMethodTitle = 'Global Mail - iGlobal';
					break;
				case 'FEDEX_ECONOMY' :
					$shipper = 'excellence_excellence';
					$shippingCarrierTitle = 'FedExl';
					$shippingMethodTitle = 'Economy - iGlobal';
					break;
				case 'FEDEX_GROUND' :
					$shipper = 'excellence_excellence';
					$shippingCarrierTitle = 'FedEx';
					$shippingMethodTitle = 'Ground - iGlobal';
					break;
				case 'FEDEX_PRIORITY' :
					$shipper = 'excellence_excellence';
					$shippingCarrierTitle = 'FedEx';
					$shippingMethodTitle = 'Priority - iGlobal';
					break;
				case 'UPS_EXPEDITED' :
					$shipper = 'excellence_excellence';
					$shippingCarrierTitle = 'UPS';
					$shippingMethodTitle = 'Expedited - iGlobal';
					break;
				case 'UPS_EXPRESS' :
					$shipper = 'excellence_excellence';
					$shippingCarrierTitle = 'UPS';
					$shippingMethodTitle = 'Express - iGlobal';
					break;
				case 'UPS_EXPRESS_SAVER':
					$shipper = 'excellence_excellence';
					$shippingCarrierTitle = 'UPS';
					$shippingMethodTitle = 'Express Saver - iGlobal';
					break;
				case 'UPS_GROUND':
					$shipper = 'excellence_excellence';
					$shippingCarrierTitle = 'UPS';
					$shippingMethodTitle = 'Ground - iGlobal';
					break;
				case 'UPS_STANDARD' : 
					$shipper = 'excellence_excellence';
					$shippingCarrierTitle = 'UPS';
					$shippingMethodTitle = 'Standard - iGlobal';
					break;
				case 'USPS_FIRST_CLASS_MAIL_INTERNATIONAL' :
					$shipper = 'excellence_excellence';
					$shippingCarrierTitle = 'USPS';
					$shippingMethodTitle = 'First Class Mail, International - iGlobal';
					break;
				case 'USPS_PRIORITY_MAIL_EXPRESS_INTERNATIONAL' :
					$shipper = 'excellence_excellence';
					$shippingCarrierTitle = 'USPS';
					$shippingMethodTitle = 'Priority Mail Express, International - iGlobal';
					break;
				case 'USPS_PRIORITY_MAIL_INTERNATIONAL' :
					$shipper = 'excellence_excellence';
					$shippingCarrierTitle = 'USPS';
					$shippingMethodTitle = 'Priority Mail, International - iGlobal';
					break;
				case  'LANDMARK_LGINTREGU':
				case 'LANDMARK_LGINTSTD':
				case 'LANDMARK_LGINTSTDU':
				case 'MSI_PARCEL':
				case 'MSI_PRIORITY':
					$shipper = 'excellence_excellence';
					$shippingCarrierTitle = 'iGlobal';
					$shippingMethodTitle = 'Landmark';
					break;
				default: 
					$shipper = 'excellence_excellence';
					$shippingCarrierTitle = 'iGlobal';
					$shippingMethodTitle = 'International Shipping';
			}
				
			//Add things to the register so they can be used by the shipping method
			Mage::register('shipping_cost', $shippingRate); 
			Mage::register('shipping_carriertitle', $shippingCarrierTitle); 
			Mage::register('shipping_methodtitle', $shippingMethodTitle);

			//set shipping info
			$billingAddress = $quote->getBillingAddress()->addData($billingAddressData);
			$shippingAddress = $quote->getShippingAddress()->addData($addressData);
			$shippingAddress->setCollectShippingRates(true)->collectShippingRates()
							->setShippingMethod('excellence_excellence')
							->setPaymentMethod('iGlobalCreditCard');
				
			  
			//updates payment type in Magento Admin area
			$paymentMethod = $data ['paymentProcessing'] ['paymentGateway'];
			if($paymentMethod === 'iGlobal_CC'){
				$paymentType = 'iGlobalCreditCard';
			} else if ($paymentMethod === 'iGlobal PayPal') {
				$paymentType = 'iGlobalPaypal';
			} else {
				$paymentType = 'iGlobal';
			};	

			$quote->getPayment()->importData(array('method' => $paymentType));

			$quote->collectTotals()->save();
			$quote->setIsActive(0)->save();

			$service = Mage::getModel('stores/service_quote', $quote);
			$service->submitAll();
			$order = $service->getOrder();
			 

			// cleaning up
			Mage::getSingleton('checkout/session')->clear();

			$id = $order->getEntityId();

			Mage::getSingleton('checkout/session')->setLastOrderId($order->getId());
			Mage::getSingleton('checkout/session')->setLastRealOrderId($order->getIncrementId());

			//Save Order Invoice as paid
			$commentMessage = 'Order automatically imported from iGlobal order ID: '. $_order;
			 
			try {
				$order = Mage::getModel("sales/order")->load($id);
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
			} catch (Exception $e) {
				$order->addStatusHistoryComment('iGlobal Invoicer: Exception occurred during automatically invoicing. Exception message: '.$e->getMessage(), false);
				$order->save();
			}

			if ($data['testOrder'] == 'true') {
				//Set the international_order flag and the ig_order_number on the order and mark as a test order
				$query = "UPDATE `" . $tableName . "` SET `international_order` = 1, `ig_order_number` = '{$_order}', `iglobal_test_order` = 1 WHERE `entity_id` = '{$id}'";
				Mage::getSingleton('core/resource')->getConnection('core_write')->query($query);
			} else {
				//Set the international_order flag and the ig_order_number on the order
				$query = "UPDATE `" . $tableName . "` SET `international_order` = 1, `ig_order_number` = '{$_order}' WHERE `entity_id` = '{$id}'";
				Mage::getSingleton('core/resource')->getConnection('core_write')->query($query);
			}

			//Send the magento id to iGlobal
			$rest->sendMagentoOrderId($_order, $id);

        }
        catch(Exception $e)
        {
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
