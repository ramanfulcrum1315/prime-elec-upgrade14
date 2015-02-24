<?php

$installer = $this;

$installer->startSetup();


$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_errorlog_com')};
CREATE TABLE {$this->getTable('amazonimport_errorlog_com')} (
  `elog_id` int(11) unsigned NOT NULL auto_increment,
  `productid` int(11) NOT NULL,
  `messageid` int(11) NOT NULL,
  `dtid` text NOT NULL,
  `time_submitted` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `submission_type` text NOT NULL,
  `result` text NOT NULL,
  `result_description` text NOT NULL,
   PRIMARY KEY (`elog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_errorlog_uk')};
CREATE TABLE {$this->getTable('amazonimport_errorlog_uk')} (
  `elog_id` int(11) unsigned NOT NULL auto_increment,
  `productid` int(11) NOT NULL,
  `messageid` int(11) NOT NULL,
  `dtid` text NOT NULL,
  `time_submitted` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `submission_type` text NOT NULL,
  `result` text NOT NULL,
  `result_description` text NOT NULL,
   PRIMARY KEY (`elog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_errorlog_fr')};
CREATE TABLE {$this->getTable('amazonimport_errorlog_fr')} (
  `elog_id` int(11) unsigned NOT NULL auto_increment,
  `productid` int(11) NOT NULL,
  `messageid` int(11) NOT NULL,
  `dtid` text NOT NULL,
  `time_submitted` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `submission_type` text NOT NULL,
  `result` text NOT NULL,
  `result_description` text NOT NULL,
   PRIMARY KEY (`elog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_errorlog_de')};
CREATE TABLE {$this->getTable('amazonimport_errorlog_de')} (
  `elog_id` int(11) unsigned NOT NULL auto_increment,
  `productid` int(11) NOT NULL,
  `messageid` int(11) NOT NULL,
  `dtid` text NOT NULL,
  `time_submitted` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `submission_type` text NOT NULL,
  `result` text NOT NULL,
  `result_description` text NOT NULL,
   PRIMARY KEY (`elog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


$installer->installEntities();

$installer->endSetup();