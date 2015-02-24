<?php
$installer = $this;

$installer->startSetup();

$installer->run("
		ALTER TABLE {$this->getTable('newsletter_subscriber')} 
		ADD firstname varchar(255) NOT NULL default '',
		ADD lastname varchar(255) NOT NULL default ''
  ");

$installer->endSetup();
