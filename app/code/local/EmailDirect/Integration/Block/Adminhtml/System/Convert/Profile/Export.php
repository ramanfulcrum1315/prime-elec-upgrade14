<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profiles run block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Emaildirect_Integration_Block_Adminhtml_System_Convert_Profile_Export extends Mage_Adminhtml_Block_Abstract
{
   public function __construct()
	{
		parent::__construct();
		$this->_init();
	}
	protected function _init()
	{
		$products   = Mage::getModel('catalog/product')->getCollection();
		
		if (Mage::helper('emaildirect')->exportConfig('include_disabled') == false)
			$products->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
    	
    	$importData = array_map('intval',$products->getAllIds());
		$this->setBatchItemsCount(count($importData));
		
		$this->setBatchConfig(
                    array(
                        'styles' => array(
                            'error' => array(
                                'icon' => Mage::getDesign()->getSkinUrl('images/error_msg_icon.gif'),
                                'bg'   => '#FDD'
                            ),
                            'message' => array(
                                'icon' => Mage::getDesign()->getSkinUrl('images/fam_bullet_success.gif'),
                                'bg'   => '#DDF'
                            ),
                            'loader'  => Mage::getDesign()->getSkinUrl('images/ajax-loader.gif')
                        ),
                        'template' => '<li style="#{style}" id="#{id}">'
                                    . '<img id="#{id}_img" src="#{image}" class="v-middle" style="margin-right:5px"/>'
                                    . '<span id="#{id}_status" class="text">#{text}</span>'
                                    . '</li>',
                        'text'     => $this->__('Processed <strong>%s%% %s/%d</strong> records', '#{percent}', '#{updated}', $this->getBatchItemsCount()),
                        'uploadText'  => $this->__('Sending file to Emaildirect...'),
                        'successText'  => $this->__('Exported <strong>%s</strong> records', '#{updated}')
                    )
                );
		
		$this->setImportData($importData);
		
		if (Mage::helper('emaildirect')->exportConfig('send_to_emaildirect') == true)
			$this->setUploadStatus('true');
		else
			$this->setUploadStatus('false');
		$this->setBatchSize(Mage::helper('emaildirect')->exportConfig('batch'));
	}
	
	public function getBatchConfigJson()
	{
		return Mage::helper('core')->jsonEncode(
            $this->getBatchConfig()
		);
	}

	public function jsonEncode($source)
	{
		return Mage::helper('core')->jsonEncode($source);
	}

	public function getFormKey()
	{
		return Mage::getSingleton('core/session')->getFormKey();
	}
}
