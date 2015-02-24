<?php

class MW_NavigationMenu_Block_Adminhtml_Contents_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('navigationmenu_form', array('legend'=>Mage::helper('navigationmenu')->__('Item information')));
     
      $fieldset->addField('menuitem_id', 'select', array(
      		'label'     => Mage::helper('navigationmenu')->__('Menu Category Title'),
      		'name'      => 'menuitem_id',
      		'required'  => true,
      		'options' => Mage::helper('navigationmenu/contents')->getMenuItemOption()
      ));
      
      $fieldset->addField('name', 'text', array(
      		'label'     => Mage::helper('navigationmenu')->__('Menu Content Name'),
      		'class'     => 'required-entry',
      		'required'  => true,
      		'name'      => 'name',
      ));
      
      $fieldset->addField('position', 'select', array(
      		'label'     => Mage::helper('navigationmenu')->__('Position of Content'),
      		'name'      => 'position',
      		'required'  => true,
      		'options' => Mage::helper('navigationmenu/contents')->getContentsPositionOption()
      ));
      
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('navigationmenu')->__('Menu Content Title'),
          'required'  => false,
          'name'      => 'title',
      	  'note'	  => 'To display at top of content block',
      ));

      $fieldset->addField('text', 'text', array(
          'label'     => Mage::helper('navigationmenu')->__('Content Text'),
          'required'  => false,
          'name'      => 'text',
      	  'note'	  => 'To be shown beneath Title',
      ));
      
      $fieldset->addField('image', 'image', array(
      		'label'     => Mage::helper('navigationmenu')->__('Image'),
      		'required'  => false,
      		'name'      => 'image',
      ));
      
      $fieldset->addField('sku', 'text', array(
      		'label'     => Mage::helper('navigationmenu')->__('Product SKU'),
      		'name'      => 'sku',
      		'required'  => false,
      		'note'	  => 'Image, name, price and short description of product will be shown',
      ));
      
      $fieldset->addField('block_id', 'select', array(
      		'label'     => Mage::helper('navigationmenu')->__('Block'),
      		'name'      => 'block_id',
      		'required'  => false,
      		'options' => Mage::helper('navigationmenu/contents')->getStaticBlockOption(),
      		'note'	  => 'Static block content is managed under CMS / Static Block',
      ));
      
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('navigationmenu')->__('Status'),
          'name'      => 'status',
      	  'note'	  => 'Enable and Save Item to Activate',
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