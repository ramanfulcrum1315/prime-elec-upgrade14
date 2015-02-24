<?php
/*
 * Created on Dec 6, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class EmailDirect_Integration_Model_System_Config_Source_Publication
{
	public function toOptionArray()
	{
		$options =  array();

		$publications = Mage::getSingleton('emaildirect/wrapper_publications')->getPublications();
		foreach($publications as $publication)
		{
			if($publication['active'])
				$options[] = array(
								'value' => $publication['id'],
								'label' => $publication['name']);
		}
		return $options;
	}
}