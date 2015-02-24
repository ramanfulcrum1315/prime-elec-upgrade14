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

class Camiloo_Amazonimport_SelectionController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout();
		return $this;
	}

	public function indexAction() {
                /* 
                   Bugfix for issue KDO-449-54740 - MN
                   Sometimes Magento skips over table creation during upgrade. 
                   Added new method 'checkTables' to Amazonimport - calling in place will eradicate issue.
                */
                Mage::getModel('amazonimport/amazonimport')->checkTables();
		$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);

		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_selection_marketplace'),'camiloo_amazon_selection_marketplace');
		$this->loadLayout();
		if($iview == true){
				
			$this->_addContent($this->getLayout()->createBlock('amazonimport/selection_gridheader'));
			$this->_addContent($this->getLayout()->createBlock('amazonimport/selection'));
		}
		$this->renderLayout();
	}
			
	public function masslistonamznoyesAction(){
		$count = 0;
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
		$listThisTable = $table_prefix."amazonimport_listthis_".$country;
		$setupTable = $table_prefix."amazonimport_setup_".$country;
		foreach($this->getRequest()->getPost('entity_id') as $productid){
			$result = $db->query("SELECT * FROM $listThisTable where productid=$productid");
			if($row = $result->fetch(PDO::FETCH_ASSOC)){
					$db->query("UPDATE $listThisTable SET is_active='1' where productid=$productid");
			}else{
					$db->query("INSERT INTO $listThisTable (`productid`,`is_active`) VALUES ($productid,1)");
			}
			$count++;
			
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__($count.' products have been made eligible for listing onto Amazon.'));
		$this->_redirect('*/*/');
	}
	public function masslistonamznonoAction(){$count = 0;
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
		$listThisTable = $table_prefix."amazonimport_listthis_".$country;
		$setupTable = $table_prefix."amazonimport_setup_".$country;
		foreach($this->getRequest()->getPost('entity_id') as $productid){
			if($productid != ""){
				$result = $db->query("SELECT * FROM $listThisTable where productid=$productid");
				if($row = $result->fetch(PDO::FETCH_ASSOC)){
						$db->query("UPDATE $listThisTable SET is_active='0' where productid=$productid");
				}else{
					$db->query("INSERT INTO $listThisTable (`productid`,`is_active`) VALUES ($productid,0)");
				}
				$count++;
			}			
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__($count.' products have had List On Amazon set to No. Please note that this action will not remove products from your Amazon account - for security reasons this must be done manually via Amazon Seller Central.'));
		$this->_redirect('*/*/');
	}
	public function masssetupasquickAction(){$count = 0;
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
		$listThisTable = $table_prefix."amazonimport_listthis_".$country;
		$setupTable = $table_prefix."amazonimport_setup_".$country;
		foreach($this->getRequest()->getPost('entity_id') as $productid){
			$result = $db->query("SELECT * FROM $setupTable where productid=$productid");
			if($row = $result->fetch(PDO::FETCH_ASSOC)){
					$db->query("UPDATE $setupTable SET setup_type='auto' where productid=$productid");
			}else{
				 	$db->query("INSERT INTO $setupTable (`productid`,`setup_type`,`initial_setup_complete`) VALUES ($productid,'auto',0)");
			}
			$count++;
			
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__($count.' products have been made available within the Find Products on Amazon wizard.'));
		$this->_redirect('*/*/');
	}
	public function masssetupasmanualAction(){
		$count = 0;
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
		$listThisTable = $table_prefix."amazonimport_listthis_".$country;
		$setupTable = $table_prefix."amazonimport_setup_".$country;
		foreach($this->getRequest()->getPost('entity_id') as $productid){
			$result = $db->query("SELECT * FROM $setupTable where productid=$productid");
			if($row = $result->fetch(PDO::FETCH_ASSOC)){
					$db->query("UPDATE $setupTable SET setup_type='manual' where productid=$productid");
			}else{
					$db->query("INSERT INTO $setupTable (`productid`,`setup_type`,`initial_setup_complete`) VALUES ($productid,'manual',0)");
			}
			$count++;
			
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__($count.' products have had setup type set to Advanced mode.'));
		$this->_redirect('*/*/');
	}
	public function massenablerepricingAction(){
		$count = 0;
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
		$listThisTable = $table_prefix."amazonimport_listthis_".$country;
		$setupTable = $table_prefix."amazonimport_setup_".$country;
		foreach($this->getRequest()->getPost('entity_id') as $productid){
			$minimumprice = Mage::getModel('catalog/product')->load($productid)->getPrice();
			$result = $db->query("SELECT * FROM $listThisTable where productid=$productid");
			if($row = $result->fetch(PDO::FETCH_ASSOC)){
					$db->query("UPDATE $listThisTable SET reprice_enabled='1' where productid=$productid");
			}else{
			 	$db->query("INSERT INTO $setupTable (`productid`,`reprice_enabled`,`minimum_price`) VALUES ($productid,1,'$minimumprice')");
			}
			
			// we have just enabled repricing, lets set up the calculated price value too.
		    Mage::getModel('amazonimport/amazonlink')->calculatePriceLevel(
											$productid, Mage::getStoreConfig('amazonint/amazon'.$country.'/store'), $country, true);
		
			
			
			$count++;
			
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__($count.' products have had repricing enabled. Please remember to check the minimum price settings. If you have not set these previously for safety these have been set to the current price within Magento.'));
		$this->_redirect('*/*/');
	}
	public function massdisablerepricingAction(){$count = 0;
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
		$listThisTable = $table_prefix."amazonimport_listthis_".$country;
		$setupTable = $table_prefix."amazonimport_setup_".$country;
		foreach($this->getRequest()->getPost('entity_id') as $productid){
			$result = $db->query("SELECT * FROM $listThisTable where productid=$productid");
			if($row = $result->fetch(PDO::FETCH_ASSOC)){
					$db->query("UPDATE $listThisTable SET reprice_enabled='0' where productid=$productid");
			}else{
				$db->query("INSERT INTO $setupTable (`productid`,`reprice_enabled`) VALUES ($productid,0)");
			}
			$count++;
			
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__($count.' products have had repricing disabled.'));
		$this->_redirect('*/*/');
	}
		
		
	public function editAction() {
		$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);

		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_selection_marketplace'),'camiloo_amazon_selection_marketplace');
		$this->loadLayout();
		if($iview == true){
			$this->_addContent($this->getLayout()->createBlock('amazonimport/selection_edit'));
			$this->_addLeft($this->getLayout()->createBlock('amazonimport/selection_edit_tabs'));
		}
		$this->renderLayout();
	}

	public function clonefromAction() {
		$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);

		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_selection_marketplace'),'camiloo_amazon_selection_marketplace');
		$this->loadLayout();
		if($iview == true){
			$this->_addContent($this->getLayout()->createBlock('amazonimport/selection_clone_edit'));
			$this->_addLeft($this->getLayout()->createBlock('amazonimport/selection_clone_edit_tabs'));
		}
		$this->renderLayout();
	}

	public function saveAction(){
		// Get current marketplace
		$mkt = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_selection_marketplace');
		// Save the marketplace value
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue($mkt, 'camiloo_amazon_selection_marketplace');
		// Get the POST request data
		$data = $this->getRequest()->getPost();
		
		// Boilerplate DB code
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		
		// first, we must check if there is a row for this product id (in listthis)
		$model = Mage::getModel('amazonimport/amazonimportlistthis'.$mkt)->getCollection()->addFieldToFilter('productid', array($data['productid']));
		if (sizeof($model) > 0) {
			foreach($model as $mdl){
				break;
			}
				
		}else{
			$mdl = Mage::getModel('amazonimport/amazonimportlistthis'.$mkt);
			$mdl->setData('productid',$data['productid']);
		}

		// Remove any pending requests for the product if it is no longer selected to list
		if($data['is_active'] == 0){
			$db->query("DELETE FROM {$table_prefix}amazonimport_errorlog_".$mkt." WHERE productid=".$data['productid']);
		}

		$mdl->setData('is_active', $data['is_active']);
		$mdl->setData('reprice_enabled', $data['reprice_enabled']);
		$mdl->setData('minimum_price', $data['minimum_price']);
		$mdl->save();

		// first time we elect to list this product, we will setup the setup row for it too.
		$saving = Mage::getModel('amazonimportsetup'.$mkt.'/amazonimportsetup'.$mkt)->getCollection()->addFieldToFilter('productid',$data['productid']);
		if (sizeof($saving) == 0)
		{
			$saving = Mage::getModel('amazonimportsetup'.$mkt.'/amazonimportsetup'.$mkt);
			$saving->setData('productid', $data['productid']);
			$saving->setData('initial_setup_complete',0);
			$saving->setData('setup_type', $data['setup_type']);
			$saving->save();
		}
		else {
			
			foreach ($saving as $s1) {
				$s1->setData('setup_type', $data['setup_type']);
				$s1->save();
				break;
			}
			
		}
		// ============== Register our interest in the ASIN ==============
		$setupdata = $saving->getData();
		try {
			$_asin = $setupdata[0]['asincode'];
			
			if ($_asin != "" && strlen($_asin) > 0) {
				
				Mage::getModel('amazonimport/reprice')->registerAsin($_asin, "new");
			}
		
		}
		catch (Exception $e) {
		}
		// ============== ============== ==============
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__('Settings have been updated'));
		$this->_redirect('*/*/');
	}

	public function comAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("com",'camiloo_amazon_selection_marketplace');
		$this->indexAction();
	}

	public function ukAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("uk",'camiloo_amazon_selection_marketplace');
		$this->indexAction();
	}

	public function frAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("fr",'camiloo_amazon_selection_marketplace');
		$this->indexAction();
	}

	public function deAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("de",'camiloo_amazon_selection_marketplace');
		$this->indexAction();
	}

	public function bulkprocessAction(){
		$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);

		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_selection_marketplace'),'camiloo_amazon_selection_marketplace');
		$this->loadLayout();
		if($iview == true){
			$this->_addContent($this->getLayout()->createBlock('amazonimport/selection_bulk_edit'));
			$this->_addLeft($this->getLayout()->createBlock('amazonimport/selection_bulk_edit_tabs'));
		}
		$this->renderLayout();
	}

}

?>