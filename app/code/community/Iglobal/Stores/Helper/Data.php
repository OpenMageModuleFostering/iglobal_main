<?php

class Iglobal_Stores_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected static $_i = 1;
    
    public function showHiddenProductFields($item)
    {
        $sku = $item->getProduct()->getTypeId() == 'bundle' ? substr($item->getSku(), strpos($item->getSku(), '-')+1) : $item->getSku();
        $id = $item->getProductId();
        $price = $item->getPrice();
        // This is because Magento fails to load the custom attributes on this php template, hence we have to go back to the DB.
        $weight = "";
        $length = "";
        $width = "";
        $height = "";

        try {

            $allItemData = Mage::getModel('catalog/product')->load($item['product_id']);
            try {
                $weightUnits = "";
                if (!empty($allItemData['ig_weight_units'])) {
                    $weightUnits = $allItemData->getAttributeText('ig_weight_units');
                }
            } catch (Exception $e) {
                $weightUnits = "";
            }
            try {
                $weight = "";
                if (!empty($allItemData['ig_weight'])) {
                    $weight = $allItemData->getData('ig_weight');
                }
                if (!empty($weight)) {
                    if ($weightUnits=="kg") {
                        $weight = round(floatval($weight) / 0.453592, 2);
                    } else if ($weightUnits=="oz") {
                        $weight = round(floatval($weight) / 16, 2);
                    } else if ($weightUnits=="g") {
                        $weight = round(floatval($weight) / 453.592, 2);
                    } else {//Default is lbs
                        $weight = round(floatval($weight), 2);
                    }
                } else {
                    $weight = "";
                }
            } catch(Exception $e) {
                $weight = "";
            }
            try {
                $dimUnits = "";
                if (!empty($allItemData['ig_dimension_units'])) {
                    $dimUnits = $allItemData->getAttributeText('ig_dimension_units');
                }
            } catch (Exception $e) {
                $dimUnits = "";
            }
            try {
                $length = "";
                if (!empty($allItemData['ig_length'])) {
                    $length = $allItemData->getData('ig_length');
                }
                $width = "";
                if (!empty($allItemData['ig_width'])) {
                    $width = $allItemData->getData('ig_width');
                }
                $height = "";
                if (!empty($allItemData['ig_height'])) {
                    $height = $allItemData->getData('ig_height');
                }
                if (!empty($length) && !empty($width) && !empty($height)) {
                    if ($dimUnits=="cm") {
                        $length = ceil(floatval($length) / 2.54);
                        $width = ceil(floatval($width) / 2.54);
                        $height = ceil(floatval($height) / 2.54);
                    } else {//Default is inches
                        $length = ceil(floatval($length));
                        $width = ceil(floatval($width));
                        $height = ceil(floatval($height));
                    }
                } else {
                    $length = "";
                    $width = "";
                    $height = "";
                }
            } catch(Exception $e) {
                $length = "";
                $width = "";
                $height = "";
            }
        } catch (Exception $outerE) {

        }

        echo "<div class='ig_itemAttributes' style='display:none;'>";
        echo "<span class='ig_itemProductId'>".$id."</span>";
        echo "<span class='ig_itemSku'>".$sku."</span>";
        echo "<span class='ig_itemPrice'>".$price."</span>";
        echo "<span class='ig_itemWeight'>".$weight."</span>";
        echo "<span class='ig_itemLength'>".$length."</span>";
        echo "<span class='ig_itemWidth'>".$width."</span>";
        echo "<span class='ig_itemHeight'>".$height."</span>";
        echo "</div>";

        self::$_i++;
    }

	//stuff for jquery observer
	/**
     * Path for config.
     */
    const XML_CONFIG_PATH = 'iglobal_integration/igjq/';

    /**
     * Name library directory.
     */
    const NAME_DIR_JS = 'iGlobal/jquery/';

    /**
     * List files for include.
     *
     * @var array
     */
    protected $_files = array(
        'jquery.js',
        'jquery.noconflict.js',
    );

    /**
     * Check enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->_getConfigValue('enabled', $store = '');
    }

    /**
     * Return path file.
     *
     * @param $file
     *
     * @return string
     */
    public function getJQueryPath($file)
    {
        return self::NAME_DIR_JS . $file;
    }

    /**
     * Return list files.
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->_files;
    }

    protected function _getConfigValue($key, $store)
    {
        return Mage::getStoreConfig(self::XML_CONFIG_PATH . $key, $store = '');
    }
	
}
