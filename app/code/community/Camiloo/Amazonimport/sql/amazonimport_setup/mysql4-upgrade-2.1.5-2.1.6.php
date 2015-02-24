<?php

$installer = $this;
	
$installer->startSetup();

$installer->run("CREATE TABLE IF NOT EXISTS {$this->getTable('amazonimport_stockpricetrack')} (
					  `entity_id` int(11) NOT NULL default 0,
					  marketplace varchar(3),
					  curr_mage_price decimal(10,2),
					  curr_mage_qty int(11),
					  last_sent_price decimal(10,2),
					  last_sent_qty int(11),
					  date_price_sent timestamp DEFAULT 0,
					  date_qty_sent timestamp DEFAULT 0,
					  `last_update` timestamp,
					  PRIMARY KEY (`entity_id`,marketplace)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			

$installer->installEntities();
$installer->endSetup();


?>