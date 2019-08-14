<?php

class Iglobal_Stores_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup
{
    /**
     * Create an attribute.
     *
     * For reference, see Mage_Adminhtml_Catalog_Product_AttributeController::saveAction().
     *
     * @return int|false
     */
    function createAttribute($labelText, $attributeCode, $inputType, $option = -1, $values = -1, $productTypes = -1)
    {
        $setInfo = array('SetID'=>'4', 'GroupID'=>'7');
        $labelText = trim($labelText);
        $attributeCode = trim($attributeCode);

        if($labelText == '' || $attributeCode == '')
        {
            //$this->logError("Can't import the attribute with an empty label or code.  LABEL= [$labelText]  CODE= [$attributeCode]");
            return false;
        }

        if($values === -1)
            $values = array();

        if($productTypes === -1)
            $productTypes = array();

        if($setInfo !== -1 && (isset($setInfo['SetID']) == false || isset($setInfo['GroupID']) == false))
        {
            //$this->logError("Please provide both the set-ID and the group-ID of the attribute-set if you'd like to subscribe to one.");
            return false;
        }

        //$this->logInfo("Creating attribute [$labelText] with code [$attributeCode].");

        //>>>> Build the data structure that will define the attribute. See
        //     Mage_Adminhtml_Catalog_Product_AttributeController::saveAction().

        if($inputType === 'text'){
            $data = array(
                'is_global'                     => '1',
                'frontend_input'                => 'text',
                'default_value_text'            => '',
                'default_value_yesno'           => '0',
                'default_value_date'            => '',
                'default_value_textarea'        => '',
                'is_unique'                     => '0',
                'is_required'                   => '0',
                'frontend_class'                => 'validate-number',
                'is_searchable'                 => '0',
                'is_visible_in_advanced_search' => '0',
                'is_comparable'                 => '0',
                'is_used_for_promo_rules'       => '0',
                'is_html_allowed_on_front'      => '1',
                'is_visible_on_front'           => '0',
                'used_in_product_listing'       => '0',
                'used_for_sort_by'              => '0',
                'is_configurable'               => '0',
                'is_filterable'                 => '0',
                'is_filterable_in_search'       => '0',
                'backend_type'                  => 'varchar',
                'default_value'                 => '',
            );
        }
        elseif($inputType === 'select'){
            $data = array(
                'is_global'                     => '1',
                'frontend_input'                => 'select',
                'default_value_text'            => '',
                'default_value_yesno'           => '0',
                'default_value_date'            => '',
                'default_value_textarea'        => '',
                'is_unique'                     => '0',
                'is_required'                   => '1',
                'frontend_class'                => '',
                'is_searchable'                 => '0',
                'is_visible_in_advanced_search' => '0',
                'is_comparable'                 => '0',
                'is_used_for_promo_rules'       => '0',
                'is_html_allowed_on_front'      => '1',
                'is_visible_on_front'           => '0',
                'used_in_product_listing'       => '0',
                'used_for_sort_by'              => '0',
                'is_configurable'               => '0',
                'is_filterable'                 => '0',
                'is_filterable_in_search'       => '0',
                'backend_type'                  => 'int',
                'default_value'                 => '',
            );
        }
        // Now, overlay the incoming values on to the defaults.
        foreach($values as $key => $newValue)
            if(isset($data[$key]) == false)
            {
                //$this->logError("Attribute feature [$key] is not valid.");
                return false;
            }

            else
                $data[$key] = $newValue;

        // Valid product types: simple, grouped, configurable, virtual, bundle, downloadable, giftcard
        $data['apply_to']       = $productTypes;
        $data['attribute_code'] = $attributeCode;
        $data['frontend_label'] = array(
            0 => $labelText,
            1 => '',
            3 => '',
            2 => '',
            4 => '',
        );

        //<<<<

        //>>>> Build the model.

        $model = Mage::getModel('catalog/resource_eav_attribute');

        $model->addData($data);

        if($setInfo !== -1)
        {
            $model->setAttributeSetId($setInfo['SetID']);
            $model->setAttributeGroupId($setInfo['GroupID']);
        }

        $entityTypeID = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
        $model->setEntityTypeId($entityTypeID);

        $model->setIsUserDefined(1);

        //<<<<

        // Save.

        try
        {
            $model->save();
        }
        catch(Exception $ex)
        {
           // $this->logError("Attribute [$labelText] could not be saved: " . $ex->getMessage());
            return false;
        }
        $id = $model->getId();

        if($inputType=='select' && $option !== -1){
            $option['attribute_id'] = $id;
            $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
            $setup->addAttributeOption($option);
        }


        //$this->logInfo("Attribute [$labelText] has been saved as ID ($id).");

        return $id;
    }
}

?>