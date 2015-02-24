<?php

$installer = $this;
	
$installer->startSetup();

$installer->run("CREATE TABLE IF NOT EXISTS {$this->getTable('camiloo_magelicense')} (
			  `sku` text NOT NULL default '',
			  `licensedata` text NOT NULL default ''
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	
$installer->installEntities();
$installer->endSetup();

?>