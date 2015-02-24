<?php
class Redstage_Groupcat_Block_Procats extends Mage_Core_Block_Template
    implements Mage_Widget_Block_Interface
{

    protected $_serializer = null;

    /**
     * Initialization
     */
    protected function _construct()
    {
        $this->_serializer = new Varien_Object();
        parent::_construct();
    }

    protected function _beforeToHtml()
    {
        $all_cat_ids = explode(",", $this->getCategoryIds());

        if($this->getPromotionOnly()){
            $collection = Mage::getResourceModel('catalog/product_collection')
                ->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds())
                ->joinField('category_id',
                    'catalog/category_product',
                    'category_id',
                    'product_id=entity_id',
                    null,
                    'left')
                ->addAttributeToFilter('category_id', array( 'in' => $all_cat_ids ))
                ->addFieldToFilter(
                    'promotion',
                    array(
                        'eq' => Mage::getResourceModel('catalog/product')
                                ->getAttribute('promotion')
                                ->getSource()
                                ->getOptionId(1)
                    ))
                ->addAttributeToSelect('*')
                ->setPageSize($this->getProductsCount())
                ->setCurPage(1);
            if($this->getOrderBy() == 0){
                $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
            }

        }else{

            $collection = Mage::getResourceModel('catalog/product_collection')
                ->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds())
                ->joinField('category_id',
                    'catalog/category_product',
                    'category_id',
                    'product_id=entity_id',
                    null,
                    'left')
                ->addAttributeToFilter('category_id', array( 'in' => $all_cat_ids ))
                ->addAttributeToSelect('*')
                ->setPageSize($this->getProductsCount())
                ->setCurPage(1);
            if($this->getOrderBy() == 0){
                $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
            }
        }

        //$collection->getSelect()->group('e.entity_id');
        //$collection->distinct(true);

        $this->setCatId(0);
        if(sizeof($all_cat_ids) == 1){
            $this->setCatId($this->getCategoryIds());
        }
        $this->setProductCollection($collection);

        return parent::_beforeToHtml();
    }

    /**
     * Produce links list rendered as html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = $this->getWidgetName();
        $cidz = trim($this->getCategoryIds());
        $cidz = str_replace(",", "", $cidz);

        $this->assign('wzid', $cidz);
        $this->assign('wlabel', $html);

        $this->assign('column', $this->getColumnCount());

        return parent::_toHtml();
    }


}