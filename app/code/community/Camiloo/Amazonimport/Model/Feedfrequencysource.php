<?php 
class Camiloo_Amazonimport_Model_Feedfrequencysource
{

	  public function toOptionArray($type = null)
	  {
	    return array(
	      array('value' => '15MINS', 'label' => 'Every 15 minutes'),
	      array('value' => '30MINS', 'label' => 'Every 30 minutes'),
	      array('value' => '1HOUR', 'label' => 'Every hour'),
	      array('value' => '4HOUR', 'label' => 'Every 4 hours'),
	      array('value' => '12HOUR', 'label' => 'Every 12 hours'),
	    );
	  }

}
?>