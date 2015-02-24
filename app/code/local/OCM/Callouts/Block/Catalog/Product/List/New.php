<?php 

class OCM_Callouts_Block_Catalog_Product_List_New extends OCM_Callouts_Block_Catalog_Product_List_New_Abstract
{
    public $page_size = 20;


    public function hasItems() {
        return $this->getItemCollection()->count();
    }
    

}

