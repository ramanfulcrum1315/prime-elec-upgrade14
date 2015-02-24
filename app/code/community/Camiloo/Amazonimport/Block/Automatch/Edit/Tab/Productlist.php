<?php

class Camiloo_Amazonimport_Block_Automatch_Edit_Tab_Productlist extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('amazonimportGrid');
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}

	protected function _prepareCollection()
	{
		$mkt = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_selection_marketplace');
		 
		$listThisTable = 'amazonimportlistthis'.$mkt.'/amazonimportlistthis'.$mkt;
		$setupTable = 'amazonimportsetup'.$mkt.'/amazonimportsetup'.$mkt;
		 
		$collection = Mage::getModel('catalog/product')->getCollection()
		->addAttributeToSelect('sku')
		->addAttributeToSelect('name')
		->joinTable($listThisTable, 'productid=entity_id', array('productid' => 'productid',
														         'is_active' => 'is_active',
														         'is_on_amazon' => 'is_on_amazon',
														         'amazonlink' => 'amazonlink', 
														         'reprice_enabled' => 'reprice_enabled'), null, 'left')
		->joinTable($setupTable, 'productid=entity_id', array('setup_type' => 'setup_type', 'asincode' => 'asincode'), null, 'left')
		->addFieldToFilter('setup_type', array('eq' => 'auto'));

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

		$this->addColumn('type',
		array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '100px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
		));

		$this->addColumn('asincode',
		array(
                'header'=> Mage::helper('catalog')->__('Match Found'),
                'width' => '70px',
                'index' => 'asincode',
		));

		$this->addColumn('reprice_enabled',
		array(
                'header'=> Mage::helper('catalog')->__('Repricing?'),
                'width' => '100px',
                'index' => 'reprice_enabled',
            	'type' => 'options',
            	'options' => array(''=>'Not set','0'=>'No','1'=>'Yes'),
		));

		$this->addColumn('is_on_amazon',
		array(
                'header'=> Mage::helper('catalog')->__('On Your Amazon Account?'),
                'width' => '100px',
                'index' => 'is_on_amazon',
            	'type' => 'options',
            	'options' => array('0'=>'Unknown','1'=>'Yes'),
		));
		
		$this->addColumn('action', array(
                'header'    =>  '  ',
                'width'     => '80px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('amazonimport')->__('Erase Match'),
                        'url'       => array('base'=> '*/*/erasematch'),
                        'field'     => 'entity_id'
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
        $this->getMassactionBlock()->addItem('listdisable', array(
            'label'         => Mage::helper('amazonimport')->__('Set Publish This On Amazon? to No'),
            'url'           => $this->getUrl('*/automatch/masslistonamzno'),
            'selected'      => true,
        ));
        $this->getMassactionBlock()->addItem('movetomanual', array(
            'label'         => Mage::helper('amazonimport')->__('Move to Manual Setup method'),
            'url'           => $this->getUrl('*/automatch/massmovetoadvanced'),
        ));
        $this->getMassactionBlock()->addItem('erasematch', array(
            'label'         => Mage::helper('amazonimport')->__('Erase Matches'),
            'url'           => $this->getUrl('*/automatch/masserasematches'),
        ));
        $this->getMassactionBlock()->addItem('togglerepricing', array(
            'label'         => Mage::helper('amazonimport')->__('Toggle Repricing'),
            'url'           => $this->getUrl('*/automatch/masstogglerepricing'),
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
        return $this->getUrl('*/automatch/getproductgrid', array('_current'=>true));
    }
	//public function getGridUrl()
	//{
	//    return $this->getUrl('*/*/edit', array('_current' => true));
	//}
	
}

?>
