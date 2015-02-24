<?php

class OCM_Brands_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getBrandAttrCode() {
        return Mage::getStoreConfig('brands_options/data/brand_attr');
    }
    public function getMetaTitle() {
        return Mage::getStoreConfig('brands_options/data/meta_title');
    }
    public function getMetaKeywords() {
        return Mage::getStoreConfig('brands_options/data/meta_keywords');
    }
    public function getMetaDescription() {
        return Mage::getStoreConfig('brands_options/data/meta_description');
    }
    
    public function isThirdPartSearchEngine() {
        if(Mage::helper('enterprise_search')->isThirdPartSearchEngine() && Mage::helper('enterprise_search')->getIsEngineAvailableForNavigation()) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getBrandUrl($id,$brand) {
        
        $url_model = Mage::getModel('core/url_rewrite')->load($this->getUrlRewritePath($id),'id_path');
        if($url_model->getId()) {
            return Mage::getModel('core/url')->getUrl($url_model->getRequestPath());
        }
        $val = ($this->isThirdPartSearchEngine()) ? $brand : $id;
        return Mage::getModel('core/url')->getUrl($this->getBrandUri($id,$brand));
        
    }
    
    public function getBrandUri($id,$brand) {
                
        $val = ($this->isThirdPartSearchEngine()) ? $brand : $id;
        return 'brands/category/view/'.$this->getBrandAttrCode().'/'.$val.'/';
        
    }
    
    public function getCurrentBrand() {
        return Mage::registry('current_brand');
    }
    
    public function getUrlRewritePath($id){
        return 'ocm/brands/'.$id;
    }

    public function formatUrlKey($str) {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }
    
    public function resizeImage($imagepath,$width,$height=null) {
    
        $parts = explode('/',$imagepath);
        $img_file = end($parts);
        $height = ($height) ? $height:$width;
        $cache_dir = "brands".DS."cache".DS.$width.'x'.$height.DS;
        $image_full_path = Mage::getBaseDir('media').DS.$imagepath;
        $image_cached = Mage::getBaseDir('media').DS.$cache_dir.$img_file;
         
        if (!file_exists($image_cached)&&file_exists($image_full_path)) {
            $imageObj = new Varien_Image($image_full_path);
            //$imageObj->constrainOnly(TRUE);
            $imageObj->keepAspectRatio(TRUE);
            $imageObj->keepFrame(TRUE);
            $imageObj->backgroundColor(array(255, 255, 255));
            $imageObj->resize($width,$height);
            $imageObj->save($image_cached);
        }
        
        return Mage::getBaseUrl('media').$cache_dir.$img_file;   
    }

}