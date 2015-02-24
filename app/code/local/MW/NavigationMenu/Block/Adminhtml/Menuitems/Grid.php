<?php

class MW_NavigationMenu_Block_Adminhtml_Menuitems_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('navigationmenuGrid');
      $this->setDefaultSort('item_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('navigationmenu/menuitems')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('item_id', array(
          'header'    => Mage::helper('navigationmenu')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'item_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('navigationmenu')->__('Category Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

      $this->addColumn('category_name', array(
      		'header'    => Mage::helper('navigationmenu')->__('Category'),
      		'align'     =>'left',
      		'index'     => 'category_name',
      ));
      
      if (!Mage::app()->isSingleStoreMode()) {
      	$this->addColumn('store_ids', array(
      			'header'        => Mage::helper('navigationmenu')->__('Store View'),
      			'index'         => 'store_ids',
      			'type'          => 'store',
      			'store_all'     => true,
      			'store_view'    => true,
      			'sortable'      => false,
      			'filter_condition_callback' => array($this, '_filterStoreCondition'),
      	));
      }
      
      $this->addColumn('type', array(
          'header'    => Mage::helper('navigationmenu')->__('Type'),
          'align'     => 'left',
          'index'     => 'type',
          'type'      => 'options',
          'options' => Mage::helper('navigationmenu/menuitems')->getItemsTypeOption()
      ));
      
      $this->addColumn('order', array(
      		'header'    => Mage::helper('navigationmenu')->__('Order'),
      		'align'     =>'left',
      		'index'     => 'order',
      ));
      
      $this->addColumn('column', array(
      		'header'    => Mage::helper('navigationmenu')->__('# of Columns'),
      		'align'     =>'left',
      		'index'     => 'column',
      ));
      
      $this->addColumn('status', array(
          'header'    => Mage::helper('navigationmenu')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              0 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('navigationmenu')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('navigationmenu')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('navigationmenu')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('navigationmenu')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('navigationmenu_id');
        $this->getMassactionBlock()->setFormFieldName('navigationmenu');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('navigationmenu')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('navigationmenu')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('navigationmenu/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('navigationmenu')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('navigationmenu')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }
    
    protected function _filterStoreCondition($collection, $column)
    {
    	if (!$value = $column->getFilter()->getValue()) {
    		return;
    	}
    
    	$this->getCollection()->getSelect()->where("main_table.store_ids like '%".$value."%' or main_table.store_ids = '0'");
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}