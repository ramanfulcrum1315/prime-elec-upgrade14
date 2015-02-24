<?php

class OCM_Callouts_Block_Catalog_Category_List extends Mage_Core_Block_Template
{

    protected $_collection;
    public $page_size = null;


    protected function _construct()
    {
        parent::_construct();
        $this->setName('cms_category_callout');
        $this->setTemplate('ocm/callouts/catalog/category/list.phtml');
    }

    public function hasItems() {
        return $this->getItemCollection()->count();
    }
    
    public function getItemCollection() {
        if(!$this->_collection) {

            $collection = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('thumbnail')
                ->addUrlRewriteToResult()
                ->addAttributeToFilter('popular_category',1)
                ->addAttributeToSort('popular_category_position', 'DESC')
            ;
            if($this->page_size) {
                $collection->setPageSize($this->page_size);
            }
                
            $this->_collection = $collection;
        }
        
        return $this->_collection;
        
    }

    public function getThumbnailUrl($category)
    {
        $url = false;
        if ($category->getThumbnail()) {
            $url = Mage::getBaseUrl().'media/catalog/category/'.$category->getThumbnail();
        } else {
            $url = Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/small_image.jpg');
        }
        return $url;
    }


}