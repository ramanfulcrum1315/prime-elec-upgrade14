<?php
/*
 * Created on Dec 6, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class EmailDirect_Integration_Model_System_Config_Source_Export_Batch
{
   public function toOptionArray()
   {
      $options = array(
                  array('value' => 1,'label' => "1"),
                  array('value' => 5,'label' => "5"),
                  array('value' => 10,'label' => "10"),
                  array('value' => 25,'label' => "25"),
                  array('value' => 50,'label' => "50"),
                  array('value' => 100,'label' => "100"),
                  array('value' => 250,'label' => "250")
						);
                  
      return $options;
   }
}