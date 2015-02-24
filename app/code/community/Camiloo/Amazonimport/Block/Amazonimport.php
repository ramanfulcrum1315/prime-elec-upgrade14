<?php
class Camiloo_Amazonimport_Block_Amazonimport extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getAmazonimport()     
     { 
        if (!$this->hasData('amazonimport')) {
            $this->setData('amazonimport', Mage::registry('amazonimport'));
        }
        return $this->getData('amazonimport');
        
    }
}