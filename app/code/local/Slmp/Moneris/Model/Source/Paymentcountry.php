<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 */

class Slmp_Moneris_Model_Source_Paymentcountry
{
    /**
     * Prepare payment list
     *
     * @return array
     */
   public function toOptionArray()
    {
		$options = array();
		$options[] = array('value'=>'CA', 'label'=>'Canada');
		$options[] = array('value'=>'US', 'label'=>'United States');
		return $options;
        
    }
}
