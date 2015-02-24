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


class Camiloo_Amazonimport_Block_Reviewerrors_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('amazonimportGrid');
      $this->setDefaultSort('sku');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
	
	$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_reviewerrors_marketplace');
	  	$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
		$store = Mage::getStoreConfig('amazonint/amazon'.$country.'/store');
		
	
  	$collection = Mage::getModel('catalog/product')->setStoreId($store)->getCollection()
         ->addAttributeToSelect('sku')
         ->addAttributeToSelect('name')
         ->joinTable('amazonimporterrorlog'.$country.'/amazonimporterrorlog'.$country,
         			 'productid=entity_id',
					 array('elog_id'=>'elog_id','productid'=>'productid','result'=>'result',
					 'result_description'=>'result_description','submission_type'=>'submission_type'),
					 null,
					 'left')
		 ->addFieldToFilter('result',array('neq'=>''))
		 ->joinTable('amazonimportlistthis'.$country.'/amazonimportlistthis'.$country, 
		 			 'productid=entity_id', array('is_active' => 'is_active'), null, 'left')		 
		 ->addFieldToFilter('is_active',array('eq' => 1)); // only show products that have a result

	  $collection->setRowIdFieldName('elog_id');
	  $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
	  
      $this->addColumn('elog_id', array(
          'header'    => Mage::helper('amazonimport')->__('Error ID'),
          'align'     =>'left',
          'index'     => 'elog_id',
          'filter'    => false,
          'sortable'  => true,
      ));
	  
      $this->addColumn('name', array(
          'header'    => Mage::helper('amazonimport')->__('Product Title'),
          'align'     =>'left',
          'index'     => 'name',
          'filter'    => false,
          'sortable'  => true,
      ));
      
      $this->addColumn('sku', array(
          'header'    => Mage::helper('amazonimport')->__('SKU'),
          'align'     =>'left',
          'index'     => 'sku',
          'filter'    => false,
          'sortable'  => true,
      ));
	  
	        
      $this->addColumn('error', array(
          'header'    => Mage::helper('amazonimport')->__('Error Preview'),
          'align'     =>'left',
          'index'     => 'result_description',
		  'type'      => 'text',
          'filter'    => false,
          'sortable'  => true,
		  'truncate'  => 50,
      ));
	  
      $this->addColumn('submission_type', array(
          'header'    => Mage::helper('amazonimport')->__('Submission Type'),
          'align'     =>'left',
          'index'     => 'submission_type',
          'filter'    => false,
          'sortable'  => true,
      	  'type'      => 'options',
          'options' => array('Product'=>'Product','Images'=>'Product Image','Price'=>'Product Price','Quantity'=>'Product Stock','Relationship'=>'Product Relationship'),
      ));
      	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('amazonimport')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getElogId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('amazonimport')->__('View Issue'),
                        'url'       => array('base'=> '*/*/edit/'),
                        'field'     => 'elog_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
        ));
        
      return parent::_prepareColumns();
  }
  
  
    protected function _prepareMassaction()
    {
 //       $this->setMassactionIdFieldOnlyIndexValue(true);
		$this->getMassactionBlock()->setFormFieldName('bulkerrorlog');
        
        $this->getMassactionBlock()->addItem('attributes', array(
            'label' => Mage::helper('amazonimport')->__('Mark selected as resolved'),
            'url'   => $this->getUrl('*/*/bulkresolve', array('_current'=>true))
        ));
		
		$this->setMassactionIdField('elog_id');
		
         return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getElogId()));
  }

}