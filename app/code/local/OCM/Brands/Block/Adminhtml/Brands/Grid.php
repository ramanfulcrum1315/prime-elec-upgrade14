<?php

class OCM_Brands_Block_Adminhtml_Brands_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('brandsGrid');
      $this->setDefaultSort('brands_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('brands/brands')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('brands_id', array(
          'header'    => Mage::helper('brands')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'brands_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('brands')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('brands')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('status', array(
          'header'    => Mage::helper('brands')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
      $this->addColumn('featured', array(
          'header'    => Mage::helper('brands')->__('Featured'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'featured',
          'type'      => 'options',
          'options'   => array(
              1 => 'Yes',
              2 => 'No',
          ),
      ));

        $this->addColumn('show_in_menu', array(
            'header'    => Mage::helper('catalog')->__('Menu'),
            'align'     =>'left',
            'index'     => 'show_in_menu',
            'width'     =>  '1',
          'type'      => 'options',
          'options'   => array(
              1 => 'Yes',
              0 => 'No',
          ),
            //'editable'  => true        
        ));


        $this->addColumn('menu_position', array(
            'header'    => Mage::helper('catalog')->__('Position'),
            'align'     =>'left',
            'index'     => 'menu_position',
            'type'      => 'number',
            'width'     =>  '1',
            'sortable'  => true,
            //'editable'  => true        
        ));


//	  
//        $this->addColumn('action',
//            array(
//                'header'    =>  Mage::helper('brands')->__('Action'),
//                'width'     => '100',
//                'type'      => 'action',
//                'getter'    => 'getId',
//                'actions'   => array(
//                    array(
//                        'caption'   => Mage::helper('brands')->__('Edit'),
//                        'url'       => array('base'=> '*/*/edit'),
//                        'field'     => 'id'
//                    )
//                ),
//                'filter'    => false,
//                'sortable'  => false,
//                'index'     => 'stores',
//                'is_system' => true,
//        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('brands')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('brands')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('brands_id');
        $this->getMassactionBlock()->setFormFieldName('brands');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('brands')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('brands')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('brands/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('brands')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('brands')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));

        $this->getMassactionBlock()->addItem('update_position', array(
             'label'    => Mage::helper('brands')->__('Update Menu'),
             'url'      => $this->getUrl('*/*/massPosition'),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'action',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('brands')->__('Use In Menu'),
                         'values' => array(1 => 'Yes',2 => 'No')
                     )
             )

             //'confirm'  => Mage::helper('brands')->__('Are you sure?')
        ));

        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}