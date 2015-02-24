<?php

class EmailDirect_Integration_Block_Adminhtml_Memberactivity_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('token_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
    	$customer = Mage::registry('current_customer');
    	$email    = $customer->getEmail();

    	$api      = Mage::getSingleton('emaildirect/wrapper_suscribers', array('apikey' => Mage::helper('emaildirect')->getApiKey($customer->getStore())));
    	$activity = array();
//    	$lists    = $api->listsForEmail($email);

		$activityData = array();
		$history = $api->suscriberHistory($email);
		foreach($history->Actions->HistoryAction as $activity){
			$act['action'] = $activity->Action;
			$act['details'] = $activity->Details;
			$act['timestamp'] = $activity->Date;
			if(isset($activity->Links->Link)) {
				$act['url'] = $activity->Links->Link;
			}
			$activityData[] = $act;
		}



//		if(is_array($lists)){
//			foreach($lists as $list){
//				$activity []= $api->listMemberActivity($list, $email);
//			}
//
//			if(!empty($activity)){
//				foreach($activity as $act){
//
//					if(empty($act['data'][0])){
//						continue;
//					}
//
//					$activityData []= $act['data'];
//				}
//			}
//		}

        $collection = Mage::getModel('emaildirect/custom_collection', array($activityData));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('action', array(
            'header'=> Mage::helper('emaildirect')->__('Action'),
            'index' => 'action',
            'sortable' => false
        ));
        $this->addColumn('details', array(
            'header'=> Mage::helper('emaildirect')->__('Details'),
            'index' => 'details',
            'sortable' => false
        ));
        $this->addColumn('url', array(
            'header'=> Mage::helper('emaildirect')->__('Url'),
            'index' => 'url',
            'sortable' => false
        ));
//        $this->addColumn('bounce_type', array(
//            'header'=> Mage::helper('emaildirect')->__('Bounce Type'),
//            'index' => 'bounce_type',
//            'sortable' => false
//        ));
//        $this->addColumn('campaign_id', array(
//            'header'=> Mage::helper('emaildirect')->__('Campaign ID'),
//            'index' => 'campaign_id',
//            'sortable' => false
//        ));
        $this->addColumn('timestamp', array(
            'header'=> Mage::helper('emaildirect')->__('Timestamp'),
            'index' => 'timestamp',
            'sortable' => false
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return false;
    }

}