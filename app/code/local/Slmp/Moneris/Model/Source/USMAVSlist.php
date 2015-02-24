<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 */

class Slmp_Moneris_Model_Source_USMAVSlist
{
    /**
     * Prepare AVS list
     *
     * @return array
     */
    public function toOptionArray()
    {
		$options = array();
		$options[] = array('value'=>'A', 'label'=>'A - Address matches, zip code does not.');
		$options[] = array('value'=>'N', 'label'=> 'N - Neither address nor zip code matches.');
		$options[] = array('value'=>'R', 'label'=>'R - Retry; system unable to	process.');
		$options[] = array('value'=>'S', 'label'=>'S - AVS currently not supported.');
		$options[] = array('value'=>'U', 'label'=>'U - No data from	 Issuer/Authorization system.');
		$options[] = array('value'=>'W', 'label'=>'W - For U.S. Addresses, nine-digit zip code matches, address does not; for address	 outside the U.S. postal code matches, address does not.');
		$options[] = array('value'=>'X', 'label'=> 'Y - For U.S. addresses,	 nine-digit zip code and addresses matches; for addresses outside the U.S., postal code and address match');
		$options[] = array('value'=>'Y', 'label'=>'Y - Street address and postal code match.');
		$options[] = array('value'=>'Z', 'label'=>'Z - For U.S. addresses, five-digit zip code and address matches.');
		return $options;
        
    }
}
