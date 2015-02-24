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

class Camiloo_Amazonimport_Block_Selection_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('selection_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('amazonimport')->__(''));
	}

	protected function _beforeToHtml()
	{
		$this->addTab('settings', array(
          'label'     => Mage::helper('amazonimport')->__('Settings'),
          'title'     => Mage::helper('amazonimport')->__('Settings'),
          'content'   => $this->getLayout()->createBlock('amazonimport/selection_edit_tab_default')->toHtml(),
		));
		 

		$mdl = "";
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('amazonimport/amazonimportlistthis'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_selection_marketplace'))->getCollection()->addFieldToFilter('productid',array($id));
		if(sizeof($model) > 0){
			foreach($model as $mdl){
				break;
			}
		}

		if(is_object($mdl)){
				

			if($mdl->getIsOnAmazon() == 1){
				$this->addTab('preview', array(
		          'label'     => Mage::helper('amazonimport')->__('Live Listing Preview'),
		          'title'     => Mage::helper('amazonimport')->__('Live Listing Preview'),
		          'content'   => $this->getLayout()->createBlock('amazonimport/selection_edit_tab_preview')->toHtml(),
				));
				 

			}
		}

		return parent::_beforeToHtml();
	}
}