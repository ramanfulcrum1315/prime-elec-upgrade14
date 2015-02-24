<?php

class MW_NavigationMenu_Helper_Menuitems extends Mage_Core_Helper_Abstract
{
	const NM_MENU_ITEM_TYPE1	= 1;
	const NM_MENU_ITEM_TYPE2	= 2;
	
	public function getItemsTypeOption(){
		return array(
				self::NM_MENU_ITEM_TYPE1 => 'Show subcategories only',
				self::NM_MENU_ITEM_TYPE2 => 'Show subcategories and products',
		);
	}
	
	public function getCategoryOption(){
		$collection = Mage::getModel('catalog/category')->getCollection()
		->addAttributeToSelect('is_active')
		->addAttributeToSelect('level')
		->addAttributeToSelect('name');
		$categoryOption[0] = '';
		foreach($collection as $category){
			if ($category->getLevel()>1){
				$categoryOption[$category->getId()] = $category->getName()." (".$category->getId().")";
			}
		}
		return $categoryOption;
	}
}