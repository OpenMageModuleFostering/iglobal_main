<?php

class Iglobal_Stores_Model_ConfigOptions extends Mage_Eav_Model_Entity_Attribute_Source_Store
{
    public function toOptionArray()
    {
        $collection = Mage::getResourceModel('catalog/product_attribute_collection')->load();
        $options = array(array('value'=> null, 'label'=> '- Not mapped -' ));
        foreach($collection as $attr) {
            if($attr->getIsVisible()) {
                $options[] = array('value' => $attr->getAttributeCode(), 'label' => $attr->getFrontendLabel());
            }
        }
        return $options;
    }

}