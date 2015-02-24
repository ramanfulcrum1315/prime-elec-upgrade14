<?php
class OCM_Brands_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {			
		$this->loadLayout();     
		$this->getLayout()->getBlock('head')
		  ->setTitle(Mage::helper('brands')->getMetaTitle())
		  ->setKeywords(Mage::helper('brands')->getMetaKeywords())
		  ->setDescription(Mage::helper('brands')->getMetaDescription())
		;
		$this->renderLayout();
    }
}