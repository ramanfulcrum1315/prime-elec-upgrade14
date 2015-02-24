<?php

class OCM_Callouts_Block_Cms_List_New extends OCM_Callouts_Block_Catalog_Product_List_New_Abstract
{

    public $page_size = 21;


    public function _construct() {
        parent::_construct();
        $this->setName('cms_callout');
        $this->setTemplate('ocm/callouts/catalog/product/list/new.phtml');
    }
    
    public function getLoadedProductCollection() {
        return $this->getItemCollection();
    }

}
