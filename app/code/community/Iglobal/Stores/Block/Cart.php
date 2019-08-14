<?php
class Iglobal_Stores_Block_Cart extends Mage_Core_Block_Template
{
    
   protected function _construct()
   {
    parent::_construct();
    $this->setTemplate('iglobal/stores/cart.phtml');
   }

   public function igAttrBlock()
   {
      $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
Zend_Debug::dump($items);
      echo '<div class="igItemAttr" style="display: none;">';
      foreach($items as $item) {
     
         $image = Mage::helper('catalog/image')->init($item->getProduct(), 'thumbnail');
         $my_product = Mage::getModel('catalog/product')->load($item->getProductId()); 
         $my_product_url = $my_product->getProductUrl();

        echo '<div class="igItemDetails">':
	 echo '<p class="igID">'.$item->getProductId().'</p><br />';
        echo '<p class="igName">'.$item->getName().'</p><br />';
        echo '<p class="igSku">'.$item->getSku().'</p><br />';
        echo '<p class="igQty">'.$item->getQty().'</p><br />';
        echo '<p class="igPrice">'.$item->getPrice().'</p><br />';
       
        echo '<p class="igUrl">'.$my_product_url.'</p><br />';
        echo '<p class="igImage">I'. $image.'</p><br />';
        echo '<p class="igDescription">'. $item->getdescription.'</p><br />';
        echo '<p class="igWeight">'.$item->getWeight().'</p><br />';
        echo '<p class="igLength">'. $item->getLength.'</p><br />';
        echo '<p class="igHeight">'. $item->getHeight.'</p><br />';
	echo '</div><!-- end igItemDetails-->';

        echo "<br />";
      }
   echo '</div><!--end igItemAttr-->';

      //return $items; 
   }


}