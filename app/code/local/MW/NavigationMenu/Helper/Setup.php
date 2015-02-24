<?php

class MW_NavigationMenu_Helper_Setup extends Mage_Core_Helper_Abstract
{
	public function getMenuItemsDefault()
	{
		$model_categories_1= Mage::getModel('catalog/category')->getCollection()
		->addFieldToFilter('level', array('eq' => 1));
		$count=0;
		foreach($model_categories_1 as $model_categorie_1){
			$category= Mage::getModel('catalog/category')->load($model_categorie_1->getId());
			if ($category->getIsActive()){
				$categories2= $category->getChildrenCategories();
				foreach($categories2 as $category2){
					$item[$count]['id']= $category2->getId();
					$item[$count]['name']= $category2->getName();
					$item[$count]['status']= $category2->getIsActive();
					$item[$count]['store_ids']= $this->getStringStoreIds($category2);
					$count++;
				}
			}
		}
		
		
		return $item;
	}
	
	public function getStringStoreIds($category){
		$storeIds= $this->getStoreIdsOfCategory($category);
		$str='';
		foreach ($storeIds as $storeId){
			$str=$str.$storeId.',';
		}
		$a=strlen($str);
		$str=substr($str, 0, $a-1);
		return $str;
	}
	
	public function getStoreIdsOfCategory($category){
		$nodes = array();
		foreach ($category->getPathIds() as $id) {
			$nodes[] = $id;
		}
		$storeIds = array();
		$storeCollection = Mage::getModel('core/store')->getCollection()->loadByCategoryIds($nodes);
		foreach ($storeCollection as $store) {
			$storeIds[$store->getId()] = $store->getId();
		}
		return $storeIds;
	}
}
?>