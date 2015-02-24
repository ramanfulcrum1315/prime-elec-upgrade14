<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('brands')};
CREATE TABLE {$this->getTable('brands')} (
  `brands_id` int(11) unsigned NOT NULL auto_increment,
  `attr_value_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `logo` varchar(255) NOT NULL default '',
  `brand_content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '2',
  `featured` smallint(6) NOT NULL default '2',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`brands_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 