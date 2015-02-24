<?php

	$installer = $this;

	$installer->startSetup();

	$installer->run("

		ALTER TABLE `{$this->getTable('sales_flat_quote')}` 
				add column emaildirect_counter int(5) default '0' 
	");

	$installer->endSetup();		
