<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 */

class Slmp_Moneris_Model_Source_Currencylist
{
    /**
     * Prepare payment list
     *
     * @return array
     */
    public function toOptionArray()
    {
		return array(
            array(
                'value' => 'USD',
                'label' => 'US Dollar'
            ),
        );
    }
}
