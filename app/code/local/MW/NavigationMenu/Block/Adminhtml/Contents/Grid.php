<?php

class MW_NavigationMenu_Block_Adminhtml_Contents_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('navigationmenuGrid');
      $this->setDefaultSort('content_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('navigationmenu/contents')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('content_id', array(
          'header'    => Mage::helper('navigationmenu')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'content_id',
      ));

      $this->addColumn('name', array(
      		'header'    => Mage::helper('navigationmenu')->__('Content Name'),
      		'align'     =>'left',
      		'index'     => 'name',
      ));
      
      $this->addColumn('position', array(
      		'header'    => Mage::helper('navigationmenu')->__('Position'),
      		'align'     => 'left',
      		'index'     => 'position',
      		'type'      => 'options',
      		'options' => Mage::helper('navigationmenu/contents')->getContentsPositionOption()
      ));
      
      $this->addColumn('title', array(
          'header'    => Mage::helper('navigationmenu')->__('Content Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));
      
      $this->addColumn('text', array(
      		'header'    => Mage::helper('navigationmenu')->__('Text'),
      		'align'     =>'left',
      		'index'     => 'text',
      ));
      
      $this->addColumn('image', array(
      		'header'    => Mage::helper('navigationmenu')->__('Image'),
      		'align'     =>'left',
      		'type' 		=> 'image',
      		'index'     => 'image',
      		'width' 	=> '150px',
      		'renderer'  => 'navigationmenu/adminhtml_renderer_image'
      ));
      
      $this->addColumn('sku', array(
      		'header'    => Mage::helper('navigationmenu')->__('Product SKU'),
      		'align'     =>'left',
      		'index'     => 'sku',
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

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}