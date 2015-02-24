<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 */

class Slmp_Moneris_Model_Source_DAVSlist
{
    /**
     * Prepare AVS list
     *
     * @return array
     */
    public function toOptionArray()
    {
		$options = array();
		$options[] = array('value'=>'X', 'label'=>'X - All digits match, nine-digit	 zip code.');
		$options[] = array('value'=>'A', 'label'=>'A - All digits match, five-digit	 zip code.');
		$options[] = array('value'=>'Y', 'label'=>'Y - Address matches, zip code does not match.');
		$options[] = array('value'=>'T', 'label'=>'T - Nine-digit zip code matches,	 address does not match.');
		$options[] = array('value'=>'N', 'label'=>'N - Nothing matches.');
		$options[] = array('value'=>'W', 'label'=> 'W - No data from issuer/authorization system.');
		$options[] = array('value'=>'U', 'label'=> 'U - Retry, system unable to	 process.');
		$options[] = array('value'=>'S', 'label'=> 'S - AVS not supported at this time.');
		return $options;
        
    }
}
