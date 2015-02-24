<?php

class MW_NavigationMenu_Adminhtml_ContentsController extends Mage_Adminhtml_Controller_action
{
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('navigationmenu/contents')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Contents Manager'), Mage::helper('adminhtml')->__('Content Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('navigationmenu/contents')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('navigationmenu_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('navigationmenu/contents');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Content Manager'), Mage::helper('adminhtml')->__('Content Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Content News'), Mage::helper('adminhtml')->__('Content News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('navigationmenu/adminhtml_contents_edit'))
				->_addLeft($this->getLayout()->createBlock('navigationmenu/adminhtml_contents_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('navigationmenu')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
				try {
					/* Starting upload */
					$uploader = new Varien_File_Uploader('image');
						
					// Any extention would work
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
						
					// Set the file upload mode
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
						
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS . "mw_navigationmenu".DS;
					$uploader->save($path, $_FILES['image']['name'] );
						
				} catch (Exception $e) {
			
				}
				 
				//this way the name is saved in DB
				$data['image'] = "mw_navigationmenu/".$_FILES['image']['name'];
			}else{
				if(isset($data['image']['delete']) && $data['image']['delete']==1){
					$data['image']="";
				}else{
					unset($data['image']);
				}
			}
			
			$model = Mage::getModel('navigationmenu/contents');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			$model->setStoreIds(implode(',', $data['store_ids']));
			try {
				$model->setStoreIds(implode(',', $data['store_ids']));
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('navigationmenu')->__('Content was successfully saved'));
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
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('navigationmenu')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('navigationmenu/contents');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
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
        $navigationmenuIds = $this->getRequest()->getParam('navigationmenu');
        if(!is_array($navigationmenuIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($navigationmenuIds as $navigationmenuId) {
                    $navigationmenu = Mage::getModel('navigationmenu/contents')->load($navigationmenuId);
                    $navigationmenu->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($navigationmenuIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $navigationmenuIds = $this->getRequest()->getParam('navigationmenu');
        if(!is_array($navigationmenuIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($navigationmenuIds as $navigationmenuId) {
                    $navigationmenu = Mage::getSingleton('navigationmenu/contents')
                        ->load($navigationmenuId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($navigationmenuIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'navigationmenu.csv';
        $content    = $this->getLayout()->createBlock('navigationmenu/adminhtml_contents_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'navigationmenu.xml';
        $content    = $this->getLayout()->createBlock('navigationmenu/adminhtml_contents_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
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
}