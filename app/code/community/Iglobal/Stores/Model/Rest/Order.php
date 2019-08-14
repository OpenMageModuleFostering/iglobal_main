<?php

class Iglobal_Stores_Model_Rest_Order extends Mage_Core_Model_Abstract
{
	protected $_entryPoint = null;
    
    protected function _construct()
    {	
		//set store ID
		if (Mage::getStoreConfig('iglobal_integration/apireqs/iglobalid')){
			//use custom ID
			$this->_store = Mage::getStoreConfig('iglobal_integration/apireqs/iglobalid');    
		} else {
			//default  store ID
			$this->_store = 3;
		}
		
		//Set API Key
		if (Mage::getStoreConfig('iglobal_integration/apireqs/secret')){
			//use custom key
			$this->_key = Mage::getStoreConfig('iglobal_integration/apireqs/secret');    
		} else {
			//default key
			$this->_key = '';
		}
		
		//set entryPoint
        $this->_entryPoint = 'https://checkout.iglobalstores.com'; //change this to "https://checkout.iglobalstores.com' when you go live
		
    }
    
    protected function getRestClient()
    {
        return new Zend_Rest_Client($this->_entryPoint);
    }
    
    protected function addCredentials(array $data)
    {
        return array_merge($data, array('store'=>$this->_store, 'secret'=>$this->_key));
    }
    
    public function getOrders(array $data)
    {
        $data = array_merge($data, array('operation'=>'orderNumbers'));
	$client = $this->getRestClient();
        $result = $client->restPost('/iglobalstores/services/OrderRestService/v1.06', $this->addCredentials($data))->getBody();
        return json_decode(json_encode((array) simplexml_load_string($result)),1);
    }
    
    public function getAllOrders()
    {
        $data = array('operation'=>'orderNumbers', 'sinceDate'=>'20100101');
	$client = $this->getRestClient();
        $result = $client->restPost('/iglobalstores/services/OrderRestService/v1.06', $this->addCredentials($data))->getBody();
        return json_decode(json_encode((array) simplexml_load_string($result)),1);
    }    
    
    public function getAllOrdersSinceDate($data)
    {
        $data = array('operation'=>'orderNumbers', 'sinceDate'=>$data);
	$client = $this->getRestClient();
        $result = $client->restPost('/iglobalstores/services/OrderRestService/v1.06', $this->addCredentials($data))->getBody();
        return json_decode(json_encode((array) simplexml_load_string($result)),1);
    }    
    
    public function getOrder($order)
    {
        $data = array('operation'=>'orderDetail', 'orderId' => $order);
        $client = $this->getRestClient();
        $result = $client->restPost('iglobalstores/services/OrderRestService/v1.06', $this->addCredentials($data))->getBody();

        $result = mb_convert_encoding($result, "HTML-ENTITIES", "UTF-8");
        return json_decode(json_encode((array) simplexml_load_string($result)),1);
    }

    public function sendMagentoOrderId($igc_order_id, $magento_order_id) {
        $data = array('operation'=>'updateMerchantOrderId', 'orderId' => $igc_order_id, 'merchantOrderId' => $magento_order_id);
        $client = $this->getRestClient();
        $client->restPost('iglobalstores/services/OrderRestService', $this->addCredentials($data))->getBody();
    }
    
}
?>
