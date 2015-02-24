<?php
class Redstage_Groupcat_Block_Cats extends Mage_Core_Block_Template
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
        //$all_cat_ids = explode(",", $this->getCategoryIds());

        if($this->getCategoryId()){

            $children = Mage::getModel('catalog/category')->getCategories($this->getCategoryId());

        }

        $this->setCatsCollection($children);

        return parent::_beforeToHtml();
    }

    /**
     * Category links list rendered as html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';

        $this->assign('column', $this->getColumnCount());

        return parent::_toHtml();
    }




}