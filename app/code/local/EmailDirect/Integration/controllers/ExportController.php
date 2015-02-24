<?php
class EmailDirect_Integration_ExportController extends Mage_Core_Controller_Front_Action
{
	public function downloadAction()
	{
		$file_name = "emaildirect_products_" . $this->getRequest()->getParam('filename');
		$file = Mage::helper('emaildirect')->getExportFileName($file_name);

		$this->_prepareDownloadResponse(Mage::helper('emaildirect')->getExportFileName($file_name,false), file_get_contents($file));
	}
}