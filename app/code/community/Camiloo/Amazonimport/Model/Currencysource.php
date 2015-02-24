<?php 
class Camiloo_Amazonimport_Model_Currencysource
{
	
	  public function toOptionArray()
	  {
	    return array(
	      array('value' => 'DEFAULT', 'label' => 'DEFAULT'),
	      array('value' => 'USD', 'label' => 'USD'),
	      array('value' => 'GBP', 'label' => 'GBP'),
	      array('value' => 'EUR', 'label' => 'EUR'),
	      array('value' => 'JPY', 'label' => 'JPY'),
	      array('value' => 'CAD', 'label' => 'CAD'),
	    );
	  }
	
}
?>