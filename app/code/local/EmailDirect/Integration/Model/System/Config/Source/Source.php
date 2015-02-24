<?php
/*
 * Created on Dec 6, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class EmailDirect_Integration_Model_System_Config_Source_Source
{
	public function toOptionArray()
	{
		$options =  array();

		$sources = Mage::getSingleton('emaildirect/wrapper_sources')->getSources();
		foreach($sources as $source)
		{
			if($source['active'])
				$options[] = array(
								'value' => $source['id'],
								'label' => $source['name']);
		}
		return $options;
	}
}