<?php
class MW_NavigationMenu_Block_Topmenu extends Mage_Core_Block_Template {
	public function _prepareLayout() {
		return parent::_prepareLayout ();
	}
	
	public function checkStoreView() {
		$str_storeview_active = Mage::getStoreConfig ( 'navigationmenu/general/storeview_active' );
		if ((is_null ( $str_storeview_active )) || ($str_storeview_active == ""))
			return false;
		$storeIds = explode ( ',', $str_storeview_active );
		$id = Mage::app ()->getStore ()->getId ();
		foreach ( $storeIds as $storeId ) {
			if ($storeId == 0)
				return true;
			if ($storeId == $id) {
				return true;
			}
		}
		return false;
	}
	
	public function checkVersion17($str) {
		
		$a = explode ( '.', $str );
		$modules = array_keys ( ( array ) Mage::getConfig ()->getNode ( 'modules' )->children () );
		if (in_array ( 'Enterprise_Banner', $modules )) {
			if ($a [1] >= '12') {
				return true;
			}
		} elseif (in_array ( 'Enterprise_Enterprise', $modules )) {
			if ($a [1] <= '10') {
				return false;
			}
		} else {
			if ($a [1] >= '7') {
				return true;
			} else
				return false;
		}
	}
	
	public function getMenuItems() {
		$menu_colections = Mage::getModel ( 'navigationmenu/menuitems' )->getCollection ()->addOrder ( "main_table.`order`", 'ASC' );
		
		$a = 0;
		$items = array ();
		foreach ( $menu_colections as $menu_colection ) {
			if ($menu_colection->isDisplay ()) {
				if ($menu_colection->getCategoryId () != 0) {
					$category = Mage::getModel ( 'catalog/category' )->load ( $menu_colection->getCategoryId () );
					if ($category->getIsActive ()) {
						$isChoose = new Mage_Catalog_Block_Navigation ();
						if ($isChoose->isCategoryActive ( $category )) {
							$items [$a] ['active'] = 'active';
						}
						$items [$a] ['category2'] = $this->getChildrenCategory ( $category->getChildrenCategories (), $menu_colection->getType () );
						if (! $items [$a] ['category2']) {
							$items [$a] ['products'] = $this->getProductContent ( $category->getProductCollection (), explode ( ',', $category->getAllChildren () ) );
						}
						if (strcmp ( trim ( $menu_colection->getUrl () ), "" ) == 0) {
							$IdPath = 'category/' . $category->getId ();
							$rewriteUrl = Mage::getModel ( 'core/url_rewrite' )->loadByIdPath ( $IdPath );
							if ($rewriteUrl->getId ()) {
								$items [$a] ['url'] = Mage::getBaseUrl () . $rewriteUrl->getRequestPath ();
							} else
								$items [$a] ['url'] = $category->getUrl ();
						} else
							$items [$a] ['url'] = $menu_colection->getUrl ();
						$items [$a] ['name'] = $menu_colection->getTitle ();
						$items [$a] ['type'] = intval ( $menu_colection->getType () );
						$items [$a] ['column'] = intval ( $menu_colection->getColumn () );
						$items [$a] ['menuitem_id'] = intval ( $menu_colection->getItemId () );
						$items [$a] ['exist_position'] = $this->checkContentsPosition ( $items [$a] ['menuitem_id'] );
						$a ++;
					}
				} else {
					$items [$a] ['name'] = $menu_colection->getTitle ();
					$items [$a] ['type'] = intval ( $menu_colection->getType () );
					$items [$a] ['column'] = intval ( $menu_colection->getColumn () );
					$items [$a] ['menuitem_id'] = intval ( $menu_colection->getItemId () );
					$items [$a] ['exist_position'] = $this->checkContentsPosition ( $items [$a] ['menuitem_id'] );
					$items [$a] ['url'] = $menu_colection->getUrl ();
					$items [$a] ['is_link'] = 1;
					$a ++;
				}
			}
		}
		return $items;
	}
	
	public function checkContentsPosition($menuitem_id) {
		$array = array ();
		$contents = Mage::getModel ( 'navigationmenu/contents' )->getCollection ()
				->addFieldToFilter ( 'status', array ('eq' => 1 ) )
				->addFieldToFilter ( 'menuitem_id', array ('eq' => $menuitem_id ) );
		foreach ( $contents as $content ) {
			$array [$content->getPosition ()] = 1;
		}
		return $array;
	}
	
	public function getChildrenCategory($objs, $showtype) {
		$a = 0;
		$items = array ();
		foreach ( $objs as $obj ) {
			if ($obj->getIsActive () == 1) {
				$items [$a] ["name"] = $obj->getName ();
				
				$IdPath = 'category/' . $obj->getId ();
				$rewriteUrl = Mage::getModel ( 'core/url_rewrite' )->loadByIdPath ( $IdPath );
				if ($rewriteUrl->getId ()) {
					$items [$a] ["url"] = Mage::getBaseUrl () . $rewriteUrl->getRequestPath ();
				} else
					$items [$a] ["url"] = $obj->getUrl ();
				
				$isChoose = new Mage_Catalog_Block_Navigation ();
				if ($isChoose->isCategoryActive ( $obj )) {
					$items [$a] ['active'] = 'active';
				}
				$category = Mage::getModel ( 'catalog/category' )->load ( $obj->getId () );
				if ($showtype == 1) {
					$items [$a] ['category3'] = $this->getChildrenCategory2 ( $category->getChildrenCategories (), $obj->getId () );
				} elseif ($showtype == 2) {
					$items [$a] ['products'] = $this->getProductContent ( $obj->getProductCollection (), explode ( ',', $category->getAllChildren () ) );
				}
				$a ++;
			}
		}
		return $items;
	}
	
	public function getChildrenCategory2($objs, $category_id) {
		$a = 0;
		$items = array ();
		foreach ( $objs as $obj ) {
			if ($obj->getIsActive () == 1) {
				$items [$a] ["name"] = $obj->getName ();
				
				$IdPath = 'category' . $obj->getId ();
				$rewriteUrl = Mage::getModel ( 'core/url_rewrite' )->loadByIdPath ( $IdPath );
				if ($rewriteUrl->getId ()) {
					$items [$a] ["url"] = Mage::getBaseUrl () . $rewriteUrl->getRequestPath ();
					var_dump ( $items [$a] ["url"] );
				} else
					$items [$a] ["url"] = $obj->getUrl ();
				
				$isChoose = new Mage_Catalog_Block_Navigation ();
				if ($isChoose->isCategoryActive ( $obj )) {
					$items [$a] ['active'] = 'active';
				}
				$a ++;
			}
		}
		return $items;
	}
	
	public function getProductContent($objs, $categoryIds) {
		$a = 0;
		$items = array ();
		$CurrentStoreId = Mage::app ()->getStore ()->getStoreId ();
		foreach ( $objs as $obj ) {
			$model_product = Mage::getModel ( 'catalog/product' );
			$model_product->load ( $obj->getId () );
			if ($this->isDisplay ( $model_product )) {
				$items [$a] ['name'] = $model_product->getData ( "name" );
				
				$ParentCategoryIds = $model_product->getCategoryIds ();
				foreach ( $ParentCategoryIds as $ParentCategoryId ) {
					if (in_array ( $ParentCategoryId, $categoryIds ))
						$category_id = $ParentCategoryId;
				}
				
				$IdPath = 'product/' . $obj->getId () . '/' . $category_id;
				$rewriteUrl = Mage::getModel ( 'core/url_rewrite' )->loadByIdPath ( $IdPath );
				if ($rewriteUrl->getId ()) {
					$items [$a] ['link'] = Mage::getBaseUrl () . $rewriteUrl->getRequestPath ();
				} else
					$items [$a] ['link'] = $model_product->getProductUrl ();
				$items [$a] ['link'] = Mage::getBaseUrl () . $rewriteUrl->getRequestPath ();
				$a ++;
			}
		}
		return $items;
	}
	
	public function isDisplay($model_product) {
		$status = $model_product->getStatus ();
		$visible = $model_product->getVisibility ();
		$storeIds = $model_product->getStoreIds ();
		if (($status) & (($visible == 2) || ($visible == 4))) {
			$id = Mage::app ()->getStore ()->getId ();
			foreach ( $storeIds as $storeId ) {
				if ($storeId == 0)
					return true;
				if ($storeId == $id) {
					return true;
				}
			}
		}
		return false;
	}

}