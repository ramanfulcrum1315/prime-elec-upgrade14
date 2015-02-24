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


class Camiloo_Amazonimport_Block_Logging_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('amazonimportGrid');
      $this->setSaveParametersInSession(true);
        $this->setDefaultSort('message_time');
  }

  protected function _prepareCollection()
  {
	 $db = Mage::getSingleton("core/resource")->getConnection("core_write");
	 $table_prefix = Mage::getConfig()->getTablePrefix();
	  
      $collection = Mage::getModel('amazonimport/amazonimportlog')->getCollection()
	  ->addExpressionToSelect('outgoing2','IF(CHAR_LENGTH(`outgoing`) > 500, "XML Message",`outgoing`)')
	  ->addExpressionToSelect('incoming2','IF(CHAR_LENGTH(`incoming`) > 500, "XML Message",`incoming`)')
	  ->addExpressionToSelect('messagetype','CONCAT("<b>",'."IF(LOCATE('<MessageType>Product</MessageType>',CONCAT(incoming,outgoing)) > 1,'Product Data',IF(LOCATE('<MessageType>Inventory</MessageType>',CONCAT(incoming,outgoing)) > 1,'Inventory Data',IF(LOCATE('<MessageType>ProductImage</MessageType>',CONCAT(incoming,outgoing)) > 1,'Image Data',IF(LOCATE('<MessageType>Relationship</MessageType>',CONCAT(incoming,outgoing)) > 1,'Variation Data',IF(LOCATE('<MessageType>Price</MessageType>',CONCAT(incoming,outgoing)) > 1,'Price Data',IF(LOCATE('_GET_FLAT_FILE_OPEN_LISTINGS_DATA_',CONCAT(incoming,outgoing)) > 1,'Active Inventory Report',IF(LOCATE('u	asin	price	quantity',CONCAT(incoming,outgoing)) > 1,'Active Inventory Report',IF(LOCATE('<MessageType>OrderFulfillment</MessageType>',CONCAT(incoming,outgoing)) > 1,'Order Shipment',IF(LOCATE('<MessageType>ProcessingReport</MessageType>',CONCAT(incoming,outgoing)) > 1,'Processing Report',IF(LOCATE('edProcessingResultNotReady',CONCAT(incoming,outgoing)) > 1,'Processing Report','Order Data'))))))))))".',"</b>")')
	  ->addExpressionToSelect('direction',"IF(LENGTH(outgoing) > 0,IF(LENGTH(outgoing) > LENGTH(incoming),'Magento &gt; Amazon','Amazon &gt; Magento'),'Local Message')");
	  
	  
	  $this->setCollection($collection); 
      return parent::_prepareCollection();
     
  }
  
   protected function _setFilterValues($data)
    {
        foreach ($this->getColumns() as $columnId => $column) {
			
				if (isset($data[$columnId]) && (!empty($data[$columnId]) || strlen($data[$columnId]) > 0) && $column->getFilter()) {
					$column->getFilter()->setValue($data[$columnId]);
					if($columnId == "messagetype"){
						$this->getCollection()->addFieldToFilter('CONCAT("<b>",'."IF(LOCATE('<MessageType>Product</MessageType>',CONCAT(incoming,outgoing)) > 1,'Product Data',IF(LOCATE('<MessageType>Inventory</MessageType>',CONCAT(incoming,outgoing)) > 1,'Inventory Data',IF(LOCATE('<MessageType>ProductImage</MessageType>',CONCAT(incoming,outgoing)) > 1,'Image Data',IF(LOCATE('<MessageType>Relationship</MessageType>',CONCAT(incoming,outgoing)) > 1,'Variation Data',IF(LOCATE('<MessageType>Price</MessageType>',CONCAT(incoming,outgoing)) > 1,'Price Data',IF(LOCATE('_GET_FLAT_FILE_OPEN_LISTINGS_DATA_',CONCAT(incoming,outgoing)) > 1,'Active Inventory Report',IF(LOCATE('u	asin	price	quantity',CONCAT(incoming,outgoing)) > 1,'Active Inventory Report',IF(LOCATE('<MessageType>OrderFulfillment</MessageType>',CONCAT(incoming,outgoing)) > 1,'Order Shipment',IF(LOCATE('<MessageType>ProcessingReport</MessageType>',CONCAT(incoming,outgoing)) > 1,'Processing Report',IF(LOCATE('edProcessingResultNotReady',CONCAT(incoming,outgoing)) > 1,'Processing Report','Order Data'))))))))))".',"</b>")', $column->getFilter()->getCondition());
					}else if($columnId == "direction"){
						$this->getCollection()->addFieldToFilter("IF(LENGTH(outgoing) > 0,IF(LENGTH(outgoing) > LENGTH(incoming),'Magento &gt; Amazon','Amazon &gt; Magento'),'Local Message')", $column->getFilter()->getCondition());
					}else{
						$this->_addColumnFilterToCollection($column);
					}
				}
				
        }
        return $this;
    }

  protected function _prepareColumns()
  {
      $this->addColumn('log_id', array(
          'header'    => Mage::helper('amazonimport')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'log_id',
          'filter'    => false,
          'sortable'  => true,
      ));

      $this->addColumn('message_time', array(
          'header'    => Mage::helper('amazonimport')->__('Message Date / Time'),
          'align'     =>'left',
          'index'     => 'message_time',
            'type'          => 'datetime',
          'filter'    => false,
          'sortable'  => true,
      ));

      $this->addColumn('messagetype', array(
			'header'    => Mage::helper('amazonimport')->__('Message Type'),
			'index'     => 'messagetype',
            'type'          => 'text',
      ));
	  
	  
      $this->addColumn('direction', array(
			'header'    => Mage::helper('amazonimport')->__('Message Direction'),
			'index'     => 'direction',
            'type'          => 'options',
			'options' 	=> array('Magento &gt; Amazon'=>'Magento > Amazon',
								 'Amazon &gt; Magento'=>'Amazon > Magento',
								 'Local Message'=>'Local Message'),
								 
      ));
	
	  
	
      $this->addColumn('outgoing', array(
          'header'    => Mage::helper('amazonimport')->__('Outgoing Message'),
          'align'     =>'left',
          'index'     => 'outgoing2',
      	  'type'      => 'text',
          'filter'    => false,
          'sortable'  => true,
      ));

      $this->addColumn('incoming', array(
          'header'    => Mage::helper('amazonimport')->__('Incoming Message'),
          'align'     =>'left',
          'index'     => 'incoming2',
 		  'type'      => 'text',
          'filter'    => false,
          'sortable'  => true,
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('amazonimport')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('amazonimport')->__('Send to Support'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'log_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => true,
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