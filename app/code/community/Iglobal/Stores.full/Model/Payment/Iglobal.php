<?php

class Iglobal_Stores_Model_Payment_Iglobal extends Mage_Payment_Model_Method_Abstract
{

    protected $_code  = 'iGlobal';
    protected $_canUseInternal = true;
    protected $_canUseCheckout = false;
    protected $_canUseForMultishipping = false;

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        $info = $this->getInfoInstance();
        if ($data instanceof Varien_Object) {
            $data = $data->getData();
        }
        if (is_array($data)) {
            $info->addData($data);
            if(array_key_exists('cardType', $data)) {
                $info->setCcType($data['cardType']);
            }
            $monthMap = array(
                'JAN'=>1,'FEB'=>2,'MAR'=>3,'APR'=>4,'MAY'=>5,'JUN'=>6,
                'JUL'=>7,'AUG'=>8,'SEP'=>9,'OCT'=>10,'NOV'=>11,'DEC'=>12,
            );
            if(array_key_exists('cardExpMonth', $data) and array_key_exists($data['cardExpMonth'],$monthMap)) {
                $info->setCcExpMonth($monthMap[$data['cardExpMonth']]);
            }
            if(array_key_exists('cardExpYear', $data)) {
                $info->setCcExpYear($data['cardExpYear']);
            }
            if(array_key_exists('lastFour', $data)) {
                $info->setCcLast4($data['lastFour']);
            }
        }
        return $this;
    }

}
