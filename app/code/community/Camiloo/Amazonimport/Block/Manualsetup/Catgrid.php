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


class Camiloo_Amazonimport_Block_Manualsetup_Catgrid extends Mage_Adminhtml_Block_Widget_Grid
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
	  	}else{
			 $this->setId("gridnovalue");
		}
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
		//	 DECLARE  no_more_products, quantity_in_stock INT DEFAULT 0;
		//	 DECLARE  prd_code VARCHAR(255);
		$db->query(" DROP FUNCTION IF EXISTS AmzCatMapFunc; CREATE FUNCTION AmzCatMapFunc(paramProductid INTEGER, outputType TEXT) RETURNS TEXT DETERMINISTIC
					 BEGIN
					 
					 DECLARE  current_cat_id INT;
					 DECLARE  current_count INT;
					 DECLARE  current_count_two INT;
					 DECLARE  current_count_three INT;
					 DECLARE  no_more_products INT;
					 DECLARE  match_found INT;
					 DECLARE conditionvalue TEXT;
					 
					 DECLARE  cur_product CURSOR FOR 
					 SELECT category_id FROM {$table_prefix}catalog_category_product
											INNER JOIN {$table_prefix}catalog_category_entity ON entity_id=category_id
											WHERE product_id = paramProductid ORDER BY {$table_prefix}catalog_category_entity.level DESC;
					
					 DECLARE  CONTINUE HANDLER FOR NOT FOUND 
				     	SET no_more_products = 1;
				
					 SET match_found = 0;
					 OPEN  cur_product;
					 
					 FETCH  cur_product INTO current_cat_id;
					 
					 mainloop: REPEAT 
					 	SELECT count(*) INTO current_count FROM {$table_prefix}amazonimport_categorymapping
						WHERE country_id='$country' AND itemtype LIKE '%/".$this->getData('productdatatype')."' AND category_id = current_cat_id; 
					 	
						IF  current_count > 0 AND match_found < 1 THEN
							SET match_found = 1;
							SELECT count(*) INTO current_count_two FROM {$table_prefix}amazonimport_categorise_$country 
							WHERE productdatatype != '".$this->getData('productdatatype')."' AND productid = paramProductid;
							IF  current_count_two > 0 THEN
								SET match_found = 0;
							END IF;
							
							IF match_found = 1 AND outputType = 1 THEN
							 SELECT `condition` INTO conditionvalue FROM {$table_prefix}amazonimport_categorymapping
							 WHERE country_id='$country' AND itemtype LIKE '%/".$this->getData('productdatatype')."' AND category_id = current_cat_id; 
					 		END IF;
							
							
						END  IF;
						
						
					 	SELECT count(*) INTO current_count FROM {$table_prefix}amazonimport_categorymapping
						WHERE country_id='$country' AND itemtype NOT LIKE '%/".$this->getData('productdatatype')."' AND category_id = current_cat_id; 
						IF  current_count > 0 AND match_found < 1 THEN
						   LEAVE mainloop;
						END IF;
					
						FETCH  cur_product INTO current_cat_id;
					  	UNTIL  no_more_products = 1
					 END REPEAT;
					 
					 IF match_found < 1 THEN
							SELECT count(*) INTO current_count_three FROM {$table_prefix}amazonimport_categorise_$country 
								WHERE productdatatype = '".$this->getData('productdatatype')."' AND productid = paramProductid;
							IF  current_count_three > 0 THEN
								SET match_found = 2;
								
								IF outputType = 1 THEN
								 SELECT `condition` INTO conditionvalue FROM {$table_prefix}amazonimport_categorise_$country 
								 WHERE productdatatype = '".$this->getData('productdatatype')."' AND productid = paramProductid;
								END IF;
								
							END IF;
		 			 END IF;		 	
					 
					 CLOSE  cur_product;
					 
					 IF outputType = 1 THEN
					 	RETURN conditionvalue;
					 ELSE
					 	RETURN match_found;
					 END IF;		 
					 
END;");
$db->query("DROP FUNCTION IF EXISTS AmzGetSetupStatus; CREATE FUNCTION AmzGetSetupStatus(paramProductid INTEGER) RETURNS INTEGER DETERMINISTIC
	BEGIN
 
	DECLARE current_cat_id INT;
	DECLARE no_more_products INT;
	DECLARE setupcompletecount INT;
	DECLARE listthisstatus INT;
	DECLARE errorlogrowcount INT;
	
	SET setupcompletecount = 0;
	SET listthisstatus = 0;
	SET errorlogrowcount = 0;
	
	SELECT is_on_amazon INTO listthisstatus FROM {$table_prefix}amazonimport_listthis_$country WHERE productid = paramProductid;
		
	IF setupcompletecount = 0 AND listthisstatus = 1 THEN
		RETURN 5;
	END IF; 

	SELECT count(*) INTO setupcompletecount FROM {$table_prefix}amazonimport_setup_$country WHERE productid = paramProductid AND ((setup_type = 'manual' AND initial_setup_complete = 1) OR (setup_type = 'auto' AND asincode != '' AND initial_setup_complete = 1));
	

	IF setupcompletecount = 0 THEN
	
		
		SELECT count(*) INTO setupcompletecount FROM {$table_prefix}amazonimport_setup_$country WHERE productid = paramProductid AND setup_type = 'manual' AND initial_setup_complete != 1;
		IF setupcompletecount != 0 THEN		
			RETURN 0;
		END IF;
		
		SELECT count(*) INTO setupcompletecount FROM {$table_prefix}amazonimport_setup_$country WHERE productid = paramProductid AND setup_type = 'auto' AND asincode = '' AND initial_setup_complete = 1;
		IF setupcompletecount != 0 THEN		
			RETURN 7;
		END IF;
		
	END IF;
	
	SELECT count(*) INTO errorlogrowcount FROM {$table_prefix}amazonimport_errorlog_$country 
		WHERE productid = paramProductid AND messageid < 1 AND submission_type = 'Product' AND result='';
	
	IF errorlogrowcount = 1 AND listthisstatus != 1 THEN
		RETURN 1;
	END IF;
	
	SELECT count(*) INTO errorlogrowcount FROM {$table_prefix}amazonimport_errorlog_$country 
		WHERE productid = paramProductid AND messageid > 0 AND submission_type = 'Product' AND result='';
	
	IF errorlogrowcount = 1 THEN
		RETURN 2;
	END IF;
	
	SELECT count(*) INTO errorlogrowcount FROM {$table_prefix}amazonimport_errorlog_$country 
		WHERE productid = paramProductid AND submission_type = 'Product';
	
	IF errorlogrowcount = 0 AND listthisstatus = 1 THEN
		RETURN 3;
	END IF; 					
		
	SELECT is_active INTO listthisstatus FROM {$table_prefix}amazonimport_listthis_$country WHERE productid = paramProductid;
		
	SELECT count(*) INTO errorlogrowcount FROM {$table_prefix}amazonimport_errorlog_$country 
		WHERE productid = paramProductid AND messageid = 0 AND submission_type = 'Product' AND result != '';
	
	IF errorlogrowcount > 0 AND listthisstatus = 1 THEN
		RETURN 4;
	END IF; 
	
	RETURN 6;
END;");		
	
	  	$collection = Mage::getModel('catalog/product')->setStoreId($store)->getCollection()
         ->addAttributeToSelect('sku')->addAttributeToSelect('name')->addAttributeToSelect('status')
    	 ->joinTable($setupTable, 'productid=entity_id',
         array('initial_setup_complete'=>'initial_setup_complete','setup_id'=>'setup_id','setup_type'=>'setup_type'),
         null, 'left')
         ->joinTable($listThisTable, 'productid=entity_id', array('is_active'=>'is_active'), null, 'left')
	 	 ->addExpressionAttributeToSelect('conditionvalue','AmzCatMapFunc('.$table_prefix.'amazonimport_listthis_'.$country.'.productid,1)','entity_id')
		 ->addExpressionAttributeToSelect('categorysetuptype','AmzCatMapFunc('.$table_prefix.'amazonimport_listthis_'.$country.'.productid,0)','entity_id')
		 ->addExpressionAttributeToSelect('setup_status','AmzGetSetupStatus('.$table_prefix.'amazonimport_listthis_'.$country.'.productid)','entity_id');
		 $collection->getSelect()->distinct(true);
	 
			$collection->addFieldToFilter('type_id',array('neq'=>'bundle'));
			$collection->addFieldToFilter('type_id',array('neq'=>'downloadable'));  
			$collection->addFieldToFilter('type_id',array('neq'=>'virtual'));  
		 $collection->getSelect()->where("AmzCatMapFunc({$table_prefix}amazonimport_listthis_$country.productid,0) > 0");
	
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
	  
      $this->addColumn('entity_id', array(
          'header'    => Mage::helper('amazonimport')->__('Product ID'),
          'align'     =>'left',
          'index'     => 'entity_id',
		  'type'	  => 'number'
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
	  
	  
      $this->addColumn('conditionvalue', array(
          'header'    => Mage::helper('amazonimport')->__('Condition'),
          'align'     =>'left',
          'index'     => 'conditionvalue',
      ));

	  
      $this->addColumn('categorysetuptype', array(
          'header'    => Mage::helper('amazonimport')->__('Category Type'),
          'align'     =>'left',
          'index'     => 'categorysetuptype',
      	  'type'      => 'options',
          'options' => array('2'=>'Manual Override',
          	'1'=>'Category Mapping'),
      ));
	  
      $this->addColumn('initial_setup_complete', array(
          'header'    => Mage::helper('amazonimport')->__('Setup Complete?'),
          'align'     =>'left',
          'index'     => 'initial_setup_complete',
      	  'type'      => 'options',
          'options' => array('0'=>'No',
          	'1'=>'Yes'),
      ));
	  
	  
      $this->addColumn('setup_status', array(
          'header'    => Mage::helper('amazonimport')->__('Current Status'),
          'align'     =>'left',
          'index'     => 'setup_status',
      	  'type'      => 'options',
          'options' => array('0'=>'Waiting for Setup to be complete',
          				'1'=>'Submitting shortly',
          				'2'=>'Submitted to Amazon - Awaiting Feedback',
          				'3'=>'Product Accepted by Amazon',
						'4'=>'Error encountered - please review',
        		  		'5'=>'Product already exists on Amazon account',
        		  		'6'=>'Unknown Status',
        		  		'7'=>'Waiting to Find on Amazon or be skipped'),
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
                        'url'       => array('base'=> '*/*/edit/'.'productdatatype/'.$this->getData('productdatatype').'/'),
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

		if($this->getData('productdatatype') == ""){
	   	$this->getMassactionBlock()->addItem('changecat', array(
            'label' => Mage::helper('catalog')->__('Change Category'),
            'url'   => 'javascript:passToCategoriseFromMassAction("gridnovalue");'
        ));		
		$this->getMassactionBlock()->addItem('changecondition', array(
            'label' => Mage::helper('catalog')->__('Change Condition'),
            'url'   => 'javascript:passToConditionFromMassAction("gridnovalue");'
        ));
		
		}else{
        $this->getMassactionBlock()->addItem('changecat', array(
            'label' => Mage::helper('catalog')->__('Change Category'),
            'url'   => 'javascript:passToCategoriseFromMassAction("grid'.$this->getData('productdatatype').'");'
        ));
		$this->getMassactionBlock()->addItem('changecondition', array(
            'label' => Mage::helper('catalog')->__('Change Condition'),
            'url'   => 'javascript:passToConditionFromMassAction("grid'.$this->getData('productdatatype').'");'
        ));
		
		}
	  
        $this->getMassactionBlock()->addItem('removeoverrides', array(
            'label' => Mage::helper('catalog')->__('Remove category mapping and condition overrides'),
            'url'   => $this->getUrl('*/manualsetup/removeanyoverrides', array('_current'=>true))
        ));
		

        $this->getMassactionBlock()->addItem('attributes', array(
            'label' => Mage::helper('catalog')->__('Mass Update Setup'),
            'url'   => $this->getUrl('*/manualsetup/edit', array('_current'=>true))
        ));
		
		
        $this->getMassactionBlock()->addItem('setupcomplete', array(
            'label' => Mage::helper('catalog')->__('Mark as Setup Complete or Issue Resolved'),
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
        return $this->getUrl('*/*/getcatgrid', array('_current'=>true, 'productdatatype'=>$this->getData('productdatatype')));
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'productdatatype'=>$this->getData('productdatatype')));
  }

}