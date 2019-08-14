<?php

class Iglobal_Stores_Model_ConfigOptions
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('stores')->__('Hello')),
            array('value'=>2, 'label'=>Mage::helper('stores')->__('Goodbye')),
            array('value'=>3, 'label'=>Mage::helper('stores')->__('Yes')),            
            array('value'=>4, 'label'=>Mage::helper('stores')->__('No')),                       
        );
    }

}