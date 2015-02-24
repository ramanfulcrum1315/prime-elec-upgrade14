<?php

class Camiloo_Amazonimport_Block_Manualsetup_Categorychange_Edit_Form extends Mage_Adminhtml_Block_Catalog_Category_Abstract
{
    /**
     * Additional buttons on category page
     *
     * @var array
     */
    protected $_additionalButtons = array();

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amazonimport/manualsetup/categorychange/edit/form.phtml');
    }
	
	public function getSaveUrl(array $args = array())
	{
		$params = array('_current'=>true);
		$params = array_merge($params, $args);
		return $this->getUrl('*/*/save', $params);
	}

	protected function _prepareLayout()
    {
        $categoryId = 5; // 0 when we create category, otherwise some value for editing category

		// TODO: Decide if tabs are needed here or not.
        $this->setChild('tabs',
            $this->getLayout()->createBlock('amazonimport/manualsetup_categorychange_edit_tabs', 'tabs')
        );

        // Save button
       /* if (!$category->isReadonly()) {
            $this->setChild('save_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('catalog')->__('Save Mapping'),
                        'onclick'   => "categorySubmit('" . $this->getSaveUrl() . "', true)",
                        'class' => 'save'
                    ))
            );
        }*/

      
        return parent::_prepareLayout();
    }

    public function getStoreConfigurationUrl()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        $params = array();
//        $params = array('section'=>'catalog');
        if ($storeId) {
            $store = Mage::app()->getStore($storeId);
            $params['website'] = $store->getWebsite()->getCode();
            $params['store']   = $store->getCode();
        }
        return $this->getUrl('*/system_store', $params);
    }

    public function getSaveButtonHtml()
    {
        if ($this->hasStoreRootCategory()) {
            return $this->getChildHtml('save_button');
        }
        return '';
    }

    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    public function getHeader()
    {
       if ($this->getCategoryId()) {
                return Mage::helper('catalog')->__('Categorise selected products');
            } else {
                   return Mage::helper('catalog')->__('We should never see this on the screen');
            }
       }

  
    /**
     * Return URL for refresh input element 'path' in form
     *
     * @param array $args
     * @return string
     */
    public function getRefreshPathUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/refreshPath', $params);
    }

    public function getProductsJson()
    {
        $products = $this->getCategory()->getProductsPosition();
        if (!empty($products)) {
            return Mage::helper('core')->jsonEncode($products);
        }
        return '{}';
    }

    public function isAjax()
    {
        return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
    }
}