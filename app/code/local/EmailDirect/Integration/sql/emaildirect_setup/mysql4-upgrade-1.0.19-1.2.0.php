<?php

   $installer = $this;

   $installer->startSetup();

   $installer->run("

      ALTER TABLE `{$this->getTable('sales_flat_quote')}` 
            add column emaildirect_abandoned_date TIMESTAMP NULL,
            add column emaildirect_abandoned_url Text NULL 
   ");

   $installer->endSetup();    
