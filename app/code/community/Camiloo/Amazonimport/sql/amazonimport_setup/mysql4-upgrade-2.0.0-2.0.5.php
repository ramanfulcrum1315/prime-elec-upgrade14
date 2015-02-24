<?php

$installer = $this;

$installer->startSetup();


$installer->run("DROP TABLE IF EXISTS {$this->getTable('amazonimport_surestream')};
CREATE TABLE {$this->getTable('amazonimport_surestream')} (
  `job_id` int(11) unsigned NOT NULL auto_increment,
  `page` int(11) NOT NULL,
  `totalsize` int(11) NOT NULL,
  `pagesize` int(11) NOT NULL,
  `jobtype` text NOT NULL,
  `jobcountry` text NOT NULL,
  `submission_id` text NOT NULL,
  `document_id` text NOT NULL,
  `dependant_on_job_id` int(11) NOT NULL,
   PRIMARY KEY (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");



$installer->installEntities();

$installer->endSetup();