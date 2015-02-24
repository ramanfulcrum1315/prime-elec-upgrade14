<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 */

class Slmp_Moneris_Model_Source_Pmlist
{
    /**
     * Prepare payment list
     *
     * @return array
     */
    public function toOptionArray()
    {
		$options = array();
		$options[] = array('value'=>'authorize', 'label'=>'Authorize Only');
		$options[] = array('value'=>'authorize_capture', 'label'=>'Authorize and Capture');
		return $options;
        
    }
}
