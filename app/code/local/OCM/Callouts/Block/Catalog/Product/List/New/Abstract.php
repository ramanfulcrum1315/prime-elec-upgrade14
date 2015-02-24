<?php 

class OCM_Callouts_Block_Catalog_Product_List_New_Abstract extends 
Mage_Catalog_Block_Product_Abstract
//Enterprise_TargetRule_Block_Catalog_Product_List_Related
{

    protected $_collection;
    public $page_size = null;

    public function hasItems() {
        return $this->getItemCollection()->count();
    }
    
    public function getItemCollection() {
        if(!$this->_collection) {

            $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('news_from_date', array(
                    'to' => date('Y-m-d')
                    ))
                ->addAttributeToFilter('news_to_date', array(
                    'from' => date('Y-m-d')
                    ))
                ;
            if($this->page_size) {
                $collection->setPageSize($this->page_size);
            }
                
            $this->_addProductAttributesAndPrices($collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

            $this->_collection = $collection;
        }
        
        return $this->_collection;
        
    }

}
