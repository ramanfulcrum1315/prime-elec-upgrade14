<?php

class EmailDirect_Integration_Model_System_Config_Source_Abandonedlist
{
   public function toOptionArray()
   {
      $options =  array();
      
      $options[] = array(
                        'value' => -1,
                        'label' => "-- SELECT LIST --");

      $lists = Mage::getSingleton('emaildirect/wrapper_lists')->getLists();
      foreach($lists as $list)
      {
         if($list['active'])
            $options[] = array(
                        'value' => $list['id'],
                        'label' => $list['name']);
      }
      return $options;
   }
}
