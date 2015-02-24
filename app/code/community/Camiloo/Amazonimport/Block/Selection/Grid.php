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


class Camiloo_Amazonimport_Block_Selection_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
  	  $mkt = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_selection_marketplace');
  	
	  $db = Mage::getSingleton("core/resource")->getConnection("core_write");
	  $table_prefix = Mage::getConfig()->getTablePrefix();
		
  	  $listThisTable = 'amazonimportlistthis'.$mkt.'/amazonimportlistthis'.$mkt;
  	  $setupTable = 'amazonimportsetup'.$mkt.'/amazonimportsetup'.$mkt;
  	  $country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
	  $store = Mage::getStoreConfig('amazonint/amazon'.$country.'/store');
  	  $spimapping = Mage::getStoreConfig('amazonint/standard_product_id/productidmap');
		
	
$db->query("DROP FUNCTION IF EXISTS AmzGetMatchFromFind; CREATE FUNCTION AmzGetMatchFromFind(paramProductid INTEGER) RETURNS VARCHAR(255) DETERMINISTIC
	BEGIN
 
	DECLARE theasincode TEXT;
	DECLARE thesetuptype TEXT;
	
	SELECT asincode INTO theasincode FROM {$table_prefix}amazonimport_setup_$country WHERE productid = paramProductid;
	SELECT setup_type INTO thesetuptype FROM {$table_prefix}amazonimport_setup_$country WHERE productid = paramProductid;
	IF theasincode != '' THEN
		IF thesetuptype = 'auto' THEN
			RETURN theasincode;
		ELSE
			RETURN '';
		END IF;
	ELSE
		IF thesetuptype = 'auto' THEN
			RETURN 'No match found yet';	
		ELSE
			RETURN '';
		END IF;
	END IF;
END;");			
		
		
		
      $collection = Mage::getModel('catalog/product')->setStoreId($store)->getCollection()
         ->addAttributeToSelect('sku')
         ->addAttributeToSelect('name')
         ->joinTable($listThisTable, 'productid=entity_id', array('productid' => 'productid',
														         'is_active' => 'is_active',
														         'is_on_amazon' => 'is_on_amazon',
														         'amazonlink' => 'amazonlink', 
														         'reprice_enabled' => 'reprice_enabled'), null, 'left')
         ->joinTable($setupTable, 'productid=entity_id', array('setup_type' => 'setup_type'), null, 'left');
 		
			$collection->addExpressionAttributeToSelect('spiormatchfield','AmzGetMatchFromFind(e.entity_id)','entity_id');
			$collection->addFieldToFilter('type_id',array('neq'=>'bundle'));
			$collection->addFieldToFilter('type_id',array('neq'=>'downloadable'));  
			$collection->addFieldToFilter('type_id',array('neq'=>'virtual'));  
         
  		if(Mage::getStoreConfig('amazonint/general/show_disabled') == 0){
			$collection->addFieldToFilter('status',array('eq'=>'1')); 
		 }
		 
		 
		 
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

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));
        
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
        ));
		
		
		$typearray = Mage::getSingleton('catalog/product_type')->getOptionArray();
		unset($typearray['bundle']);
		unset($typearray['virtual']);
		unset($typearray['downloadable']);
		
         $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Magento Product Type'),
                'width' => '100px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => $typearray,
        ));
        
        $this->addColumn('is_active',
            array(
                'header'=> Mage::helper('catalog')->__('Can Publish to Amazon?'),
                'width' => '100px',
                'index' => 'is_active',
            	'type' => 'options',
            	'options' => array('0'=>'No','1'=>'Yes'),
			));
        
        $this->addColumn('setup_type',
            array(
                'header'=> Mage::helper('catalog')->__('Search Amazon for this Product?'),
                'width' => '200px',
                'index' => 'setup_type',
            	'type' => 'options',
            	'options' => array('auto'=>'Yes','manual'=>'No, skip searching'),
        ));
        
        $this->addColumn('spiormatchfield',
            array(
                'header'=> Mage::helper('catalog')->__('Standard Product Identifier'),
                'index' => 'spiormatchfield',
        ));

        $this->addColumn('reprice_enabled',
            array(
                'header'=> Mage::helper('catalog')->__('Enable Repricing?'),
                'width' => '100px',
                'index' => 'reprice_enabled',
            	'type' => 'options',
            	'options' => array('0'=>'No','1'=>'Yes'),
        ));
        
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('amazonimport')->__('Action'),
                'width'     => '80',
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
		
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('entity_id');
        $this->getMassactionBlock()->addItem('listyes', array(
            'label'         => Mage::helper('amazonimport')->__('Allow products to be published to Amazon'),
            'url'           => $this->getUrl('*/*/masslistonamznoyes'),
            'selected'      => true,
        ));
        $this->getMassactionBlock()->addItem('listno', array(
            'label'         => Mage::helper('amazonimport')->__('Do not allow products to be published to Amazon'),
            'url'           => $this->getUrl('*/*/masslistonamznono'),
        ));
        $this->getMassactionBlock()->addItem('setupauto', array(
            'label'         => Mage::helper('amazonimport')->__('Use Find on Amazon tool for selected products'),
            'url'           => $this->getUrl('*/*/masssetupasquick'),
        ));
        $this->getMassactionBlock()->addItem('setupmanual', array(
            'label'         => Mage::helper('amazonimport')->__('Skip the Find on Amazon tool for these products'),
            'url'           => $this->getUrl('*/*/masssetupasmanual'),
        ));
        $this->getMassactionBlock()->addItem('setyesrepricing', array(
            'label'         => Mage::helper('amazonimport')->__('Use Competitive Repricing on this Product'),
            'url'           => $this->getUrl('*/*/massenablerepricing'),
        ));
        $this->getMassactionBlock()->addItem('setnorepricing', array(
            'label'         => Mage::helper('amazonimport')->__('Do not use Competitive Repricing on this Product'),
            'url'           => $this->getUrl('*/*/massdisablerepricing'),
        ));

        return $this;
	}


  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}