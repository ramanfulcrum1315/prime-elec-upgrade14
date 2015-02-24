<?php
/**
 * Camiloo Limited
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.camiloo.co.uk/license.txt
 *
 * @category   Camiloo
 * @package    Camiloo_Amazonimport
 * @copyright  Copyright (c) 2011 Camiloo Limited (http://www.camiloo.co.uk)
 * @license    http://www.camiloo.co.uk/license.txt
 */


class Camiloo_Amazonimport_Block_Categorise_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('amazonimportGrid');
      $this->setDefaultSort('amazonimport_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
  	  // Get current marketplace
  	  $mkt = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_categorise_marketplace');
	  $store = Mage::getStoreConfig('amazonint/amazon'.$mkt.'/store');
	  
	  $setupTable = 'amazonimportsetup'.$mkt.'/amazonimportsetup'.$mkt;
	  $categTable = 'amazonimportcategorise'.$mkt.'/amazonimportcategorise'.$mkt;
	  $listthisTable = 'amazonimportlistthis'.$mkt.'/amazonimportlistthis'.$mkt;
		
      $collection = Mage::getModel('catalog/product')->setStoreId($store)->getCollection()
         ->addAttributeToSelect('sku')
         ->addAttributeToSelect('name')
         ->joinTable($categTable, 'productid=entity_id',
         	array('productid'=>'productid','browsenode1'=>'browsenode1','browsenode2'=>'browsenode2','category'=>'category','producttype'=>'producttype'),
         		null, 'left')
         ->joinTable($listthisTable, 'productid=entity_id', array('is_active'=>'is_active'), null, 'left')
		 ->joinTable($setupTable, 'productid=entity_id', array('setup_type'=>'setup_type'), null, 'left')
		 ->addFieldToFilter('setup_type',array("eq"=>"manual"))
		 ->addFieldToFilter('is_active',array("eq"=>"1"));

	  $collection->getSelect()->distinct(true);
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  
  protected function _prepareColumns()
  {
           $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '25px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));
        
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
        ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));

         $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '100px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));
        
        $this->addColumn('browsenode1',
            array(
                'header'=> Mage::helper('catalog')->__('Browsenode 1'),
                'width' => '80px',
                'index' => 'browsenode1',
        ));
        
        $this->addColumn('browsenode2',
            array(
                'header'=> Mage::helper('catalog')->__('Browsenode 2'),
                'width' => '80px',
                'index' => 'browsenode2',
        ));
        
        $this->addColumn('category',
            array(
                'header'=> Mage::helper('catalog')->__('Category'),
                'index' => 'category',
        ));
        
	  $this->addColumn('producttype',
            array(
                'header'=> Mage::helper('catalog')->__('Product Type'),
                'index' => 'producttype',
        ));
        
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('amazonimport')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('amazonimport')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
    
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}