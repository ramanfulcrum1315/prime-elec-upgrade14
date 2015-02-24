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


class Camiloo_Amazonimport_Block_Manualsetup_Ucgrid extends Mage_Adminhtml_Block_Widget_Grid
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
		  $this->setId(("grid"."uncategorised"));
	  	$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');
		$store = Mage::getStoreConfig('amazonint/amazon'.$country.'/store');
		
		$categoriseTable = 'amazonimportcategorise'.$country.'/amazonimportcategorise'.$country;
		$setupTable = 'amazonimportsetup'.$country.'/amazonimportsetup'.$country;
		$listThisTable = 'amazonimportlistthis'.$country.'/amazonimportlistthis'.$country;
	
		/*
		*	Okay, we need to do the following to get any products affected by this category mapping.
		*	The grid will basically be get products in a given set of categories which don't have a
		*	value in the override table.
		*
		*	So, lets begin by looping through the category mappings which apply to this product [as more than one entry may exist]
		*/
		
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();

		// IN this grid, things are a little different. We're not actually looking for the product to have a mapping available,
		// nor do we want it to have a value in the categorise table.
		$db->query(" DROP FUNCTION IF EXISTS AmzUnCatMapFunc; CREATE DEFINER = CURRENT_USER FUNCTION AmzUnCatMapFunc(paramProductid INTEGER) RETURNS INTEGER DETERMINISTIC
					 BEGIN
					 
					 DECLARE  current_cat_id INT;
					 DECLARE  current_count INT;
					 DECLARE  current_count_two INT;
					 DECLARE  current_count_three INT;
					 DECLARE  no_more_products INT;
					 DECLARE  match_found INT;
					 
					 DECLARE  cur_product CURSOR FOR 
					 SELECT category_id FROM {$table_prefix}catalog_category_product
											INNER JOIN {$table_prefix}catalog_category_entity ON entity_id=category_id
											WHERE product_id = paramProductid ORDER BY {$table_prefix}catalog_category_entity.level DESC;
					
					 DECLARE  CONTINUE HANDLER FOR NOT FOUND 
				     	SET no_more_products = 1;
				
					 SET match_found = 0;
					 OPEN cur_product;
					 
					 FETCH cur_product INTO current_cat_id;
					 
					 REPEAT 
					 	SELECT count(*) INTO current_count FROM {$table_prefix}amazonimport_categorymapping
						WHERE country_id='$country' AND category_id = current_cat_id; 
					 	
						IF  current_count > 0 AND match_found < 1 THEN
							SET match_found = 1;
						END  IF;
						
						FETCH  cur_product INTO current_cat_id;
					  	UNTIL  no_more_products = 1
					 END REPEAT;
					 
					 IF match_found != 1 THEN
							SELECT count(*) INTO current_count_three FROM {$table_prefix}amazonimport_categorise_$country 
								WHERE productid = paramProductid;
							IF  current_count_three > 0 THEN
								SET match_found = 1;
							END IF;
		 			 END IF;		 	
					 
					 CLOSE  cur_product;
					 RETURN match_found;
					 
END;");
        
	  	$collection = Mage::getModel('catalog/product')->setStoreId($store)->getCollection()
         ->addAttributeToSelect('sku')->addAttributeToSelect('name')->addAttributeToSelect('status')
         ->joinTable($categoriseTable, 'productid=entity_id',
         array('productid'=>'productid','productdatatype'=>'productdatatype'),
         null, 'left')
		 ->joinTable($setupTable, 'productid=productid',
         array('initial_setup_complete'=>'initial_setup_complete','setup_id'=>'setup_id','setup_type'=>'setup_type'),
         null, 'left')
         ->joinTable($listThisTable, 'productid=entity_id', array('is_active'=>'is_active'), null, 'left');
	 	 
		 
		 $collection->getSelect()->distinct(true);
		 
			$collection->addFieldToFilter('type_id',array('neq'=>'bundle'));
			$collection->addFieldToFilter('type_id',array('neq'=>'downloadable'));  
			$collection->addFieldToFilter('type_id',array('neq'=>'virtual'));  
	 
		
		 $collection->getSelect()->where("AmzUnCatMapFunc({$table_prefix}amazonimport_listthis_$country.productid) = 0");
	
		//$collection->getSelect()->group('e.entity_id');
		//$collection->setRowIdFieldName('setup_id');
		
			$collection->addFieldToFilter('is_active',array('eq'=>'1')); 
	
	  	if(Mage::getStoreConfig('amazonint/general/show_disabled') == 0){
		
			$collection->addFieldToFilter('status',array('eq'=>'1')); 
		}
		 				 
	  $this->setCollection($collection);
	  
	
	  return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
	  
	  
      $this->addColumn('id', array(
          'header'    => Mage::helper('amazonimport')->__('id'),
          'align'     =>'left',
          'index'     => 'entity_id',
		  'type'	  => 'number',
		  'width'	  => '100px'
      ));
	  
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

      $this->addColumn('current_product_status', array(
          'header'    => Mage::helper('amazonimport')->__('Current Status'),
          'align'     =>'left',
          'index'     => 'initial_setup_complete',
      	  'type'      => 'options',
          'options' => array(
				''=>'Please categorise before publishing'),
      ));
       
      return parent::_prepareColumns();
  }
  
  
  

	protected function _prepareMassaction()
	{

		$this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('changecat', array(
            'label' => Mage::helper('catalog')->__('Change Category'),
            'url'   => 'javascript:passToCategoriseFromMassAction("griduncategorised");'
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
        return $this->getUrl('*/*/getucgrid', array('_current'=>true));
    }

  public function getRowUrl($row)
  {
      return 'javascript:loadPopover('.$row->getId().');';
  }

}