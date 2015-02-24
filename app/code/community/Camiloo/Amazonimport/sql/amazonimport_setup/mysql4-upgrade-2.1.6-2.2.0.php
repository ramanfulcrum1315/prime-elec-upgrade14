<?php

$installer = $this;
$installer->startSetup();
$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_browsenodes')};");

		$installer->run("CREATE TABLE IF NOT EXISTS {$this->getTable('amazonimport_browsenodes')} (
							  `browsenode_id` bigint(11) NOT NULL default 0,
							  `country_id` varchar(3),
							  `category_name` text,
							  `category_tree_location` text,
							  `query` text,
							  PRIMARY KEY (`browsenode_id`,`country_id`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


		$installer->run("CREATE TABLE IF NOT EXISTS {$this->getTable('camiloo_magelicense')} (
			  `sku` text NOT NULL default '',
			  `licensedata` text NOT NULL default ''
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	
		
		$installer->run("CREATE TABLE IF NOT EXISTS {$this->getTable('amazonimport_categorymapping')} (
							  `category_id` int(11) NOT NULL default 0,
							  `country_id` varchar(3),
							  `browsenode1` bigint(11),
							  `browsenode2` bigint(11),
							  `itemtype` text,
							  `variation_theme` text,
							  `level` text,
							  `inherited` text,
							  `condition` text,
							  `condition_note` text,
							  PRIMARY KEY (`category_id`,`country_id`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


		$installer->run("CREATE TABLE IF NOT EXISTS {$this->getTable('amazonimport_fielddescriptions')} (
							  `fieldname` varchar(160) NOT NULL default '',
							  `country_id` varchar(3),
							  `category_name` varchar(160),
							  `value` text,
							  `accepted_values` text,
							  `example` text,
							  `is_required` text,
							  PRIMARY KEY (`fieldname`,`country_id`,`category_name`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		$installer->run("ALTER TABLE {$this->getTable('amazonimport_browsenodes')} MODIFY browsenode_id bigint(11)");
		
		$installer->run("ALTER TABLE {$this->getTable('amazonimport_categorymapping')} MODIFY browsenode1 bigint(11), MODIFY browsenode2 bigint(11)");

	try {
		
				
	$db = Mage::getSingleton("core/resource")->getConnection("core_write");
	$table_prefix = Mage::getConfig()->getTablePrefix();
	$db->query("TRUNCATE TABLE {$table_prefix}amazonimport_browsenodes");
	$db->query("TRUNCATE TABLE {$table_prefix}amazonimport_fielddescriptions");
											
	$bnsql = array();
	$bnsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_browsenodes_uk.sql';
	$bnsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_browsenodes_fr.sql';
	$bnsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_browsenodes_de.sql';
	$bnsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_browsenodes_com.sql';
	$bnsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_browsenodes_com2.sql';
									
	foreach($bnsql as $bnlocation){
			$_sql = str_replace("[[replaceme]]","REPLACE INTO {$table_prefix}amazonimport_browsenodes",file_get_contents($bnlocation));
			$_sql = explode("REPLACE INTO",$_sql);
			foreach($_sql as $command){
					if($command != ""){
							$db->query("REPLACE INTO".utf8_decode($command));
					}
			}
									  
									  
	}
					   
	$fdsql = array(); 
	$fdsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_fielddescriptions_uk.sql';
	$fdsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_fielddescriptions_de.sql';
	$fdsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_fielddescriptions_fr.sql';
	$fdsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_fielddescriptions_com.sql';
	foreach($fdsql as $fdlocation){
			$_sql = str_replace("[[replaceme]]","REPLACE INTO {$table_prefix}amazonimport_fielddescriptions",file_get_contents($fdlocation));
			$_sql = explode("REPLACE INTO",$_sql);
			foreach($_sql as $command){
					if($command != ""){
							$db->query("REPLACE INTO".utf8_decode($command));
					}
			}
	}

	}
	catch(Exception $e) {
	}
			
$installer->installEntities();
$installer->endSetup();


?>
