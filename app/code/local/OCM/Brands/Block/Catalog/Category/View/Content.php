<?php 

class OCM_Brands_Block_Catalog_Category_View_Content extends Mage_Core_Block_Template
{

	public function getBrandContent(){
		$brand = $this->getBrand();
		if ($brand->getBrandContent() && $brand->getStatus() == 1) {
			return Mage::helper('cms')->getBlockTemplateProcessor()->filter($brand->getBrandContent());
		} else {
			return false;
		}
		
	}
	
	public function getBrand() {
    	return Mage::registry('current_brand');
	}
}