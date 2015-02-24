<?php

class OCM_Brands_Adminhtml_BrandsController extends Mage_Adminhtml_Controller_action
{

    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('brands/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        
        return $this;
    }   
 
    public function indexAction() {
        $grid = $this->getLayout()->createBlock('brands/adminhtml_brands');
        $this->_initAction();
        $this->getLayout()->getBlock('content')->append($grid,'brands_grid');
        $this->renderLayout();
    }

    public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('brands/brands')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('brands_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('brands/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('brands/adminhtml_brands_edit'))
                ->_addLeft($this->getLayout()->createBlock('brands/adminhtml_brands_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brands')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function updateBrandsListAction() {
        
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', Mage::helper('brands')->getBrandAttrCode());
        if ($attribute->usesSource()) {
            
            // get current brands Ids
            $collection = Mage::getModel('brands/brands')->getCollection();
            $current_brand_ids = array();
            foreach ($collection as $brand) {
                $current_brand_ids[$brand->getAttrValueId()] = $brand->getBrandsId();
            }

            $options = $attribute->getSource()->getAllOptions(false);
    
            foreach ($options as $option) {
                
                // check if option already exists in brand list
                //if (in_array($option['value'],$current_brand_ids)) continue;
                
                $data = array(
                    'attr_value_id' => $option['value'],
                    'title' => $option['label']
                );
        
                $model = Mage::getModel('brands/brands');       
                $model->setData($data);
                if( isset($current_brand_ids[$option['value']]) ) {
                    $model->setId($current_brand_ids[$option['value']]);
                }
            
                try {
                    if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                        $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                    } else {
                        $model->setUpdateTime(now());
                    }   
                
                    $model->save();
                    $this->updateUrlRewrite($option['value'],$option['label']);

                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('brands')->__('Update Complete'));
            $this->_redirect('*/*/');
        }
    }
 
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            
            if(isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '') {
                try {   
                    /* Starting upload */   
                    $uploader = new Varien_File_Uploader('logo');
                    
                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    
                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //  (file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);
                            
                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS . 'brands' . DS ;
                    $uploader->save($path, $_FILES['logo']['name'] );
                    
                } catch (Exception $e) {
              
                }
            
                //this way the name is saved in DB
                $data['logo'] = 'brands' . DS . $_FILES['logo']['name'];
            } else {       
                if(isset($data['logo']['delete']) && $data['logo']['delete'] == 1)
                    $data['logo'] = '';
                else
                    unset($data['logo']);
            }
            
            //die(print_r($data));
                
            $model = Mage::getModel('brands/brands');
            /*       
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
            */
            $model->load($this->getRequest()->getParam('id'));
            foreach ($data as $key => $val) {
                $model->setData($key,$val);
            }
            
            
            $url_desc = ($data['meta_title']) ? $data['meta_title']:$model->getData('title');
            
            try {
                if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }   
                
                $model->save();
                $this->updateUrlRewrite($model->getData('attr_value_id'),$model->getData('title'),$url_desc);
                            
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('brands')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/');
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brands')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }
    
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('brands/brands');
                 
                $model->load($this->getRequest()->getParam('id'));
                $this->removeUrlRewrite($model->getAttrValueId());
                $model->delete();
                     
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $brandsIds = $this->getRequest()->getParam('brands');
        if(!is_array($brandsIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($brandsIds as $brandsId) {
                    $model = Mage::getModel('brands/brands')->load($brandsId);
                    $this->removeUrlRewrite($model->getAttrValueId());
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($brandsIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function updatePositionAction() {
        
        
        $id_array = $this->getRequest()->getParam('brands');
        $collection = Mage::getModel('brands/brands')->getcollection()
            //->addFieldToFilter('dfdfd',array(
            //    array(  'attribute'=>'brands_id' , 'in'=>array($id_array)  ),
            //    array( 'attribute'=>'show_in_menu' , 'eq'=>'1' )
            //    ))
            ;
        $collection->getSelect()->where('brands_id IN (?) OR show_in_menu = 1',$id_array);
        $collection->setOrder('menu_position', 'ASC');
        
        //die($collection->getSelect());
        
        if ($collection->getSize()) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            Mage::register('brands_collection', $collection);

            $this->loadLayout();
            $this->_setActiveMenu('brands/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Position Update'), Mage::helper('adminhtml')->__('Position Update'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('brands/adminhtml_brands_updateposition'))
                ->_addLeft($this->getLayout()->createBlock('brands/adminhtml_brands_updateposition_tabs'))
                ;

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brands')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
        
    }

    public function savePositionAction() {
        if ($data = $this->getRequest()->getPost()) {
            foreach ( $data as $id => $position) {
                
                $item = Mage::getModel('brands/brands')->load($id);
                
                if (!$item->getBrandsId()) continue;
                
                if($position < 0) {
                    $item->setShowInMenu(0)->save();
                } else {
                    $item->setMenuPosition($position)->setShowInMenu(1)->save();
                }
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('brands')->__('Your Menu has been updated'));
            $this->_redirect('*/*/');
        }
    }

    public function massPositionAction() {
        if ($post = $this->getRequest()->getPost()) {
            if($post['action'] == 1) {
                foreach($post['brands'] as $id) {
                
                    $item = Mage::getModel('brands/brands')->load($id);
                    if (!$item->getBrandsId()) continue;
                    
                    $item->setShowInMenu(1)->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('brands')->__('Your Menu Items have been added'));
                $this->_redirect('*/*/updatePosition');
                return;
            }
            
            if($post['action'] == 0) {
                $item->setShowInMenu(0)->save();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('brands')->__('Your Menu has been updated'));
            $this->_redirect('*/*/');
        }
    }
    
    
    public function massStatusAction() {
        $brandsIds = $this->getRequest()->getParam('brands');
        if(!is_array($brandsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($brandsIds as $brandsId) {
                    $brands = Mage::getSingleton('brands/brands')
                        ->load($brandsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($brandsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction() {
        $fileName   = 'brands.csv';
        $content    = $this->getLayout()->createBlock('brands/adminhtml_brands_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'brands.xml';
        $content    = $this->getLayout()->createBlock('brands/adminhtml_brands_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
    
    public function createBrandBlockAction() {

        $id = Mage::getModel('cms/block')->load('ocm-brands-block','identifier')->getId();
        if(!$id) {
        
        Mage::getModel('cms/block')
            ->setTitle('Brands')
            ->setContent('Here is brands content')
            ->setIdentifier('ocm-brands-block')
            ->setIsActive(false)
            ->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('brands')->__('Brand Block Created'));
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brands')->__('Brand Block Already Exists'));
        }

        $this->_redirect('*/*/');
        
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    private function updateUrlRewrite($id,$title,$url_desc=null){
    $helper = Mage::helper('brands');
        $id_path = Mage::helper('brands')->getUrlRewritePath($id);
        $url_model = Mage::getModel('core/url_rewrite');
        $url_model->load($id_path,'id_path');
        $url_model
            ->setStoreId('0')
            ->setIdPath($id_path)
            ->setRequestPath('brands/'.$helper->formatUrlKey($title))
            ->setTargetPath($helper->getBrandUri($id,$title))
            ->setIsSystem(0)
        ;
        if ($url_desc) $url_model->setDescription($url_desc);

        $url_model->save();
        return $this;
    } 
    private function removeUrlRewrite($id){
        $id_path = Mage::helper('brands')->getUrlRewritePath($id);
        $url_model = Mage::getModel('core/url_rewrite');
        $url_model->load($id_path,'id_path');
        $url_model->delete();
        return $this;
    } 



}