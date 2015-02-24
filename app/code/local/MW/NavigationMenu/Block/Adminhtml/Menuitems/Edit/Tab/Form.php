<?php

class MW_NavigationMenu_Block_Adminhtml_Menuitems_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('navigationmenu_form', array('legend'=>Mage::helper('navigationmenu')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('navigationmenu')->__('Category Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
      
      $fieldset->addField('url', 'text', array(
      		'label'     => Mage::helper('navigationmenu')->__('URL for Title'),
      		'name'      => 'url',
      		'required'  => false,
      ));

      $fieldset->addField('category_id', 'select', array(
      		'label'     => Mage::helper('navigationmenu')->__('Category'),
      		'name'      => 'category_id',
      		'required'  => false,
      		'options' => Mage::helper('navigationmenu/menuitems')->getCategoryOption()
      ));
      
      $fieldset->addField('order', 'text', array(
      		'label'     => Mage::helper('navigationmenu')->__('Order of Category on Menu'),
      		'name'      => 'order',
      		'class'     => 'validate-number',
      		'required'  => false,
      		'note'      => 'inserted value must differ to 0. Order must be unique.',
      ));
      
      $fieldset->addField('type', 'select', array(
      		'label'     => Mage::helper('navigationmenu')->__('Display Option'),
      		'name'      => 'type',
      		'required'  => false,
      		'options' => Mage::helper('navigationmenu/menuitems')->getItemsTypeOption()
      ));
      
      $fieldset->addField('column', 'text', array(
      		'label'     => Mage::helper('navigationmenu')->__('# of Columns on Expanded Menu'),
      		'name'      => 'column',
      		'class'     => 'validate-number',
      		'required'  => false,
      		'note'      => 'inserted value must differ to 0.',
      ));
      
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('navigationmenu')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('navigationmenu')->__('Enabled'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('navigationmenu')->__('Disabled'),
              ),
          ),
          'note'      => 'Enable and Save Item to Activate',
      ));
      
      if (!Mage::app()->isSingleStoreMode()) {
      	$fieldset->addField('store_ids', 'multiselect', array(
      			'name'      => 'store_ids[]',
      			'label'     => Mage::helper('navigationmenu')->__('Store View'),
      			'title'     => Mage::helper('navigationmenu')->__('Store View'),
      			'required'  => true,
      			'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
      	));
      }
     
      if ( Mage::getSingleton('adminhtml/session')->getNavigationMenuData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getNavigationMenuData());
          Mage::getSingleton('adminhtml/session')->setNavigationMenuData(null);
      } elseif ( Mage::registry('navigationmenu_data') ) {
          $form->setValues(Mage::registry('navigationmenu_data')->getData());
      }
      return parent::_prepareForm();
  }
}