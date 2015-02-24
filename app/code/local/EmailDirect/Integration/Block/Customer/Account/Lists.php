<?php

class Emaildirect_Integration_Block_Customer_Account_Lists extends Mage_Core_Block_Template
{

	protected $_lists  = array();
	protected $_info  = array();
	protected $_myLists = array();
	protected $_generalList = array();
	protected $_form;
	protected $_allLists = array();
	protected $_publications = array();

	public function getAllLists()
	{
		$listData = Mage::getSingleton('emaildirect/wrapper_lists')->getLists();
		$options =  array();
		foreach($listData as $list)
		{
			if($list['active'])
				$options[] = array(
								'id' => $list['id'],
								'name' => $list['name']);
		}
		$this->_allLists = $options;
		return $options;
	}

	public function getGeneralList()
	{
		$general = $this->helper('emaildirect')->config('publication');
//		foreach($this->_allLists as $list) {
//			if($list['id']==$general) {
//				$rc = array('id' => $list['id'], 'name' => $list['name']);
//				break;
//			}
//		}
		$rc = Mage::getSingleton('emaildirect/wrapper_publications')->getPublication($general);
		return array('id' =>(int) $rc->PublicationID,'name'=> (string)$rc->Name);
	}


	/**
	 * Get additional lists data
	 *
	 * @return array
	 */
	public function getLists()
	{
		$additionalLists = $this->helper('emaildirect')->config('additional_lists');
		$activelists = explode(",",$additionalLists);
		$this->getAllLists();

		if($additionalLists){
				$lists = $this->_allLists;
				$options =  array();
				foreach($lists as $list)
				{
					if(in_array($list['id'],$activelists))
						$this->_lists [] = array(
										'id' => $list['id'],
										'name' => $list['name']);
				}

		}
		return $this->_lists;
	}

	/**
	 * Getter for class property
	 *
	 * @return array
	 */
	public function getSubscribedLists()
	{
		$properties = Mage::getSingleton('emaildirect/wrapper_suscribers')->getProperties($this->_getEmail());
		$rc = array();
		$publications = array();
		foreach($properties->Lists->List as $list)
		{
			$rc [] = (int)$list->ListID;
		}
		$this->_myLists = $rc;
		foreach($properties->Publications->Publication as $publication)
		{
			$this->_publications[] = (int) $publication->PublicationID;
		}
		return $this->_myLists;
	}

	/**
	 * Utility to generate HTML name for element
	 * @param string $list
	 * @param string $group
	 * @param bool $multiple
	 * @return string
	 */
	protected function _htmlGroupName($list, $group = NULL, $multiple = FALSE)
	{
		$htmlName = "list[{$list['id']}]";

		if(!is_null($group)){
			$htmlName .= "[{$group['id']}]";
		}

		if(TRUE === $multiple){
			$htmlName .= '[]';
		}

		return $htmlName;
	}

    /**
     * Form getter/instantiation
     *
     * @return Varien_Data_Form
     */
    public function getForm()
    {
        if ($this->_form instanceof Varien_Data_Form) {
            return $this->_form;
        }
        $form = new Varien_Data_Form();
        return $form;
    }


	/**
	 * Retrieve email from Customer object in session
	 *
	 * @return string Email address
	 */
	protected function _getEmail()
	{
		return $this->helper('customer')->getCustomer()->getEmail();
	}

	/**
	 * Return HTML code for list <label> with checkbox, checked if subscribed, otherwise not
	 *
	 * @param array $list List data from 
	 * @return string HTML code
	 */
	public function listLabel($generalList,$list)
	{
		$myLists = $this->_myLists;

		$checkbox = new Varien_Data_Form_Element_Checkbox;
		$checkbox->setForm($this->getForm());
		$checkbox->setHtmlId('list-' . $list['id']);
		if(!in_array($generalList['id'],$this->_publications)) {
			$checkbox->setChecked(false);
			$checkbox->setData('disabled',true);
		}
		else {
			$checkbox->setChecked((bool)(is_array($myLists) && in_array($list['id'], $myLists)));
		}
		$checkbox->setTitle( ($checkbox->getChecked() ? $this->__('Click to unsubscribe from this list.') : $this->__('Click to subscribe to this list.')) );
		$checkbox->setLabel($list['name']);

		$hname = $this->_htmlGroupName($list);
		$checkbox->setName($hname . '[subscribed]');

		$checkbox->setValue($list['id']);
		$checkbox->setClass('emaildirect-list-subscriber');


		return $checkbox->getLabelHtml() . $checkbox->getElementHtml();
	}
	public function publicationLabel($list)
	{
		$myLists = $this->_myLists;

		$checkbox = new Varien_Data_Form_Element_Checkbox;
		$checkbox->setForm($this->getForm());
		$checkbox->setHtmlId('publication-' . $list['id']);
		$checkbox->setChecked(in_array($list['id'],$this->_publications));
//		$checkbox->setChecked((bool)(is_array($myLists) && in_array($list['id'], $myLists)));
		$checkbox->setTitle( ($checkbox->getChecked() ? $this->__('Click to unsubscribe from this list.') : $this->__('Click to subscribe to this list.')) );
		$checkbox->setLabel($list['name']);

//		$hname = $this->_htmlGroupName($list);
		$hname =  'publication';
		$checkbox->setName($hname . '[subscribed]');

		$checkbox->setValue($list['id']);
		$checkbox->setClass('emaildirect-publication-subscriber');


		return $checkbox->getLabelHtml() . $checkbox->getElementHtml();
	}
	
}
