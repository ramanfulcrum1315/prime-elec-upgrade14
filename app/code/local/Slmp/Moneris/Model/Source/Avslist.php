<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 */

class Slmp_Moneris_Model_Source_Avslist
{
    /**
     * Prepare AVS list
     *
     * @return array
     */
    public function toOptionArray()
    {
		$options = array();
		$options[] = array('value'=>'A', 'label'=>'A-Approved');
		$options[] = array('value'=>'B', 'label'=>'B-Approved');
		$options[] = array('value'=>'C', 'label'=>'C-Approved');
		$options[] = array('value'=>'D', 'label'=>'D-Approved');
		$options[] = array('value'=>'G', 'label'=>'G-Approved');
		$options[] = array('value'=>'I', 'label'=> 'I-Approved');
		$options[] = array('value'=>'M', 'label'=> 'M-Approved');
		$options[] = array('value'=>'N', 'label'=> 'N-Approved');
		$options[] = array('value'=>'P', 'label'=> 'P-Approved');
		$options[] = array('value'=>'R', 'label'=> 'R-Approved');
		$options[] = array('value'=>'U', 'label'=> 'U-Approved');
		$options[] = array('value'=>'Y', 'label'=> 'Y-Approved');
		$options[] = array('value'=>'Z', 'label'=> 'Z-Approved');
		$options[] = array('value'=>'D-Y', 'label'=> 'Y-Declined');
		$options[] = array('value'=>'D-U', 'label'=> 'U-Declined');
		$options[] = array('value'=>'D-S', 'label'=> 'S-Declined');
		return $options;
        
    }
}
