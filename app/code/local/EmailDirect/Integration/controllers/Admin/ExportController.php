<?php

class Emaildirect_Integration_Admin_ExportController extends Mage_Adminhtml_Controller_Action
{
	protected $_min_category_level = 2;


	private function setConfigValue($name)
	{
		$old_value = Mage::helper('emaildirect')->exportConfig($name);
		
		$new_value = $this->getRequest()->getParam($name);
		
		if ($new_value != '' && $new_value != $old_value)
		{
			Mage::getConfig()->saveConfig("emaildirect/export/{$name}", $new_value,"default","default");
			return true;
		}
		
		return false;
	}

	private function setConfiguration()
	{
		$config_options = array('send_to_emaildirect','include_disabled');
		
		$changed = false;
		
		foreach ($config_options as $option)
		{
			if ($this->setConfigValue($option))
				$changed = true;
		}
		
		if ($changed)
		{
			Mage::getConfig()->cleanCache();
			Mage::getConfig()->reinit();
         Mage::app()->reinitStores();
		}
	}

	public function runAction()
	{
		// Update export configuration options if changed before button click
		$this->setConfiguration();
		
		$this->loadLayout();
		$this->renderLayout();
	}
	 
	private function getCategoryPath($category)
	{
		$name = "";
		
		while ($category->parent_id != 0 && $category->level >= $this->_min_category_level)
		{
			if ($name != "")
				$name = $category->getName() . "/{$name}";
			else
				$name = $category->getName();
			
			$category = $category->getParentCategory();
		}
		
		return $name;
	}
	 
	private function getProductData($id)
	{
		$product = Mage::getModel('catalog/product')->load($id);

		$product_data = array($product->getName(), $product->getSku());
			
		$product_categories = $product->getCategoryCollection()->exportToArray();
		
		$category_data = array();
		
		foreach($product_categories as $cat)
		{
			$category = Mage::getModel('catalog/category')->load($cat['entity_id']);
			
			$category_data[] = $this->getCategoryPath($category);
		}

		if (count($category_data) > 0)
			$product_data[] = implode(",",$category_data);
		else
			$product_data[] = "";
		
		return $product_data;
	}
	
	private function saveRow($fields,$name)
	{
		$file = Mage::helper('emaildirect')->getExportFileName($name);
		
		if (file_exists($file))
		{
			$fp = fopen($file, 'a');
		}
		else
		{
			$header_fields = array('Product Name','SKU','Root Category');
			
			$fp = fopen($file, 'w');
			fputcsv($fp, $header_fields, ',','"');
		}

		fputcsv($fp, $fields, ',','"');
		
		fclose($fp);
	}
	
	private function getMinCategoryLevel()
	{
		$roots = Mage::getModel('catalog/category')->load(1)->getChildren();
		
		if (strpos($roots,',') === FALSE)
			return 2;
		return 1;
	}

	public function batchRunAction()
	{
		$this->_min_category_level = $this->getMinCategoryLevel();
		
		if ($this->getRequest()->isPost()) 
		{
			$product_id = $this->getRequest()->getPost('id', 0);
			$file_name = $this->getRequest()->getPost('filename', 0);
			
			//Mage::log($product_id,null,'1_export.log');
			
			if (is_array($product_id))
			{
				foreach ($product_id as $id)
				{
					$csv_data = $this->getProductData($id);

					$this->saveRow($csv_data,"emaildirect_products_{$file_name}");
				}
				
				$result = array(
                'savedRows' => count($product_id),
                'errors'    => array()
            	);
			}
			else
			{
				$csv_data = $this->getProductData($product_id);

				$this->saveRow($csv_data,"emaildirect_products_{$file_name}");
			
			
				$result = array(
                'savedRows' => 1,
                'errors'    => array()
            	);
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}
	
	public function downloadAction()
	{
		$file_name = "emaildirect_products_" . $this->getRequest()->getParam('filename');
		$file = Mage::helper('emaildirect')->getExportFileName($file_name);

		$this->_prepareDownloadResponse(Mage::helper('emaildirect')->getExportFileName($file_name,false), file_get_contents($file));
	}

	public function batchFinishAction()
	{
		if ($this->getRequest()->isPost()) 
		{
			$file_name = $this->getRequest()->getPost('filename', 0);

			$url = $this->getUrl('*/*/download') . "filename/{$file_name}";
			
			$result = array(
                'download_link' => $url,
            );
		
			if (Mage::helper('emaildirect')->exportConfig('send_to_emaildirect') == true)
			{
				$ed_url = $this->getUrl('emaildirect/export/download') . "filename/{$file_name}";
				$api = Mage::getSingleton('emaildirect/wrapper_ftp');
				$rc = $api->upload($ed_url,"magento_products_{$file_name}.csv");
			
				//Mage::log($rc,null,'1_export.log');
			
				if (isset($rc->ErrorCode))
				{
					$result['error'] = "EmailDirect Error: (" . (string) $rc->ErrorCode . "): " . (string)$rc->Message;
				}
			}
			
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
		
	}
/*
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('admin/system/convert/profiles');
	}*/
}
