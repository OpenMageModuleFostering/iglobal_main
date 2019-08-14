<?php

class Iglobal_Stores_UpdateShippingStatusController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {

        $reader = Mage::getSingleton('core/resource')->getConnection('core_read');
        $writer = Mage::getSingleton('core/resource')->getConnection('core_write');

        $openOrderQuery = "Select * from `sales_flat_order` where `international_order` = 1 AND `ig_order_number` IS NOT NULL AND `state` <> 'complete' AND `state` <> 'closed' AND `state` <> 'canceled' AND `state` <> 'holded'";
        $openOrders = $reader->fetchAll($openOrderQuery);

        $rest = Mage::getModel('stores/rest_order');

        $ordersShown = 0;
        foreach ($openOrders as $openOrder) {
            echo "<div style='border:solid 1px blue; margin: 15px;'>Processing Order: " . $openOrder['entity_id'] . " | increment id: " . $openOrder['increment_id'] . " | igOrderNumber: " . $openOrder['ig_order_number'] . " | state: " . $openOrder['state'] . " | status: " . $openOrder['status'];

            //Get Order Data
            $data = $rest->getOrder($openOrder['ig_order_number']);
            echo "<br>iGlobal Shipping Status: " . $data['shippingStatus'];

            //If the shipping status is end of day, then complete the order in magento
            if ($data["shippingStatus"] == "VENDOR_END_OF_DAY_COMPLETE") {
                $order = Mage::getModel("sales/order")->load($openOrder['entity_id']);
                try {

                    if ($order->canShip()) {
                        $shipment = $order->prepareShipment();
                        $shipment->register();
                        $order->setIsInProcess(true);
                        $order->addStatusHistoryComment('Automatically SHIPPED by iGlobal.', false);
                        $transactionSave = Mage::getModel('core/resource_transaction')
                            ->addObject($shipment)
                            ->addObject($shipment->getOrder())
                            ->save();

                        if (!empty($data["trackingNumber"])) {
                            //Add the tracking number "track_number"
                            $track = Mage::getModel('sales/order_shipment_track')
                                ->setShipment($shipment)
                                ->setData('title', 'iGlobal Stores')
                                ->setData('number', $data["trackingNumber"])
                                ->setData('carrier_code', 'custom')
                                ->setData('order_id', $shipment->getData('order_id'))
                                ->save();
                        }

                    }
                    echo "<br>Marking Order as Completed";
                } catch (Exception $e) {
                    $order->addStatusHistoryComment('iGlobal Shipper: Exception occurred during automatically shipping. Exception message: '.$e->getMessage(), false);
                    $order->save();
                    echo "<br>An Error Occurred while trying to mark Order as Completed";
                }
            } else {
                echo "<br>Skipping Order";
            }

            echo "</div>";

            $ordersShown++;
        }

        if ($ordersShown < 1) {
            echo "No active iGlobal orders to process";
        }

    }
    
}
