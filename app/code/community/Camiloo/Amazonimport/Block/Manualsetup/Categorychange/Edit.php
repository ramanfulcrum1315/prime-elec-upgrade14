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
 
class Camiloo_Amazonimport_Block_Manualsetup_Categorychange_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId    = 'entity_id';
        $this->_controller  = 'manualsetup';
        $this->_mode        = 'edit';

        parent::__construct();
        $this->setTemplate('amazonimport/manualsetup/categorychange/edit.phtml');
    }

    protected function _prepareLayout()
    {
		 $this->setChild('form',
            $this->getLayout()->createBlock('amazonimport/manualsetup/categorychange_edit_form', 'form')
        );
		 
        return parent::_prepareLayout();
    }
}
