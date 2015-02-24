<?php

$installer = $this;
$resource = Mage::getSingleton('core/resource');

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$resource->getTableName('navigationmenu/menuitems')};
CREATE TABLE {$resource->getTableName('navigationmenu/menuitems')} (
  `item_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `category_id` int(11) NOT NULL,
  `category_name` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `order` smallint(6) NOT NULL default '0',
  `type` smallint(6) NOT NULL default '0',
  `column` smallint(6) NOT NULL default '0',
  `url` text NOT NULL default '',
  `store_ids` varchar(255) NOT NULL default '',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$resource->getTableName('navigationmenu/contents')};
CREATE TABLE {$resource->getTableName('navigationmenu/contents')} (
  `content_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `text` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sku` varchar(255) NOT NULL default '',
  `block_id` int(11) NOT NULL default '0',
  `status` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 