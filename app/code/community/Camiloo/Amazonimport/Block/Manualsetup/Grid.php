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


class Camiloo_Amazonimport_Block_Manualsetup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setDefaultSort('amazonimport_id');
      $this->setDefaultDir('ASC');
      $this->setUseAjax(true);
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
		  if($this->getData('productdatatype') != ""){
			  $this->setId(("grid".$this->getData('productdatatype')));
	  	}
  	  	$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');
		$store = Mage::getStoreConfig('amazonint/amazon'.$country.'/store');
		
		$categoriseTable = 'amazonimportcategorise'.$country.'/amazonimportcategorise'.$country;
		$setupTable = 'amazonimportsetup'.$country.'/amazonimportsetup'.$country;
		$listThisTable = 'amazonimportlistthis'.$country.'/amazonimportlistthis'.$country;
		
  	$collection = Mage::getModel('catalog/product')->setStoreId($store)->getCollection()
         ->addAttributeToSelect('sku') ->addAttributeToSelect('name')
         ->joinTable($categoriseTable, 'productid=entity_id',
         array('productid'=>'productid','productdatatype'=>'productdatatype'),
         null, 'left')
         ->joinTable($setupTable, 'productid=productid',
         array('initial_setup_complete'=>'initial_setup_complete','setup_id'=>'setup_id','setup_type'=>'setup_type'),
         null, 'left')
         ->joinTable($listThisTable, 'productid=entity_id', array('is_active'=>'is_active'), null, 'left')
         ->addFieldToFilter('productdatatype',array('eq'=>$this->getData('productdatatype')))
         ->addFieldToFilter('is_active', array('eq'=>'1'));
	  
	  	if(Mage::getStoreConfig('amazonint/general/show_disabled') == 0){
			$collection->addFieldToFilter('status',array('eq'=>'1')); 
		 }
		 
		$collection->setRowIdFieldName('setup_id');
	
	  $collection->getSelect()->distinct(true);
	  $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('name', array(
          'header'    => Mage::helper('amazonimport')->__('Title'),
          'align'     =>'left',
          'index'     => 'name',
      ));
      
      $this->addColumn('sku', array(
          'header'    => Mage::helper('amazonimport')->__('SKU'),
          'align'     =>'left',
          'index'     => 'sku',
      ));

      $this->addColumn('initial_setup_complete', array(
          'header'    => Mage::helper('amazonimport')->__('Setup Complete?'),
          'align'     =>'left',
          'index'     => 'initial_setup_complete',
      	  'type'      => 'options',
          'options' => array('0'=>'No',
          	'1'=>'Yes'),
      ));
	  
	  
      $this->addColumn('current_product_status', array(
          'header'    => Mage::helper('amazonimport')->__('Current Status'),
          'align'     =>'left',
          'index'     => 'initial_setup_complete',
      	  'type'      => 'options',
          'options' => array(
				'0'=>'Waiting for Setup to be complete',
          		'1'=>'Ready to submit',
          		'2'=>'Submitted - Waiting for feedback',
          		'3'=>'Match found, submitted automatically - Waiting for feedback',
          		'4'=>'Product Accepted by Amazon',
          		'5'=>'Problems encountered - please review'),
      ));
      
           $this->addColumn('catchange',
            array(
                'header'    =>  Mage::helper('amazonimport')->__('Change Category'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('amazonimport')->__('Edit'),
                        'url'       => 'javascript:loadPopover();',
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
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
                        'url'       => array('base'=> '*/*/edit/'),
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

		$this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('attributes', array(
            'label' => Mage::helper('catalog')->__('Mass Update Setup'),
            'url'   => $this->getUrl('*/manualsetup/edit', array('_current'=>true))
        ));
		
		
        $this->getMassactionBlock()->addItem('setupcomplete', array(
            'label' => Mage::helper('catalog')->__('Mark as Setup Complete'),
            'url'   => $this->getUrl('*/manualsetup/massmark', array('_current'=>true,'setup'=>'1'))
        ));
		
		
        $this->getMassactionBlock()->addItem('setupincomplete', array(
            'label' => Mage::helper('catalog')->__('Mark as Setup Incomplete'),
            'url'   => $this->getUrl('*/manualsetup/massmark', array('_current'=>true,'setup'=>'0'))
        ));
	
	
        $this->getMassactionBlock()->addItem('resubmit', array(
            'label' => Mage::helper('catalog')->__('Resubmit selected to Amazon'),
            'url'   => $this->getUrl('*/manualsetup/massresubmit', array('_current'=>true))
        ));
	
	
        return $this;
	}

	 /**
     * Return Grid URL for AJAX query
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/getgrid', array('_current'=>true, 'productdatatype'=>$this->getData('productdatatype')));
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}