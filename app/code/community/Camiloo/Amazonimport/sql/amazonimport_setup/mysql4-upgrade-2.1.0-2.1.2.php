<?php

$installer = $this;
	
$installer->startSetup();


$countries = array("com","uk","de","fr");
foreach ($countries as $country){

	try {
	$installer->run("ALTER TABLE {$this->getTable('amazonimport_listthis_'.$country)} ADD COLUMN (
	reprice_enabled int(1), calculated_price decimal(10,2), minimum_price decimal(10,2))");
	} catch (Exception $e){
	}
}
	
$installer->installEntities();
$installer->endSetup();


?>