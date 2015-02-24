 <?php

class OCM_Brands_Block_Adminhtml_Brands_Updateposition_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('brands_form', array('legend'=>Mage::helper('brands')->__('Menu Positions')));
	  
      if($brands = Mage::registry('brands_collection')) {
      
          foreach ($brands as $item) {
              $fieldset->addField($item->getId(), 'text', array(
                  'label'     => Mage::helper('brands')->__($item->getTitle()),
                  'name'      => $item->getId(),
                  'value'     => $item->getMenuPosition()
              ));
          }
      }
	  
      

      return parent::_prepareForm();
  }
}