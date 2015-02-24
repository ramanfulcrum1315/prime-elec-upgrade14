<?php

$installer = $this;
$installer->startSetup();

$db = Mage::getSingleton("core/resource")->getConnection("core_write");
$table_prefix = Mage::getConfig()->getTablePrefix();
    


$db->query("TRUNCATE TABLE {$table_prefix}amazonimport_browsenodes");

try {

$installer->run("ALTER TABLE {$this->getTable('amazonimport_browsenodes')} DROP PRIMARY KEY;");


$installer->run("ALTER TABLE  {$this->getTable('amazonimport_browsenodes')} ADD `bnid` INT( 11 ) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (  `bnid` );");



}
catch (Exception $e) {


}

// update the data in the amazonimport_browsenodes table

try {


	// at this point, it would be prudent to insert the data from the sql files.


	$bnsql = array();
	$bnsql[] = Mage::getBaseDir().'/app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_browsenodes24.sql';
									
	foreach($bnsql as $bnlocation){
			$_sql = str_replace("[[replaceme]]","REPLACE INTO {$table_prefix}amazonimport_browsenodes",file_get_contents($bnlocation));
			$_sql = explode("REPLACE INTO",$_sql);
			foreach($_sql as $command){
					if($command != ""){
							$db->query("REPLACE INTO".utf8_decode($command));
					}
			}
									  
									  
	}


} catch (Exception $e) {}

try {
     
    $result = $db->query("select max(browsenode1) mx from 
                         {$this->getTable('amazonimport_categorymapping')};");
    
    $doTheUpdate = false;
    
    foreach ($result as $row) {
        
        if ($row['mx'] == 0) {
            $doTheUpdate = true;
        }
        else if ($row['mx'] < 100000) {
            // Low IDs mean that the database has already been updated
            // Don't update again otherwise the categories will be erased.
            $doTheUpdate = false;
        }
        else {
            $doTheUpdate = true;
        }
        
        break;
    }
    
    if ($doTheUpdate) {

        // now update the cat mapping table
        $installer->run("update {$this->getTable('amazonimport_categorymapping')} cm 
                        set browsenode1 = 
                        (select bnid from {$this->getTable('amazonimport_browsenodes')} bn 
                        where bn.browsenode_id = cm.browsenode1 
                        and bn.country_id = cm.country_id limit 1),
                        browsenode2 = (select bnid from {$this->getTable('amazonimport_browsenodes')} bn 
                        where bn.browsenode_id = cm.browsenode2 
                        and bn.country_id = cm.country_id limit 1)");

    } 
} catch (Exception $e) {}


$installer->installEntities();
$installer->endSetup();
     

?>