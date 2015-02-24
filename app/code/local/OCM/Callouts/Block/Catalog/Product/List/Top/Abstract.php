<?php 

class OCM_Callouts_Block_Catalog_Product_List_Top_Abstract extends Mage_Catalog_Block_Product_Abstract
{

    protected $_collection;
    public $page_size = null;

    public function hasItems() {
        return $this->getItemCollection()->count();
    }
    
    public function getItemCollection() {
        if(!$this->_collection) {

            $collection = Mage::getResourceModel('reports/product_collection')
                ->addOrderedQty()
                ->setOrder('ordered_qty', 'desc');
            if ($this->page_size) {
                $collection->getSelect()->limit($this->page_size);
            }
                        
            $this->_addProductAttributesAndPrices($collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
            $this->_collection = $collection;
        }
        
        return $this->_collection;
        
    }

}
