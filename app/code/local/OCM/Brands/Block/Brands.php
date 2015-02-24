<?php
class OCM_Brands_Block_Brands extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
	public function getBrands()     
     { 
        if (!$this->hasData('brands')) {
            $this->setData('brands', Mage::registry('brands'));
        }
        return $this->getData('brands');
        
    }
	
	public function getBrandsCollection(){
		
		return Mage::getModel('brands/brands')->getCollection();

	}
	public function getAlphabeticalChunkedBrands(){
		
		$collection = Mage::getModel('brands/brands')->getCollection()->addAttributeToSort('title', 'ASC')
		  ->addFieldToFilter('status','1')
		;
		$allBrandNames = array();
		foreach($collection as $brand) {
			if($brand->getStatus() == 1) {
				$brand_name = $brand->getTitle();
				$brand_icon = $brand->getLogo();
				$brand_id = $brand->getAttrValueId();
				$first_letter = (is_numeric($brand_name[0])) ? 'NUM' : strtoupper ($brand_name[0]);
				
				if(!isset($allBrandNames[ $first_letter ])) {
					$allBrandNames[ $first_letter ] = array();
				}
				$allBrandNames[ $first_letter ][ $brand_id ]['name'] = $brand_name;
				$allBrandNames[ $first_letter ][ $brand_id ]['icon'] = $brand_icon;
			}
		}
		return $allBrandNames;
	}
	
	public function getFeaturedBrands(){
		return Mage::getModel('brands/brands')->getCollection()->addFieldToFilter('featured','1');
	}

	public function getBrandsBlock(){
	   
	   //TODO make this setable in config
		return $this->getLayout()->createBlock('cms/block')->setBlockId('ocm-brands-block')->toHtml();
	}

	
	
	public function getDirectiveConversion($content){
        $processor = Mage::getModel('core/email_template_filter');
        $html = $processor->filter($content);
        return $html;
    }
    
	public function isThirdPartSearchEngine() {
		  if(Mage::helper('enterprise_search')->isThirdPartSearchEngine() && Mage::helper('enterprise_search')->getIsEngineAvailableForNavigation()) {
			  return true;
		  } else {
			  return false;
		  }
}

	public function getDefaultImage() {
    	if(!isset($this->defaultImage)) {
    	   $this->defaultImage = $this->getDirectiveConversion(Mage::getStoreConfig('brands_options/data/brand_default_image'));
    	}
    	return  $this->defaultImage;
	}


}