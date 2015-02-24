<?php
	if (!file_exists("/tmp")){
		mkdir("/tmp");
	}
	if(!file_exists("/tmp/amzupgrade210lockfile".date("dMY").".txt")){

			$installer = $this;
			
			$installer->startSetup();
		
			// generate a lock file to prevent multiple processes from running this.
			$file = fopen("/tmp/amzupgrade210lockfile".date("dMY").".txt","w+");
			fwrite($file,"locked");
			fclose($file);
		
		$installer->run("CREATE TABLE {$this->getTable('amazonimport_surestream_shipping')} (
					  `marketplace` VARCHAR(3) NOT NULL default '',
					  `amazon_order_id` VARCHAR(100) NOT NULL default '',
					  `last_update` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
					  `carrier_name` VARCHAR(50) NOT NULL default '',
					  `tracking_number` VARCHAR(50) NOT NULL default '',
					  PRIMARY KEY (`amazon_order_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

	
		$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_flatorders')};	
					    CREATE TABLE {$this->getTable('amazonimport_flatorders')} (
					  `entity_id` int(11) NOT NULL default 0,
					  `amazon_order_id` VARCHAR(100) NOT NULL default '',
					  `amazon_marketplace` VARCHAR(50) NOT NULL default '',
					  PRIMARY KEY (`entity_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			
		$installer->installEntities();
		$installer->endSetup();
	}
	
	unlink("/tmp/amzupgrade210lockfile".date("dMY").".txt");	
		
	?>