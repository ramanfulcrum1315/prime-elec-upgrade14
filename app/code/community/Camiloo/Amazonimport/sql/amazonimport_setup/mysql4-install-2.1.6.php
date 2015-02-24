<?php
	if (!file_exists("/tmp")){
		mkdir("/tmp");
	}
	if(!file_exists("/tmp/amzinstall210lockfile".date("dMY").".txt")){
			// generate a lock file to prevent multiple processes from running this.
			$file = fopen("/tmp/amzupgrade210lockfile".date("dMY").".txt","w+");
			fwrite($file,"locked");
			fclose($file);
			$installer = $this;
			
			$installer->startSetup();
			
			
			$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_log')};
			CREATE TABLE {$this->getTable('amazonimport_log')} (
			  `log_id` int(11) unsigned NOT NULL auto_increment,
			  `outgoing` text NOT NULL default '',
			  `incoming` text NOT NULL default '',
			  `error` text NOT NULL default '',
			  `message_time` datetime NULL,
			  `sent_to_support` int(1) default 0,
			  PRIMARY KEY (`log_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			
			
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
						
			
			$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_categorise_'.$country)};
			CREATE TABLE {$this->getTable('amazonimport_categorise_'.$country)} (
			  `cat_id` int(11) unsigned NOT NULL auto_increment,
			  `productid` int(11) unsigned NOT NULL,
			  `browsenode1` text NOT NULL default '',
			  `browsenode2` text NOT NULL default '',
			  `category` VARCHAR(255) NOT NULL default '',
			  `productdatatype` VARCHAR(255) NOT NULL default '',
			  `producttype` VARCHAR(255) NOT NULL default '',
			  `condition` text NOT NULL default '',
			  `condition_note` text NOT NULL default '',
			  PRIMARY KEY (`cat_id`),
			  KEY `productid` (`productid`),
			  KEY `producttype` (`producttype`),
			  KEY `productdatatype` (`productdatatype`),
			  KEY `category` (`category`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			
			
			$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_listthis_'.$country)};
			CREATE TABLE {$this->getTable('amazonimport_listthis_'.$country)} (
			  `list_id` int(11) unsigned NOT NULL auto_increment,
			  `productid` int(11) unsigned NOT NULL,
			  `is_active` int(1) NOT NULL,
			  reprice_enabled int(1),
			  calculated_price decimal(10,2), minimum_price decimal(10,2),
			  `is_on_amazon` int(1) NOT NULL,
			  `amazonlink` text NOT NULL default '',
			   PRIMARY KEY (`list_id`),
			  KEY `productid` (`productid`),
			  KEY `is_active` (`is_active`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			
			$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_mapping_'.$country)};
			 CREATE TABLE {$this->getTable('amazonimport_mapping_'.$country)} (
			  `mapping_id` int(11) unsigned NOT NULL auto_increment,
			  `xmlkey` VARCHAR(1000) NOT NULL,
			  `mappingvalue` text NOT NULL,
			   PRIMARY KEY (`mapping_id`),
			  KEY `xmlkey` (`xmlkey`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			
			$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_variations_'.$country)};
			  CREATE TABLE {$this->getTable('amazonimport_variations_'.$country)} (
			  `variation_id` int(11) unsigned NOT NULL auto_increment,
			  `configurable_product_id` int(11) NOT NULL,
			  `variation_theme` text NOT NULL,
			   PRIMARY KEY (`variation_id`),
			  KEY `configurable_product_id` (`configurable_product_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			
			$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_setup_'.$country)};
			   CREATE TABLE {$this->getTable('amazonimport_setup_'.$country)} (
			  `setup_id` int(11) unsigned NOT NULL auto_increment,
			  `setup_type` VARCHAR(10) NOT NULL,
			  `asincode` text NOT NULL,
			  `productid` int(11) unsigned NOT NULL,
			  `initial_setup_complete` int(1) NOT NULL,
			   PRIMARY KEY (`setup_id`),
			  KEY `productid` (`productid`),
			  KEY `initial_setup_complete` (`initial_setup_complete`),
			  KEY `setup_type` (`setup_type`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			
			$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_manualsetup_'.$country)};
			CREATE TABLE {$this->getTable('amazonimport_manualsetup_'.$country)} (
			  `manualsetup_id` int(11) unsigned NOT NULL auto_increment,
			  `xmlkey` VARCHAR(1000) NOT NULL,
			  `manualsetupvalue` text NOT NULL,
			  `mapping_override` int(1) NOT NULL,
			  `productid` int(11) NOT NULL,
			   PRIMARY KEY (`manualsetup_id`),
			  KEY `productid` (`productid`),
			  KEY `xmlkey` (`xmlkey`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			
			$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_errorlog_'.$country)};
			  CREATE TABLE {$this->getTable('amazonimport_errorlog_'.$country)} (
			  `elog_id` int(11) unsigned NOT NULL auto_increment,
			  `productid` int(11) NOT NULL,
			  `messageid` int(11) NOT NULL,
			  `dtid` VARCHAR(255) NOT NULL,
			  `time_submitted` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
			  `submission_type` VARCHAR(255) NOT NULL,
			  `result` VARCHAR(255) NOT NULL,
			  `result_description` text NOT NULL,
			   PRIMARY KEY (`elog_id`),
			  KEY `productid` (`productid`),
			  KEY `messageid` (`messageid`),
			  KEY `dtid` (`dtid`),
			  KEY `submission_type` (`submission_type`),
			  KEY `result` (`result`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			
				}
		
		
		$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_surestream_shipping')};
						CREATE TABLE {$this->getTable('amazonimport_surestream_shipping')} (
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
					  PRIMARY KEY (`entity_id`, marketplace)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			
			$installer->installEntities();
			
			
			$installer->endSetup();
			
			$user =  Mage::getSingleton('admin/user')->setReloadAclFlag(true);
			
			Mage::getSingleton('admin/session')->refreshAcl($user);
			
		try{ 
		unlink("/tmp/amzinstall210lockfile".date("dMY").".txt");	
		}catch (Exception $e){
			// don't do anything though.	
		}
	
	}