 <?php

class OCM_Brands_Block_Adminhtml_Brands_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('brands_form', array('legend'=>Mage::helper('brands')->__('Brand Information')));
	  
	  //$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('add_variables' => false, 'add_widgets' => false,'files_browser_window_url'=>$this->getUrl().'admin/cms_wysiwyg_images/index/'));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('brands')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
		  'disabled'  => 'disabled',
      ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('brands')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('brands')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('brands')->__('Disabled'),
              ),
          ),
      ));
	  
       $fieldset->addField('featured', 'select', array(
          'label'     => Mage::helper('brands')->__('Featured'),
          'name'      => 'featured',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('brands')->__('Yes'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('brands')->__('No'),
              ),
          ),
      ));
      
      $fieldset->addField('logo', 'image', array(
          'label'     => Mage::helper('brands')->__('Featured Image'),
          'required'  => false,
          'name'      => 'logo',
	  ));
    
      $fieldset->addField('brand_content', 'editor', array(
          'name'      => 'brand_content',
          'label'     => Mage::helper('brands')->__('Content'),
          'title'     => Mage::helper('brands')->__('Content'),
          'style'     => 'width:700px; height:300px;',
          'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(array(
							//'add_variables' => false, 
							//'add_widgets' => false,


            'enabled'                       => 'enabled',
            'hidden'                        => 'hidden',
            'use_container'                 => false,
            'add_variables'                 => true,
            'add_widgets'                   => true,
            'no_display'                    => false,
            'translator'                    => Mage::helper('cms'),
            'encode_directives'             => true,
            'directives_url'                => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_wysiwyg/directive'),
            'popup_css'                     => Mage::getBaseUrl('js').'mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/dialog.css',
            'content_css'                   => Mage::getBaseUrl('js').'mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/content.css',
            'width'                         => '100%',
            'plugins'                       => array(),
            'files_browser_window_url' => Mage::helper('adminhtml')->getUrl().'cms_wysiwyg_images/index/'
						)),
          'wysiwyg'   => true,
          'required'  => true,
      ));


      if ( Mage::getSingleton('adminhtml/session')->getBrandsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getBrandsData());
          Mage::getSingleton('adminhtml/session')->setBrandsData(null);
      } elseif ( Mage::registry('brands_data') ) {
          $form->setValues(Mage::registry('brands_data')->getData());
      }
      return parent::_prepareForm();
  }
}