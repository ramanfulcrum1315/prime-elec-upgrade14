<?php

class MW_NavigationMenu_Helper_Configuration extends Mage_Core_Helper_Abstract
{
	public function getActive(){
		return Mage::getStoreConfig('navigationmenu/general/active');
	}
	public function getConfiguration(){
		$config= array();
		if ($this->getActive()==1){
			$config["top_possition"] = Mage::getStoreConfig('navigationmenu/general/top_possition');
			if ($config["top_possition"]==1){
				$config["top_animation"] = Mage::getStoreConfig('navigationmenu/general/top_animation');
				$config["top_animation_speed"] = Mage::getStoreConfig('navigationmenu/general/top_animation_speed');
			}
			$config["left_possition"] = Mage::getStoreConfig('navigationmenu/general/left_possition');
			if ($config["left_possition"]==1){
				$config["left_animation"] = Mage::getStoreConfig('navigationmenu/general/left_animation');
				$config["left_animation_speed"] = Mage::getStoreConfig('navigationmenu/general/left_animation_speed');
			}
			$config["number_category"] = Mage::getStoreConfig('navigationmenu/general/number_category');
			$config["view_more_category"] = Mage::getStoreConfig('navigationmenu/general/view_more_category');
			if (($config["number_category"]==0)||(is_null($config["number_category"]))){
				$config["number_category"]=9999;
				$config["view_more_category"]=0;
			}
			$config["number_product"] = Mage::getStoreConfig('navigationmenu/general/number_product');
			$config["view_more_product"] = Mage::getStoreConfig('navigationmenu/general/view_more_product');
			if (($config["number_product"]==0)||(is_null($config["number_product"]))){
				$config["number_product"]=9999;
				$config["view_more_product"]=0;
			}
			$config["truncate_description"] = Mage::getStoreConfig('navigationmenu/general/truncate_description');
		}
		return $config;
	}
}