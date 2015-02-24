<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 */

class Slmp_Moneris_Model_Source_AJAVSlist
{
    /**
     * Prepare AVS list
     *
     * @return array
     */
    public function toOptionArray()
    {
		$options = array();
		$options[] = array('value'=>'A', 'label'=>'A - Billing address matches, postal code does not');
		$options[] = array('value'=>'D', 'label'=>'D - Customer name incorrect,	 postal code matches');
		$options[] = array('value'=>'E', 'label'=>'E - Customer name incorrect, billing address and postal code match');
		$options[] = array('value'=>'F', 'label'=>'F - Customer name incorrect, billing address matches');
		$options[] = array('value'=>'K', 'label'=>'K - Customer name matches');
		$options[] = array('value'=>'L', 'label'=> 'L - Customer name and postal codematch');
		$options[] = array('value'=>'M', 'label'=> 'M - Customer name, billing	address, and postal code match');
		$options[] = array('value'=>'N', 'label'=> 'N - Billing address and postal code do not match');
		$options[] = array('value'=>'O', 'label'=> 'O - Customer name and billing address match');
		$options[] = array('value'=>'R', 'label'=> 'R - System unavailable; retry');
		$options[] = array('value'=>'S', 'label'=> 'S - AVS not currently supported');
		$options[] = array('value'=>'U', 'label'=> 'U - Information is unavailable');
		$options[] = array('value'=>'W', 'label'=> 'Customer name, billing address,	 and postal code are all correct');
		$options[] = array('value'=>'Y', 'label'=> 'Y - Billing address and postal	code both match');
		$options[] = array('value'=>'Z', 'label'=> 'Z - Postal code matches, billing address does not');
		return $options;
        
    }
}
