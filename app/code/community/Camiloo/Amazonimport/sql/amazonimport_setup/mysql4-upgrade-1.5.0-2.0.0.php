<?php

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


$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_categorise_com')};
CREATE TABLE {$this->getTable('amazonimport_categorise_com')} (
  `cat_id` int(11) unsigned NOT NULL auto_increment,
  `productid` int(11) unsigned NOT NULL,
  `browsenode1` text NOT NULL default '',
  `browsenode2` text NOT NULL default '',
  `category` text NOT NULL default '',
  `productdatatype` text NOT NULL default '',
  `producttype` text NOT NULL default '',
  `condition` text NOT NULL default '',
  `condition_note` text NOT NULL default '',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_categorise_uk')};
CREATE TABLE {$this->getTable('amazonimport_categorise_uk')} (
  `cat_id` int(11) unsigned NOT NULL auto_increment,
  `productid` int(11) unsigned NOT NULL,
  `browsenode1` text NOT NULL default '',
  `browsenode2` text NOT NULL default '',
  `category` text NOT NULL default '',
  `productdatatype` text NOT NULL default '',
  `producttype` text NOT NULL default '',
  `condition` text NOT NULL default '',
  `condition_note` text NOT NULL default '',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_categorise_fr')};
CREATE TABLE {$this->getTable('amazonimport_categorise_fr')} (
  `cat_id` int(11) unsigned NOT NULL auto_increment,
  `productid` int(11) unsigned NOT NULL,
  `browsenode1` text NOT NULL default '',
  `browsenode2` text NOT NULL default '',
  `category` text NOT NULL default '',
  `productdatatype` text NOT NULL default '',
  `producttype` text NOT NULL default '',
  `condition` text NOT NULL default '',
  `condition_note` text NOT NULL default '',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_categorise_de')};
CREATE TABLE {$this->getTable('amazonimport_categorise_de')} (
  `cat_id` int(11) unsigned NOT NULL auto_increment,
  `productid` int(11) unsigned NOT NULL,
  `browsenode1` text NOT NULL default '',
  `browsenode2` text NOT NULL default '',
  `category` text NOT NULL default '',
  `productdatatype` text NOT NULL default '',
  `producttype` text NOT NULL default '',
  `condition` text NOT NULL default '',
  `condition_note` text NOT NULL default '',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_listthis_com')};
CREATE TABLE {$this->getTable('amazonimport_listthis_com')} (
  `list_id` int(11) unsigned NOT NULL auto_increment,
  `productid` int(11) unsigned NOT NULL,
  `is_active` int(1) NOT NULL,
  `is_on_amazon` int(1) NOT NULL,
  `amazonlink` text NOT NULL default '',
   PRIMARY KEY (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_listthis_uk')};
CREATE TABLE {$this->getTable('amazonimport_listthis_uk')} (
  `list_id` int(11) unsigned NOT NULL auto_increment,
  `productid` int(11) unsigned NOT NULL,
  `is_active` int(1) NOT NULL,
  `is_on_amazon` int(1) NOT NULL,
  `amazonlink` text NOT NULL default '',
   PRIMARY KEY (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_listthis_fr')};
CREATE TABLE {$this->getTable('amazonimport_listthis_fr')} (
  `list_id` int(11) unsigned NOT NULL auto_increment,
  `productid` int(11) unsigned NOT NULL,
  `is_active` int(1) NOT NULL,
  `is_on_amazon` int(1) NOT NULL,
  `amazonlink` text NOT NULL default '',
   PRIMARY KEY (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_listthis_de')};
CREATE TABLE {$this->getTable('amazonimport_listthis_de')} (
  `list_id` int(11) unsigned NOT NULL auto_increment,
  `productid` int(11) unsigned NOT NULL,
  `is_active` int(1) NOT NULL,
  `is_on_amazon` int(1) NOT NULL,
  `amazonlink` text NOT NULL default '',
   PRIMARY KEY (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_mapping_com')};
CREATE TABLE {$this->getTable('amazonimport_mapping_com')} (
  `mapping_id` int(11) unsigned NOT NULL auto_increment,
  `xmlkey` text NOT NULL,
  `mappingvalue` text NOT NULL,
   PRIMARY KEY (`mapping_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_mapping_uk')};
CREATE TABLE {$this->getTable('amazonimport_mapping_uk')} (
  `mapping_id` int(11) unsigned NOT NULL auto_increment,
  `xmlkey` text NOT NULL,
  `mappingvalue` text NOT NULL,
   PRIMARY KEY (`mapping_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_mapping_fr')};
CREATE TABLE {$this->getTable('amazonimport_mapping_fr')} (
  `mapping_id` int(11) unsigned NOT NULL auto_increment,
  `xmlkey` text NOT NULL,
  `mappingvalue` text NOT NULL,
   PRIMARY KEY (`mapping_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_mapping_de')};
CREATE TABLE {$this->getTable('amazonimport_mapping_de')} (
  `mapping_id` int(11) unsigned NOT NULL auto_increment,
  `xmlkey` text NOT NULL,
  `mappingvalue` text NOT NULL,
   PRIMARY KEY (`mapping_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_variations_com')};
CREATE TABLE {$this->getTable('amazonimport_variations_com')} (
  `variation_id` int(11) unsigned NOT NULL auto_increment,
  `configurable_product_id` text NOT NULL,
  `variation_theme` text NOT NULL,
   PRIMARY KEY (`variation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_variations_uk')};
CREATE TABLE {$this->getTable('amazonimport_variations_uk')} (
  `variation_id` int(11) unsigned NOT NULL auto_increment,
  `configurable_product_id` text NOT NULL,
  `variation_theme` text NOT NULL,
   PRIMARY KEY (`variation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_variations_fr')};
CREATE TABLE {$this->getTable('amazonimport_variations_fr')} (
  `variation_id` int(11) unsigned NOT NULL auto_increment,
  `configurable_product_id` text NOT NULL,
  `variation_theme` text NOT NULL,
   PRIMARY KEY (`variation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_variations_de')};
CREATE TABLE {$this->getTable('amazonimport_variations_de')} (
  `variation_id` int(11) unsigned NOT NULL auto_increment,
  `configurable_product_id` text NOT NULL,
  `variation_theme` text NOT NULL,
   PRIMARY KEY (`variation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_setup_com')};
CREATE TABLE {$this->getTable('amazonimport_setup_com')} (
  `setup_id` int(11) unsigned NOT NULL auto_increment,
  `setup_type` text NOT NULL,
  `asincode` text NOT NULL,
  `productid` int(11) unsigned NOT NULL,
  `initial_setup_complete` text NOT NULL,
   PRIMARY KEY (`setup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_setup_uk')};
CREATE TABLE {$this->getTable('amazonimport_setup_uk')} (
  `setup_id` int(11) unsigned NOT NULL auto_increment,
  `setup_type` text NOT NULL,
  `asincode` text NOT NULL,
  `productid` int(11) unsigned NOT NULL,
  `initial_setup_complete` text NOT NULL,
   PRIMARY KEY (`setup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_setup_fr')};
CREATE TABLE {$this->getTable('amazonimport_setup_fr')} (
  `setup_id` int(11) unsigned NOT NULL auto_increment,
  `setup_type` text NOT NULL,
  `asincode` text NOT NULL,
  `productid` int(11) unsigned NOT NULL,
  `initial_setup_complete` text NOT NULL,
   PRIMARY KEY (`setup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_setup_de')};
CREATE TABLE {$this->getTable('amazonimport_setup_de')} (
  `setup_id` int(11) unsigned NOT NULL auto_increment,
  `setup_type` text NOT NULL,
  `asincode` text NOT NULL,
  `productid` int(11) unsigned NOT NULL,
  `initial_setup_complete` text NOT NULL,
   PRIMARY KEY (`setup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_manualsetup_com')};
CREATE TABLE {$this->getTable('amazonimport_manualsetup_com')} (
  `manualsetup_id` int(11) unsigned NOT NULL auto_increment,
  `xmlkey` text NOT NULL,
  `manualsetupvalue` text NOT NULL,
  `mapping_override` int(1) NOT NULL,
  `productid` int(11) NOT NULL,
   PRIMARY KEY (`manualsetup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_manualsetup_uk')};
CREATE TABLE {$this->getTable('amazonimport_manualsetup_uk')} (
  `manualsetup_id` int(11) unsigned NOT NULL auto_increment,
  `xmlkey` text NOT NULL,
  `manualsetupvalue` text NOT NULL,
  `mapping_override` int(1) NOT NULL,
  `productid` int(11) NOT NULL,
   PRIMARY KEY (`manualsetup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_manualsetup_fr')};
CREATE TABLE {$this->getTable('amazonimport_manualsetup_fr')} (
  `manualsetup_id` int(11) unsigned NOT NULL auto_increment,
  `xmlkey` text NOT NULL,
  `manualsetupvalue` text NOT NULL,
  `mapping_override` int(1) NOT NULL,
  `productid` int(11) NOT NULL,
   PRIMARY KEY (`manualsetup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_manualsetup_de')};
CREATE TABLE {$this->getTable('amazonimport_manualsetup_de')} (
  `manualsetup_id` int(11) unsigned NOT NULL auto_increment,
  `xmlkey` text NOT NULL,
  `manualsetupvalue` text NOT NULL,
  `mapping_override` int(1) NOT NULL,
  `productid` int(11) NOT NULL,
   PRIMARY KEY (`manualsetup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_surestream')};
CREATE TABLE {$this->getTable('amazonimport_surestream')} (
  `job_id` int(11) unsigned NOT NULL auto_increment,
  `page` int(11) NOT NULL,
  `totalsize` int(11) NOT NULL,
  `pagesize` int(11) NOT NULL,
  `jobtype` text NOT NULL,
  `jobcountry` text NOT NULL,
  `submission_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `dependant_on_job_id` int(11) NOT NULL,
   PRIMARY KEY (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


$installer->installEntities();

$installer->endSetup(); 