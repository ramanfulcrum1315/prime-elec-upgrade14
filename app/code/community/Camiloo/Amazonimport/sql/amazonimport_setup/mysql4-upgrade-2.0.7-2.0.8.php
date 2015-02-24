<?php
	if (!file_exists("/tmp")){
		mkdir("/tmp");
	}
	if(!file_exists("/tmp/amzupgrade208lockfile".date("dMY").".txt")){

			$installer = $this;
			
			$installer->startSetup();


			// generate a lock file to prevent multiple processes from running this.
			$file = fopen("/tmp/amzupgrade208lockfile".date("dMY").".txt","w+");
			fwrite($file,"locked");
			fclose($file);
		
			$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_surestream')};
			CREATE TABLE {$this->getTable('amazonimport_surestream')} (
			  `marketplace` VARCHAR(3) NOT NULL default '',
			  `state` VARCHAR(100) NOT NULL default 'WaitingToSubmitProductFeed',
			  `last_state_change` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
			  `submission_id` VARCHAR(25) NOT NULL default '',
			  `orderimport_submission_id` VARCHAR(25) NOT NULL default '',
			  `productimport_submission_id` VARCHAR(25) NOT NULL default '',
			  `running_flag` INT(1) NOT NULL,
			  PRIMARY KEY (`marketplace`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			
				$countries = array("com","uk","de","fr");
				foreach ($countries as $country){
					
					$installer->run("INSERT INTO {$this->getTable('amazonimport_surestream')} (`marketplace`) VALUES ('".$country."');");
						
					/* 	categorise */
					$installer->run("ALTER TABLE  {$this->getTable('amazonimport_categorise_'.$country)} ADD INDEX (`productid`);");
			
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_categorise_'.$country)} 
						CHANGE  `producttype` `producttype` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_categorise_'.$country)} ADD INDEX (`producttype`);");
					
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_categorise_'.$country)} 
						CHANGE  `productdatatype` `productdatatype` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_categorise_'.$country)} ADD INDEX (`productdatatype`);");
					
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_categorise_'.$country)} 
						CHANGE  `category` `category` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_categorise_'.$country)} ADD INDEX (`category`);");
					
						/* 	listthis */
					
					$installer->run("ALTER TABLE  {$this->getTable('amazonimport_listthis_'.$country)} ADD INDEX (`productid`);");
					$installer->run("ALTER TABLE  {$this->getTable('amazonimport_listthis_'.$country)} ADD INDEX (`is_active`);");
					
						/* 	manual setup */
					
					$installer->run("ALTER TABLE  {$this->getTable('amazonimport_manualsetup_'.$country)} ADD INDEX (`productid`);");
					
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_manualsetup_'.$country)} 
						CHANGE  `xmlkey` `xmlkey` VARCHAR( 1000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_manualsetup_'.$country)} ADD INDEX (`xmlkey`);");
							
								
					/* 	mapping */
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_mapping_'.$country)} CHANGE  `xmlkey` `xmlkey` VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_mapping_'.$country)} ADD INDEX (`xmlkey`);");
				
					/*	setup 	*/
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_setup_'.$country)} ADD INDEX (`productid`);");				
					
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_setup_'.$country)} CHANGE  `initial_setup_complete`
																  `initial_setup_complete` int(1) NOT NULL;");
					
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_setup_'.$country)} ADD INDEX (`initial_setup_complete`);");
					
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_setup_'.$country)} CHANGE  `setup_type`
																  `setup_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
					
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_setup_'.$country)} ADD INDEX (`setup_type`);");
					
					
					/*	variations 	*/
					
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_variations_'.$country)} CHANGE  `configurable_product_id`
																  `configurable_product_id`  INT( 11 ) NOT NULL;");
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_variations_'.$country)} ADD INDEX (`configurable_product_id`);");
					
					
					/*	error log 	*/
					
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_errorlog_'.$country)} ADD INDEX (`productid`);");
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_errorlog_'.$country)} ADD INDEX (`messageid`);");
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_errorlog_'.$country)} CHANGE  `dtid`
																  `dtid` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_errorlog_'.$country)} ADD INDEX (`dtid`);");
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_errorlog_'.$country)} CHANGE  `submission_type`
																  `submission_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_errorlog_'.$country)} ADD INDEX (`submission_type`);");
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_errorlog_'.$country)} CHANGE  `result`
																  `result` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
					$installer->run("ALTER TABLE {$this->getTable('amazonimport_errorlog_'.$country)} ADD INDEX (`result`);");
					
				}
				
			
			
		
				
		$installer->installEntities();
		$installer->endSetup();
		unlink("/tmp/amzupgrade208lockfile".date("dMY").".txt");	
	}
	