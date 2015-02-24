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

class Camiloo_Amazonimport_LicensingController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout();	
		return $this;
	}   
	
	public function indexAction() {
		
		if(isset($_REQUEST['activatetrial'])){
			$this->getActivateTrialsPending();
			Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(Mage_Adminhtml_Block_Page_Menu::CACHE_TAGS));	
			$this->loadLayout();
			$this->_addContent($this->getLayout()->createBlock('amazonimport/licensing_edit_tab_welcome'));
			$this->renderLayout();	
			
		}else if(isset($_SESSION['postupgrade'])){
			unset($_SESSION['postupgrade']);
			Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(Mage_Adminhtml_Block_Page_Menu::CACHE_TAGS));	
			$this->loadLayout();
			$this->_addContent($this->getLayout()->createBlock('amazonimport/licensing_edit_tab_welcome'));
			$this->renderLayout();	
		}else if(isset($_REQUEST['refreshcaches'])){
			$this->loadLayout();
			$this->_addContent($this->getLayout()->createBlock('amazonimport/licensing_edit_tab_welcome'));
			$this->renderLayout();	
		}else{
			$this->getActivateTrialsPending();
		    Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(Mage_Adminhtml_Block_Page_Menu::CACHE_TAGS));	
			$this->loadLayout();
			$this->_addContent($this->getLayout()->createBlock('amazonimport/licensing_edit_tab_store'));
			$this->renderLayout();	
		}	
		
	}
	
	public function refreshLicenseAction(){
		
		echo Mage::getModel('amazonimport/amazonlink')->refreshLicensedisplay();
	       
	}
	
	public function updateamzcachesAction(){
	
	 	$amazoncore = Mage::getModel('amazonimport/amazonimport');		
		$amazoncore->updateCache();
		echo "<script type='text/javascript'>parent.doneshowthanks();</script>";
			
	}
	
	
	public function getActivateTrialsPending(){
		// this function gets the requested data
		$session = curl_init("http://service.camiloo.co.uk/activate_amazon_trials.php");
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($session, CURLOPT_TIMEOUT, 60);
		$result = curl_exec($session);
		curl_close($session);
		return $result;
	}
	
}
?>