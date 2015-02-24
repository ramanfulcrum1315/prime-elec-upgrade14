<?php
/*
 * Created on Nov 25, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class EmailDirect_Integration_Model_System_Config_Source_List
{
	public function toOptionArray()
	{
		$options =  array();
		$options[] = array (
						'value' => -1,
						'label' => '--select--',
					);

//		$ws = Mage::getModel('emaildirect/wrapper_lists');
//		$lists = $ws->getLists();
		$lists = Mage::getSingleton('emaildirect/wrapper_lists')->getLists();
		foreach($lists as $list)
		{
			if($list['active'])
				$options[] = array(
								'value' => $list['id'],
								'label' => $list['name']);
		}
		return $options;
	}
}
