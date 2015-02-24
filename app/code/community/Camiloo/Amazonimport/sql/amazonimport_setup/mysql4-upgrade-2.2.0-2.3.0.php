<?php

$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE {$this->getTable('amazonimport_flatorders')} DROP PRIMARY KEY , ADD PRIMARY KEY ( `entity_id` , `amazon_order_id` );");                                                        
$installer->installEntities();
$installer->endSetup();

?>                                                          
                        
