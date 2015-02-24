<?php

require_once 'Mage/Catalog/controllers/CategoryController.php';
class OCM_Brands_CategoryController extends Mage_Catalog_CategoryController
{

    public function viewAction()
    {
        $attr_code = $this->getHelper()->getBrandAttrCode();		
        $att_id = $this->getRequest()->getParam($attr_code);	
		$attribute_to_load = ($this->getHelper()->isThirdPartSearchEngine()) ? 'title':'attr_value_id';
		
		$brand = Mage::getModel('brands/brands')->loadByAttribute($attribute_to_load,$att_id);
				
		if ($brand && $brand->getStatus() == 1) {

            Mage::register('current_brand' , $brand);
		 
    		$root_cat_id = Mage::app()->getStore()->getRootCategoryId(); 
    		$category = Mage::getModel('catalog/category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($root_cat_id);
                
            $category->setName($brand->getTitle());
            Mage::register('current_category' , $category);
            
    		$this->loadLayout();
    		$this->getLayout()->getBlock('head')
    		  ->setTitle($this->__($brand->getTitle()))
    		  ->setKeywords($brand->getMetaKeywords())
    		  ->setDescription($brand->getMetaDescription())
    		  ->removeItem('link_rel', $category->getUrl())
    		  ->addLinkRel('canonical', $this->getHelper()->getBrandUrl($brand->getAttrValueId(),$brand->getTitle()))
    		;
            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');
    		$this->renderLayout();
    		
        } elseif (!$this->getResponse()->isRedirect()) {
            $this->_forward('noRoute');
        }
    }
	public function getHelper() {
    	return Mage::helper('brands');
	}
	
}


//'canonical', $category->getUrl()
