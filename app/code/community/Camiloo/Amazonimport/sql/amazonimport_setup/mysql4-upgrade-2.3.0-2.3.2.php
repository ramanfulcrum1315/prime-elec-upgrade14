<?php

$installer = $this;
$installer->startSetup();


$installer->run("DELETE FROM {$this->getTable('amazonimport_errorlog_uk')}");
$installer->run("DELETE FROM {$this->getTable('amazonimport_errorlog_de')}");
$installer->run("DELETE FROM {$this->getTable('amazonimport_errorlog_fr')}");
$installer->run("DELETE FROM {$this->getTable('amazonimport_errorlog_com')}");


    try {

        $installer->run("ALTER TABLE
                               {$this->getTable('amazonimport_errorlog_uk')}
                               ADD UNIQUE stopclash ( `productid` , `submission_type`);");
        
    }
    catch (Exception $e) {}
    
    
    try {
        
        $installer->run("ALTER TABLE
                               {$this->getTable('amazonimport_errorlog_de')}
                               ADD UNIQUE stopclash ( `productid` , `submission_type`);");
        
    }
    catch (Exception $e) {}
        
    try {
            
        $installer->run("ALTER TABLE
                               {$this->getTable('amazonimport_errorlog_fr')}
                               ADD UNIQUE stopclash ( `productid` , `submission_type`);");
            
    }
    catch (Exception $e) {}
            
    try {
                
        $installer->run("ALTER TABLE
                               {$this->getTable('amazonimport_errorlog_com')}
                               ADD UNIQUE stopclash ( `productid` , `submission_type`);");
                
                
    }
    catch (Exception $e) {}

/*
$installer->run("ALTER TABLE {$this->getTable('amazonimport_browsenodes')} CHANGE `category_tree_location` `category_tree_location` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");

$installer->run("ALTER TABLE {$this->getTable('amazonimport_browsenodes')} DROP PRIMARY KEY , ADD PRIMARY KEY ( `browsenode_id` , `country_id` , `category_tree_location` );");
*/
$installer->installEntities();
$installer->endSetup();

?>