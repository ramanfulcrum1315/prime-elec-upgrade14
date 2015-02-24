<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();
	$installer->run("
	ALTER TABLE {$resource->getTableName('navigationmenu/contents')} ADD COLUMN `store_ids` varchar(255) NOT NULL default '';
	");
$installer->endSetup();

