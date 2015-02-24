<?php

class MW_NavigationMenu_Helper_Contents extends Mage_Core_Helper_Abstract
{
	public function getContentsPositionOption(){
		return array(
				1 => 'Top',
				2 => 'Left',
				3 => 'Right',
				4 => 'Bottom',
		);
	}
	
	public function getStaticBlockOption(){
		$collection = Mage::getModel('cms/block')->getCollection()
		->addfieldToSelect('is_active')
		->addfieldToSelect('block_id')
		->addfieldToSelect('title');
        $blockOption= array();
		$blockOption[0] = "";
		foreach($collection as $block){
			if ($block->getIsActive()==1){
				$blockOption[$block->getBlockId()] = $block->getTitle();
			}
		}
		return $blockOption;
	}
	
	public function getMenuItemOption(){
        $itemOption= array();
		$collection = Mage::getModel('navigationmenu/menuitems')->getCollection()
		->addfieldToSelect('item_id')
		->addfieldToSelect('title')
		->addfieldToSelect('status');
		foreach($collection as $item){
			if ($item->getStatus()==1){
				$itemOption[$item->getItemId()] = $item->getTitle();
			}
		}
		return $itemOption;
	}
	
	public function getContent($menuitem_id,$position)
	{
        $items= array();
		$collections= Mage::getModel("navigationmenu/contents")->getCollection()
					->addFieldToFilter('status', array('eq' => 1))
					->addFieldToFilter('position', array('eq' => $position))
					->addFieldToFilter('menuitem_id', array('eq' => $menuitem_id));
		$a=0;
		foreach($collections as $collection){
			if ($collection->isDisplay()){
				if ($collection->getTitle())
					$items[$a]['title']=$collection->getTitle();
				if ($collection->getText())
					$items[$a]['text']=$collection->getText();
				if (strcmp(trim($collection->getImage()),"")!=0)
					$items[$a]['image']=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$collection->getImage();
				$block= $this->getStaticBlockIdentifier($collection->getBlockId());
				if ($block)
					$items[$a]['block_identifier']= $block;
				if (strcmp(trim($collection->getSku()),"")!=0)
				{
					$product= $this->getProductContent(trim($collection->getSku()));
					if (isset($product))
						$items[$a]['product']= $product;
				}
				$a++;					
			}
		}
		return $items;
	}
	
	
	public function getStaticBlockIdentifier($id)
	{
		$block = Mage::getModel('cms/block')->load($id);
		return $block->getIdentifier();
	}
	
	public function getProductContent($sku)
	{
        $items= array();
		//$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		$product_id = Mage::getModel("catalog/product")->getIdBySku($sku);
		if($product_id){
			$product = Mage::getModel("catalog/product")->load($product_id);
			$items['name']=$product->getData("name");
			$items['product_id'] = $product->getId();
			/* $items['linkimage'] = Mage::helper('catalog/image')->init($product, 'image')->resize(141,167); */
			$items['price']= Mage::helper('core')->currency($product->getFinalPrice(),true,false);
			$items['description']=$this->truncateDescription($product->getData("short_description"));
			$items['link'] = $product->getProductUrl();
			/*
			$IdPath = 'product/'.$product->getId();
			$rewriteUrl = Mage::getModel('core/url_rewrite')->loadByIdPath($IdPath);
			if ($rewriteUrl->getId()) {
				$items['link'] = Mage::getBaseUrl().$rewriteUrl->getRequestPath();
			}else if ($product->getUrlPath()){
				$items['link'] = Mage::getBaseUrl().$product->getUrlPath();
			}else{
				$items['link'] = Mage::getBaseUrl().'catalog/product/view/id/'.$product->getId();
			}*/
		}
		return $items;
	}
	
	public function truncateDescription($str)
	{
		$str= str_replace("<"," <",$str);
		$str= str_replace(">","> ",$str);
		$str= strip_tags($str);
		$str= trim($str);
	
		$number_character=intval(Mage::getStoreConfig('navigationmenu/general/truncate_description'));
		if ((is_null($number_character))||($number_character == 0)){
			return $str;
		}elseif ($number_character < 0){
			$number_character=200;
		}
	
		$str=Mage::helper('core/string')->truncate($str,$number_character,'...',$_remainder,false);
		$_remainder="";
		return $str;
	}
}