<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 */

class Slmp_Moneris_Model_Source_Cavvlist
{
    /**
     * Prepare CVD list
     *
     * @return array
     */
    public function toOptionArray()
    {
		$options = array();
		$options[] = array('value'=>'0', 'label'=>'0 - CAVV authentication results invalid');
		$options[] = array('value'=>'1', 'label'=>'1 - CAVV failed validation;authentication');
		$options[] = array('value'=>'2', 'label'=>'2 - CAVV passed validation;authentication');
		$options[] = array('value'=>'3', 'label'=>'3 - CAVV passed validation; attempt');
		$options[] = array('value'=>'4', 'label'=>'4 - CAVV failed validation; attempt');
		$options[] = array('value'=>'7', 'label'=>'7 - CAVV failed validation; attempt (US issued cards only)');
		$options[] = array('value'=>'8', 'label'=>'8 - CAVV passed validation; attempt(US issued cards only');
		$options[] = array('value'=>'9', 'label'=>'9 - CAVV failed validation; attempt(US issued cards only)');
		$options[] = array('value'=>'A', 'label'=>'A - CAVV passed validation; attempt(US issued cards only)');
		$options[] = array('value'=>'B', 'label'=>'B - CAVV passed validation;information only, no liability shift');
		return $options;
        
    }
}
