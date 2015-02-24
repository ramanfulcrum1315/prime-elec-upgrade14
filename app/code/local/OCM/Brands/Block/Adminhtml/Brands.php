<?php
class OCM_Brands_Block_Adminhtml_Brands extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_brands';
    $this->_blockGroup = 'brands';
    $this->_headerText = Mage::helper('brands')->__('Brands Manager');
    //$this->_addButtonLabel = Mage::helper('brands')->__('Add Item');

    $this->_addButton('updatebrandslist', array(
    	'label'     => Mage::helper('adminhtml')->__('Update Brands List'),
    	'onclick'   => "setLocation('".Mage::helper("adminhtml")->getUrl("adminhtml/brands/updateBrandsList/",array())."')",
    	//'class'     => 'save',
    	), -100);
    	
    if($brand_bloc_id = $this->getCmsBlockId()) {
        $this->_addButton('editBrandBlock', array(
        	'label'     => Mage::helper('adminhtml')->__('Edit Brands Block'),
        	'onclick'   => "setLocation('".Mage::helper("adminhtml")->getUrl("adminhtml/cms_block/edit",array('block_id'=>$brand_bloc_id))."')",
        	//'class'     => 'save',
        	), -100);
    } else {
        $this->_addButton('createBrandBlock', array(
        	'label'     => Mage::helper('adminhtml')->__('Create Brands Block'),
        	'onclick'   => "setLocation('".Mage::helper("adminhtml")->getUrl("adminhtml/brands/createBrandBlock",array())."')",
        	//'class'     => 'save',
        	), -100);
        
    }
    $this->_addButton('updatebrandsMenu', array(
    	'label'     => Mage::helper('adminhtml')->__('Menu Positions'),
    	'onclick'   => "setLocation('".Mage::helper("adminhtml")->getUrl("adminhtml/brands/updatePosition/",array())."')",
    	//'class'     => 'save',
    	), -100);


	parent::__construct();
	$this->removeButton('add');
  }
  
  public function getCmsBlockId() {
      $id = Mage::getModel('cms/block')->load('ocm-brands-block','identifier')->getId();
      if(!$id) return false;
      return $id;
  }
  
}