<?php
class OCM_Brands_Block_Page_Html_Menuleft extends OCM_Brands_Block_Brands
{

    public function getBrandsMenuItems() {
		$collection = Mage::getModel('brands/brands')->getCollection()
            ->addFieldToFilter('status','1')
            ->addFieldToFilter('show_in_menu','1')
            ->setOrder('menu_position', 'ASC')
		;
        return $collection;
    }

}