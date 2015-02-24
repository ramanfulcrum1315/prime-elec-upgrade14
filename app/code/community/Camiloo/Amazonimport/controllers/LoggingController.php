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

class Camiloo_Amazonimport_LoggingController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout();
		return $this;
	}

	public function indexAction() {

		// safety feature.
		// if comm log is more than 200 rows, we'll truncate. in future this will
		// be carefully managed by the logging itself.
		
		if(Mage::getModel('amazonimport/amazonimportlog')->getCollection()->getSize() > 250){
			$db = Mage::getSingleton("core/resource")->getConnection("core_write");
			$table_prefix = Mage::getConfig()->getTablePrefix();		
			$_sql = "TRUNCATE TABLE {$table_prefix}amazonimport_log";
			$db->query($_sql);
		}
	
		
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('amazonimport/logging'));
		$this->renderLayout();


	}
	
	function manualcronAction(){
		ini_set("display_errors",1);

		Mage::getModel('amazonimport/amazonimport')->limitLoggingHelper();
		$model = Mage::getModel('amazonimport/amazonimportlog');
		$model->setOutgoing("Running timed job manually...");
		$model->setIncoming("");
		$model->setError("");
		$model->setMessageTime(date("c"));
		$model->save();
		$start = time();

		Mage::getModel('amazonimport/timedjobscheduler')->runeverything();

		$seconds = time() - $start;
		Mage::getModel('amazonimport/amazonimport')->limitLoggingHelper();
		$model = Mage::getModel('amazonimport/amazonimportlog');
		$model->setOutgoing("Run timed job manually completed in $seconds seconds");
		$model->setIncoming("");
		$model->setError("");
		$model->setMessageTime(date("c"));
		$model->save();

		Mage::getSingleton('adminhtml/session')->addSuccess('Called to Amazon successfully');
		$this->_redirect('*/*/');
	
	}
	public function supportAction() {
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('amazonimport/logging_edit'));
		$this->_addLeft($this->getLayout()->createBlock('amazonimport/logging_edit_tabs'));
		$this->renderLayout();
	}

	public function editAction() {
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('amazonimport/logging_edit'));
		$this->_addLeft($this->getLayout()->createBlock('amazonimport/logging_edit_tabs'));
		$this->renderLayout();
	}

	public function flushlogAction(){

		foreach(Mage::getModel('amazonimport/amazonimportlog')->getCollection() as $log){
			$log->delete();
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__('The log has been cleared.'));
		$this->_redirect('*/*/');
	}


	public function resetsurestreamAction(){
		Mage::getModel('amazonimport/amazonimport')->limitLoggingHelper();
		$model = Mage::getModel('amazonimport/amazonimportlog');
		$model->setOutgoing("Surestream was reset.");
		$model->setIncoming("");
		$model->setError("");
		$model->setMessageTime(date("c"));
		$model->save();

		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$db->query("UPDATE {$table_prefix}amazonimport_surestream SET running_flag=0, state='WaitingToSubmitProductFeed'");
			
		$db->query("UPDATE {$table_prefix}amazonimport_errorlog_com SET messageid=0 WHERE messageid != 0 AND result=''");
		$db->query("UPDATE {$table_prefix}amazonimport_errorlog_uk SET messageid=0 WHERE messageid != 0 AND result=''");
		$db->query("UPDATE {$table_prefix}amazonimport_errorlog_de SET messageid=0 WHERE messageid != 0 AND result=''");
		$db->query("UPDATE {$table_prefix}amazonimport_errorlog_fr SET messageid=0 WHERE messageid != 0 AND result=''");
			
			
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__('Surestream has been reset.'));
		$this->_redirect('*/*/');
	}



	public function saveAction() {
			
		$data = $this->getRequest()->getPost();

		$message = "Support Request from ".$data['customer_name'].":\n\r";
		$message .= "===============================================\n\r\n\r";
		$message .= $data['support_query']."\n\r\n\r";

		$message .= "Access Credentials:\n\r";
		$message .= "===============================================\n\r\n\r";
		$message .= "FTP is at:  ".$data['ftp_address']."\n\r";
		$message .= "FTP username:  ".$data['ftp_username']."\n\r";
		$message .= "FTP password:  ".$data['ftp_password']."\n\r";
		$message .= "Magento Admin is at:  ".$data['admin_url']."\n\r";
		$message .= "Magento Username:  ".$data['admin_username']."\n\r";
		$message .= "Magento Password:  ".$data['admin_password']."\n\r\n\r";

		$message .= "Module Version\n\r";
		$message .= "===============================================\n\r\n\r";
		$message .= "2.14\n\r\n\r";

		$message .= "Surestream States\n\r";
		$message .= "===============================================\n\r\n\r";

		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_surestream");
		while($row = $result->fetch()){
			$message .= $row['marketplace'].":\n\r  ".serialize($row)."\n\r";
		}

		$message .= "\n\r";
	  

		if(isset($data['outgoing'])){
			 
			$message .= "Attached Log Message\n\r";
			$message .= "===============================================\n\r\n\r";
			$message .= "Outgoing Message:\n\r  ".$data['outgoing']."\n\r";
			$message .= "Incoming Message:\n\r  ".$data['incoming']."\n\r";
			$message .= "Error Reported:\n\r ".$data['error']."\n\r";
			$message .= "Message Timestamp:\n\r ".$data['message_time']."\n\r\n\r";
			 
		}

		$message .= "Server Variable Output\n\r";
		$message .= "===============================================\n\r\n\r";
		 

		foreach($_SERVER as $svkey=>$svval){
			$message .= "$svkey: $svval\n\r";
		}

		mail("support@camiloo.co.uk","Support Request - CAMAMUK200",$message,"From: ".$data['customer_name']." <".$data['customer_email'].">");
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__('Thank you - your request has been sent to Camiloo Support. A member of our support team will respond within 2 business days.'));
		$this->_redirect('*/*/');

	}

}

?>