 <?php

class OCM_Brands_Block_Adminhtml_Brands_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm() {
  
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('brands_form', array('legend'=>Mage::helper('brands')->__('Meta Data')));
	  
     
      $fieldset->addField('meta_title', 'text', array(
          'label'     => Mage::helper('brands')->__('Meta Page Title'),
          'name'      => 'meta_title',
      ));
		
        $fieldset->addField('meta_description', 'textarea', array(
            'name'      => 'meta_description',
            'label'     => Mage::helper('cms')->__('Meta Description'),
            'title'     => Mage::helper('cms')->__('Meta Description'),
        ));

        $fieldset->addField('meta_keywords', 'textarea', array(
            'name'      => 'meta_keywords',
            'label'     => Mage::helper('cms')->__('Meta Keywords'),
            'title'     => Mage::helper('cms')->__('Meta Keywords'),
        ));


      if ( Mage::getSingleton('adminhtml/session')->getBrandsData() ) {
      
          $form->setValues(Mage::getSingleton('adminhtml/session')->getBrandsData());
          Mage::getSingleton('adminhtml/session')->setBrandsData(null);
          
      } elseif ( Mage::registry('brands_data') ) {
      
          $form->setValues(Mage::registry('brands_data')->getData());
          
      }
      return parent::_prepareForm();
  }
}