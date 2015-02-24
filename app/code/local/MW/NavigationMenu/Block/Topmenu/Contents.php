<?php
class MW_NavigationMenu_Block_Topmenu_Contents extends Mage_Core_Block_Template
{
	protected $_priceDisplayType = null;
    protected $_idSuffix = '';
	protected $_finalPrice = array();
	
	public $_product_new = '';
	
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}
	
	 public function getPriceHtml($product)
    {
		$_product = Mage::getModel("catalog/product")->getCollection()
						->addAttributeToSelect(Mage::getSingleton("catalog/config")->getProductAttributes())
						->addAttributeToFilter("entity_id", $product->getId())
						->setPage(1, 1)
						->addMinimalPrice()
						->addFinalPrice()
						->addTaxPercents()
						->load()
						->getFirstItem();
						
		return $this->getLayout()->createBlock('navigationmenu/topmenu_price')
								->setTemplate('catalog/product/price.phtml')
								->setDisplayMinimalPrice(true)
								->setProduct($_product)
								->toHtml();
    }

    protected function _toHtml()
    {
        return parent::_toHtml();
    }

   
	
}
