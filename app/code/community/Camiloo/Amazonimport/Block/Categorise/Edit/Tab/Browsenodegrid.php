<?php

class Camiloo_Amazonimport_Block_Categorise_Edit_Tab_Browsenodegrid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('browsenodegrid');
		$this->setDefaultSort('category_name');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}

	protected function _prepareCollection()
	{
		$mkt = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_selection_marketplace');
		 
		 
		$collection = Mage::getModel('amazonimportbrowsenodes/amazonimportbrowsenodes')->getCollection()
			->addFieldToFilter('country_id', array('eq' => $mkt))
			->addFieldToFilter('browsenode_id', array('neq' => 0))
			->addExpressionToSelect('category_tree_location','REPLACE(category_tree_location,"/"," &gt; ")')
			->addExpressionToSelect('category_one','CONCAT("<a href=\"#\" onclick=\"setPrimaryCategory(\'",bnid,"\');\">Click Here</a>")')
			->addExpressionToSelect('category_two','CONCAT("<a href=\"#\" onclick=\"setSecondaryCategory(\'",bnid,"\');\">Click Here</a>")');
			
			/*
			$this->addColumn('browsenode_id',
		array(
                'header'=> Mage::helper('amazonimport')->__('Browse Node ID'),
                'width' => '25px',
                'type'  => 'number',
                'index' => 'browsenode_id',
		));
			
			*/
			

		$collection->getSelect()->distinct(true);
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	public function getBrowsenodecategories(){
	
		$mkt = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_selection_marketplace');
		 
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		
		$_sql = "SELECT category_name FROM {$table_prefix}amazonimport_browsenodes WHERE country_id='$mkt' GROUP BY category_name;";
		$result = $db->query($_sql);
		$returnArray = array();
		
		while($row = $result->fetch(PDO::FETCH_ASSOC)){
			$returnArray["".$row['category_name'].""] = $row['category_name'];
		}
		
		return $returnArray;
	}

	protected function _prepareColumns()
	{
		
		$this->addColumn('category_name',
		array(
                'header'=> Mage::helper('amazonimport')->__('Category'),
                'width' => '150px',
                'index' => 'category_name',
				'type' => 'options',
		  		'options' => $this->getBrowsenodecategories(),
		));

		$this->addColumn('category_tree_location',
		array(
                'header'=> Mage::helper('catalog')->__('Category Location in Amazon'),
                'index' => 'category_tree_location',
                'type'  => 'text',
		));
		 
		$this->addColumn('category_one',
		array(
                'header'=> Mage::helper('catalog')->__('Set as Primary Category'),
                'index' => 'category_one',
				'sortable' => false,
				'filter' => false,
                'type'  => 'text',
		));
		
		$this->addColumn('category_two',
		array(
                'header'=> Mage::helper('catalog')->__('Set as Secondary Category'),
                'index' => 'category_two',
				'sortable' => false,
				'filter' => false,
                'type'  => 'text',
		));
		 
		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
        return false;
	}

	 /**
     * Return Grid URL for AJAX query
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/categorise/getbrowsenodegrid', array('_current'=>true));
    }
	
}

?>
