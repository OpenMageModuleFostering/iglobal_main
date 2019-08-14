<?php

class Iglobal_Stores_Model_Rest_Region extends Mage_Core_Model_Abstract
{
    protected $_entryPoint = null;

    protected function _construct()
    {


        //Set Service Key
        $this -> _key = '{31ae7155-5b9e-461f-8353-9a8c3f8ae35974ffec3a-88dc-4acb-8420-270df7967338}';

        //set entryPoint
        $this->_entryPoint = 'https://api.iglobalstores.com/v1/magento-region';

    }

    protected function getRestClient()
    {
        return new Zend_Http_Client($this->_entryPoint);
    }

    protected function addCredentials(array $data)
    {
        return array_merge($data, array('serviceToken'=>$this->_key));
    }

    public function getRegionId($countryId, $region, $order)
    {
        $data = array('countryCode'=> $countryId,'region'=> $region, 'orderId' => $order);
        $client = $this->getRestClient();
        $client->setRawData(json_encode($data), 'application/json')->setHeaders("serviceToken:31ae7155-5b9e-461f-8353-9a8c3f8ae35974ffec3a-88dc-4acb-8420-270df7967338")->request('POST');
        $result = $client->request()->getBody();
        $resArray = json_decode($result, true);
        //Mage::log("The region rest result for order {$order}:"  . Zend_Debug::dump($result), 'mattscustom.log', true);
        //Mage::log("The region rest result for order {$order}:" . print_r($result, true), null, 'mattscustom.log', true);
        return $resArray;
    }

}
?>