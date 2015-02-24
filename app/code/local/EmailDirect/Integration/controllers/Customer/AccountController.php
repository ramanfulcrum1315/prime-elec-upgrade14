<?php
class EmailDirect_Integration_Customer_AccountController extends Mage_Core_Controller_Front_Action
{

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        if (!$this->_getCustomerSession()->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

	/**
	 * Display data
	 */
	public function indexAction()
	{
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('Newsletter Subscription'));
        $this->renderLayout();
	}
	
	public function saveadditionalAction()
	{
//		Mage::log(__METHOD__);
		$customerSession = Mage::getSingleton('customer/session');
		
		if($this->getRequest()->isPost()){

			//<state> param is an html serialized field containing the default form state
			//before submission, we need to parse it as a request in order to save it to $odata and process it
			parse_str($this->getRequest()->getPost('state'), $odata);
			
			$curlists = (TRUE === array_key_exists('list', $odata)) ? $odata['list'] : array();
			$lists    = $this->getRequest()->getPost('list', array());
			$customer  = Mage::helper('customer')->getCustomer();
			$email     = $customer->getEmail();
			
			// Manage the main publication and subscription
			$publication = (TRUE === array_key_exists('publication', $odata)) ? $odata['publication'] : array();
			$pub_selection    = $this->getRequest()->getPost('publication', array());
			$general = Mage::helper('emaildirect')->config('publication');
			
			$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
			$subscriber->setImportMode(false);
			$rc = Mage::getSingleton('emaildirect/wrapper_publications')->getPublication($general);
			$subscriber->setListName((string)$rc->Name);



			if(!$pub_selection) {
				Mage::getSingleton('emaildirect/wrapper_publications')->unsubscribe($general,$email);
				$subscriber->unsubscribe();
				// unsuscribe for all the lists
				foreach($curlists as $listId => $list){
					Mage::getSingleton('emaildirect/wrapper_lists')->listUnsubscribe($listId, $email);
				}
				$this->_redirect('*/*/index');
			}
			elseif($publication != $pub_selection) {
				if($subscriber->isObjectNew()) {
					$mergeVars = Mage::getModel('emaildirect/observer')->_mergeVars($subscriber);
					Mage::getSingleton('emaildirect/wrapper_suscribers')
								->suscriberAdd($email,$mergeVars);
				}
				else {
					Mage::getSingleton('emaildirect/wrapper_publications')->subscribe($general,$email);
				}
				$subscriber->subscribe($email);
			}
			if( !empty($curlists) ){
				foreach($curlists as $listId => $list){
					if(FALSE === array_key_exists($listId, $lists)){
						Mage::getSingleton('emaildirect/wrapper_lists')->listUnsubscribe($listId, $email);
					}
				}
			}
			//Subscribe to new lists
			$subscribe = array_diff_key($lists, $curlists);
			if( !empty($subscribe) ){
				foreach($subscribe as $listId => $slist){
					Mage::getSingleton('emaildirect/wrapper_lists')->listSubscribe($listId, $email);
				}

			}

		}

		$this->_redirect('*/*/index');
	}
}
