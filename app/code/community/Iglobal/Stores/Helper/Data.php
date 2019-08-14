<?php

class Iglobal_Stores_Helper_Data extends Mage_Core_Helper_Abstract
{
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

    public function units2lbs($value, $unit='lbs')
    {
        $convert = array('kg' => 0.453592, 'oz' => 16, 'g' => 453.592, 'lbs' => 1, '' => 1);
        $value = round(floatval($value) / $convert[$unit], 2);
        if($value){
            return $value;
        }
        return null;
    }

    public function dim2inch($value, $dim='in')
    {
        $convert = array('cm' => 2.54, 'in' => 1, '' => 1);
        $value = ceil(floatval($value) / $convert[$dim]);
        if($value){
            return $value;
        }
        return null;
    }

    public function getDimensions($product, $item)
    {
        $dim = $product->getIgDemesionUnits();
        $weight = $product->getIgWeight();
        if (empty($weight))
        {
            $weight = $item->getWeight();
        }
        $dimensions = array(
            'weight' => $this->units2lbs($weight, $product->getIgWeightUnits()),
            'length' => $this->dim2inch($product->getIgLength(), $dim),
            'width'  => $this->dim2inch($product->getIgWidth(), $dim),
            'height' => $this->dim2inch($product->getIgHeight(), $dim),
        );
        return $dimensions;
    }

    public function getItemDetails($item)
    {
        $product = $item->getProduct();

        //get options on the items
        $options = $product->getTypeInstance(true)->getOrderOptions($product);
        $optionList = "";
        if ($options && isset($options["options"])) {
            $optionList = "|";
            foreach ($options["options"] as $option) { //todo: add loops for $options[additional_options] and $options[attributes_info] to get all possible options
                $optionList = $optionList . $option["label"] . ':"' . $option["value"] . '"|';
            }
        }

        return array(
            'productId'   => $product->getId(),
            'sku'         => $product->getSku(),
            'description' => $product->getName(),
            'unitPrice'   => $item->getPrice(),
            'quantity'    => $item->getQty(),
            'itemURL' => $product->getProductUrl(),
            'imageURL' => str_replace("http:", "https:", Mage::helper('catalog/image')->init($product, 'thumbnail')),
            'itemDescriptionLong' => $optionList,
        ) + $this->getDimensions($product, $item);

    }

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
