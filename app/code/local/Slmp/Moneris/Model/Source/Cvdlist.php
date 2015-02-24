<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 */

class Slmp_Moneris_Model_Source_Cvdlist
{
    /**
     * Prepare CVD list
     *
     * @return array
     */
    public function toOptionArray()
    {
		$options = array();
		$options[] = array('value'=>'M', 'label'=>'M - Match');
		$options[] = array('value'=>'Y', 'label'=>'Y - Match for AmEx');
		$options[] = array('value'=>'N', 'label'=>'N - No Match');
		$options[] = array('value'=>'P', 'label'=>'P - Not Processed');
		$options[] = array('value'=>'S', 'label'=>'S - CVD should be on the card, but Merchant has indicated that CVD is not present');
		$options[] = array('value'=>'R', 'label'=> 'R - Retry for AmEx');
		$options[] = array('value'=>'U', 'label'=> 'U - Issuer is not a CVD participant');
		$options[] = array('value'=>'Other', 'label'=> 'Other - Invalid Response Code');
		return $options;
        
    }
}
