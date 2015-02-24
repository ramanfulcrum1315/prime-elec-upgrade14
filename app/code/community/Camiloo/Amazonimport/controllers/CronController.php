<?php
class Camiloo_AmazonImport_CronController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
	}

	public function ordertestAction()
	{
        Mage::getModel('amazonimport/cron')->importArray(NULL, true);
        
	}

    public function upgrade24Action() {
        
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
        
        
        $result = $db->query("select max(browsenode1) mx from  amazonimport_categorymapping;");
        
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
        
        echo $doTheUpdate; die;
        
        /////////////////////////////////////////////////////////////////////////////////



		echo "Upgrading now. . .\n";

		$db->query("ALTER TABLE {$table_prefix}amazonimport_browsenodes DROP PRIMARY KEY;");

		echo "Dropped PK\n";

		$db->query("ALTER TABLE  {$table_prefix}amazonimport_browsenodes ADD `bnid` INT( 11 ) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (  `bnid` );");

		echo "Created PK\n";

try {


	// at this point, it would be prudent to insert the data from the sql files.
	$db = Mage::getSingleton("core/resource")->getConnection("core_write");
	$table_prefix = Mage::getConfig()->getTablePrefix();
	$db->query("TRUNCATE TABLE {$table_prefix}amazonimport_browsenodes");

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

	echo "Refreshed BN table\n";

} catch (Exception $e) {}

try {

// now update the cat mapping table
$installer->run("update {$this->getTable('amazonimport_categorymapping')} cm set browsenode1 = 
 (select bnid from {$this->getTable('amazonimport_browsenodes')} bn where bn.browsenode_id = cm.browsenode1 and bn.country_id = cm.country_id limit 1),
browsenode2 = (select bnid from {$this->getTable('amazonimport_browsenodes')} bn where bn.browsenode_id = cm.browsenode2 and bn.country_id = cm.country_id limit 1)");

} catch (Exception $e) {}


	echo "Done\n"; die;

	}
		
	public function getRemoteXMLFileData($urltograb){
		// this function gets the requested data
		$session = curl_init("$urltograb");
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($session, CURLOPT_TIMEOUT, 15);
		$result = curl_exec($session);
		curl_close($session);
		return simplexml_load_string($result,'SimpleXMLElement', LIBXML_NOCDATA);
	}

	/* To invoke this action visit the URL:
	 * http://www.xx/amazonimport/cron/amazonreinstall
	 */
	public function amazonreinstallAction()
	{
		if(Mage::getSingleton('admin/session')->isLoggedIn()){
			/* insert code to delete resource here */
			$db = Mage::getSingleton("core/resource")->getConnection("core_write");
			$table_prefix = Mage::getConfig()->getTablePrefix();
			$result = $db->query("DELETE FROM {$table_prefix}core_resource where code='amazonimport_setup'");
			die;
		}
		else {
			
			echo "Must be logged in as admin";
		}
	}

	public function checkcatAction() {
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();

		$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_categorymapping
						WHERE country_id='uk' ");

		foreach ($result as $r){
			print_r($r);
			echo "\n";
		}	
        
        $result = $db->query("SELECT category_id FROM {$table_prefix}catalog_category_product
        INNER JOIN {$table_prefix}catalog_category_entity ON entity_id=category_id
                             WHERE product_id = 12011 ORDER BY {$table_prefix}catalog_category_entity.level DESC;");
        
        
		foreach ($result as $r){
			print_r($r);
			echo "\n";
		}	
        
	}
	
	public function amazonreinstall2Action()
	{
			$db = Mage::getSingleton("core/resource")->getConnection("core_write");
			$table_prefix = Mage::getConfig()->getTablePrefix();
			$result = $db->query("DELETE FROM {$table_prefix}core_resource where code='amazonimport_setup'");
	}
	
	
	public function fixduplicateissueAction(){
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
	
		$db->query("ALTER TABLE {$table_prefix}amazonimport_flatorders DROP PRIMARY KEY , ADD PRIMARY KEY ( `entity_id` , `amazon_order_id` );");
			
	}
	
	
	public function tempcheckAction() {
	
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();

		$result = $db->query("SHOW GRANTS FOR CURRENT_USER;");
		
		foreach ($result as $r) {
			print_r($r);
			echo "\n";
		}

		$result = $db->query("SHOW TABLES;");
		
		foreach ($result as $r) {
			print_r($r);
			echo "\n";
		}
		echo "\n";

		$result = $db->query("DESCRIBE amazonimport_listthis_uk;");
		
		foreach ($result as $r) {
			print_r($r);
			echo "\n";
		}
		echo "\n";

		echo mysql_get_client_info();
	}
	
	public function listsetuptableAction() {
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$result = $db->query("select * from amazonimport_setup_com");
		
		foreach ($result as $r){
			print_r($r);
			echo "\n";
		}
		
		die;
	}

	public function cronscheduleAction() {

		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$result = $db->query("select * from {$table_prefix}cron_schedule");
		
		foreach ($result as $r){
			print_r($r);
			echo "\n";
		}
		
		die;
	}
	
	public function checkflatordersAction() {
		//amazonimport_flatorders
		
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();

		$result = $db->query("select * from {$table_prefix}amazonimport_flatorders");
		
		foreach ($result as $r){
			print_r($r);
			echo "\n";
		}
		// = = = = = = = = = = = = = = = = = = = = = = = =
		
		
		
		die;
		
		
		$_sql = "SELECT * FROM {$table_prefix}amazonimport_flatorders WHERE amazon_order_id='105-8874612-3377020'";
		
		echo $_sql."\n";
		
		$result = $db->query($_sql);
		
		if($result->rowCount() > 0){
			echo "Array Len 3";
		}else{
			echo "Empty Array";
		}
		
		if(version_compare(Mage::getVersion(), "1.4.1.0", ">=")){
				
				echo "Version is 1.4.1.0\n";
					
				$amazon_data = Mage::getModel('amazonimport/amazonimport')->flatordersUniqueHelper($orderid);
				
				print_r($amazon_data);
				echo "Version is 1.4.1.0 [2]\n";

			}
	}

	public function resetautoAction()
	{
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
	//	$result = $db->query("UPDATE {$table_prefix}amazonimport_setup_uk set initial_setup_complete=1");
		
		
	//	$result = $db->query("UPDATE {$table_prefix}amazonimport_setup_uk set asincode='', initial_setup_complete=1, setup_type='manual'
		// where asincode <> '' and setup_type = 'auto'");

		
		
		$result = $db->query("UPDATE {$table_prefix}amazonimport_setup_uk set setup_type='manual'");
		
		
		die;
	}
	
	public function upgradetablesAction() 
	{
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
	
		$countries = array("com","uk","de","fr");
		foreach ($countries as $country){
		
			try {
			$db->query("ALTER TABLE {$table_prefix}amazonimport_listthis_$country ADD COLUMN (
			reprice_enabled int(1), calculated_price decimal(10,2), minimum_price decimal(10,2))");
			} catch (Exception $e){
			}
		}
	}

	public function removequotesAction() {

		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		
		$countries = array("com","uk","de","fr");
		foreach ($countries as $country){
		
			try {
				$db->query("UPDATE {$table_prefix}amazonimport_categorise_$country set productdatatype = REPLACE(productdatatype, '\"', ''), 
						producttype = REPLACE(producttype, '\"', '');"); 

			} catch (Exception $e){
			}
		}
	}
	
	
	public function upgradetotwotwentyAction() 
	{
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
	
		$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_log (
			  `log_id` int(11) unsigned NOT NULL auto_increment,
			  `outgoing` text NOT NULL default '',
			  `incoming` text NOT NULL default '',
			  `error` text NOT NULL default '',
			  `message_time` datetime NULL,
			  `sent_to_support` int(1) default 0,
			  PRIMARY KEY (`log_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


		$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}camiloo_magelicense (
			  `sku` text NOT NULL default '',
			  `licensedata` text NOT NULL default ''
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_surestream (
			  `marketplace` VARCHAR(3) NOT NULL default '',
			  `state` VARCHAR(100) NOT NULL default 'WaitingToSubmitProductFeed',
			  `last_state_change` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
			  `submission_id` VARCHAR(25) NOT NULL default '',
			  `orderimport_submission_id` VARCHAR(25) NOT NULL default '',
			  `productimport_submission_id` VARCHAR(25) NOT NULL default '',
			  `running_flag` INT(1) NOT NULL,
			  PRIMARY KEY (`marketplace`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		$countries = array("com","uk","de","fr");

		foreach ($countries as $country){

			$db->query("REPLACE INTO {$table_prefix}amazonimport_surestream (`marketplace`) VALUES ('".$country."');");


			$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_categorise_{$country} (
			  `cat_id` int(11) unsigned NOT NULL auto_increment,
			  `productid` int(11) unsigned NOT NULL,
			  `browsenode1` text NOT NULL default '',
			  `browsenode2` text NOT NULL default '',
			  `category` VARCHAR(255) NOT NULL default '',
			  `productdatatype` VARCHAR(255) NOT NULL default '',
			  `producttype` VARCHAR(255) NOT NULL default '',
			  `condition` text NOT NULL default '',
			  `condition_note` text NOT NULL default '',
			  PRIMARY KEY (`cat_id`),
			  KEY `productid` (`productid`),
			  KEY `producttype` (`producttype`),
			  KEY `productdatatype` (`productdatatype`),
			  KEY `category` (`category`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

			$queryTemp = "CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_listthis_{$country} (
			  `list_id` int(11) unsigned NOT NULL auto_increment,
			  `productid` int(11) unsigned NOT NULL,
			  `is_active` int(1) NOT NULL,
			  reprice_enabled int(1),
			  calculated_price decimal(10,2), 
			  minimum_price decimal(10,2),
			  `is_on_amazon` int(1) NOT NULL,
			  `amazonlink` text NOT NULL default '',
			   PRIMARY KEY (`list_id`),
			  KEY `productid` (`productid`),
			  KEY `is_active` (`is_active`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$db->query($queryTemp);

			$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_mapping_{$country} (
			  `mapping_id` int(11) unsigned NOT NULL auto_increment,
			  `xmlkey` VARCHAR(1000) NOT NULL,
			  `mappingvalue` text NOT NULL,
			   PRIMARY KEY (`mapping_id`),
			  KEY `xmlkey` (`xmlkey`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

			$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_variations_{$country} (
			  `variation_id` int(11) unsigned NOT NULL auto_increment,
			  `configurable_product_id` int(11) NOT NULL,
			  `variation_theme` text NOT NULL,
			   PRIMARY KEY (`variation_id`),
			  KEY `configurable_product_id` (`configurable_product_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

			$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_setup_{$country} (
			  `setup_id` int(11) unsigned NOT NULL auto_increment,
			  `setup_type` VARCHAR(10) NOT NULL,
			  `asincode` text NOT NULL,
			  `productid` int(11) unsigned NOT NULL,
			  `initial_setup_complete` int(1) NOT NULL,
			   PRIMARY KEY (`setup_id`),
			  KEY `productid` (`productid`),
			  KEY `initial_setup_complete` (`initial_setup_complete`),
			  KEY `setup_type` (`setup_type`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

			$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_manualsetup_{$country} (
			  `manualsetup_id` int(11) unsigned NOT NULL auto_increment,
			  `xmlkey` VARCHAR(1000) NOT NULL,
			  `manualsetupvalue` text NOT NULL,
			  `mapping_override` int(1) NOT NULL,
			  `productid` int(11) NOT NULL,
			   PRIMARY KEY (`manualsetup_id`),
			  KEY `productid` (`productid`),
			  KEY `xmlkey` (`xmlkey`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

			$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_errorlog_{$country} (
			  `elog_id` int(11) unsigned NOT NULL auto_increment,
			  `productid` int(11) NOT NULL,
			  `messageid` int(11) NOT NULL,
			  `dtid` VARCHAR(255) NOT NULL,
			  `time_submitted` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
			  `submission_type` VARCHAR(255) NOT NULL,
			  `result` VARCHAR(255) NOT NULL,
			  `result_description` text NOT NULL,
			   PRIMARY KEY (`elog_id`),
			  KEY `productid` (`productid`),
			  KEY `messageid` (`messageid`),
			  KEY `dtid` (`dtid`),
			  KEY `submission_type` (`submission_type`),
			  KEY `result` (`result`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		}

		
		$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_surestream_shipping (
					  `marketplace` VARCHAR(3) NOT NULL default '',
					  `amazon_order_id` VARCHAR(100) NOT NULL default '',
					  `last_update` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
					  `carrier_name` VARCHAR(50) NOT NULL default '',
					  `tracking_number` VARCHAR(50) NOT NULL default '',
					  PRIMARY KEY (`amazon_order_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;");	


		$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_flatorders (
					  `entity_id` int(11) NOT NULL default 0,
					  `amazon_order_id` VARCHAR(100) NOT NULL default '',
					  `amazon_marketplace` VARCHAR(50) NOT NULL default '',
					  PRIMARY KEY (`entity_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


		$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_browsenodes (
							  `browsenode_id` int(11) NOT NULL default 0,
							  `country_id` varchar(3),
							  `category_name` text,
							  `category_tree_location` text,
							  `query` text,
							  PRIMARY KEY (`browsenode_id`,`country_id`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8");

		$db->query("CREATE TABLE IF NOT EXISTS  {$table_prefix}amazonimport_fielddescriptions (
							  `fieldname` varchar(160) NOT NULL default '',
							  `country_id` varchar(3),
							  `category_name` varchar(160),
							  `value` text,
							  `accepted_values` text,
							  `example` text,
							  `is_required` text,
							  PRIMARY KEY (`fieldname`,`country_id`,`category_name`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8");
	
		
		$db->query("CREATE TABLE IF NOT EXISTS  {$table_prefix}amazonimport_categorymapping (
							  `category_id` int(11) NOT NULL default 0,
							  `country_id` varchar(3),
							  `browsenode1` int(11),
							  `browsenode2` int(11),
							  `itemtype` text,
							  `variation_theme` text,
							  `inherited` text,
							  `level` text,
							  `condition` text,
							  `condition_note` text,
							  PRIMARY KEY (`category_id`,`country_id`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8");
			
	$db->query("TRUNCATE TABLE {$table_prefix}amazonimport_browsenodes");
	$db->query("TRUNCATE TABLE {$table_prefix}amazonimport_fielddescriptions");
											

        try {

	$bnsql = array();
	$bnsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_browsenodes_uk.sql';
	$bnsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_browsenodes_fr.sql';
	$bnsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_browsenodes_de.sql';
	$bnsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_browsenodes_com.sql';
	$bnsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_browsenodes_com2.sql';
									
	foreach($bnsql as $bnlocation){
			$_sql = str_replace("[[replaceme]]","REPLACE INTO {$table_prefix}amazonimport_browsenodes",file_get_contents($bnlocation));
			$_sql = explode("REPLACE INTO",$_sql);
			foreach($_sql as $command){
					if($command != ""){
							$db->query("REPLACE INTO".$command);
					}
			}
									  
									  
	}
					   
	$fdsql = array(); 
	$fdsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_fielddescriptions_uk.sql';
	$fdsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_fielddescriptions_de.sql';
	$fdsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_fielddescriptions_fr.sql';
	$fdsql[] = Mage::getBaseDir().'/'.'app/design/adminhtml/default/default/template/amazonimport/mappings/amazonimport_fielddescriptions_com.sql';
	foreach($fdsql as $fdlocation){
			$_sql = str_replace("[[replaceme]]","REPLACE INTO {$table_prefix}amazonimport_fielddescriptions",file_get_contents($fdlocation));
			$_sql = explode("REPLACE INTO",$_sql);
			foreach($_sql as $command){
					if($command != ""){
							$db->query("REPLACE INTO".$command);
					}
			}
	}	

               echo "All tables created OK"; die;

        } catch (Exception $e) {

            print_r($e); die;
        }
				
				
	}

	public function eraseerrorlogAction()
	{
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$result = $db->query("DELETE FROM {$table_prefix}amazonimport_errorlog_uk");
		die;
		//$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$productid.",'Product')");
	}
	
	public function eraseerrorlogdeAction()
	{
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$result = $db->query("DELETE FROM {$table_prefix}amazonimport_errorlog_de");
		die;
		//$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$productid.",'Product')");
	}
	
	public function eraseerrorlogfrAction()
	{
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$result = $db->query("DELETE FROM {$table_prefix}amazonimport_errorlog_fr");
		die;
		//$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$productid.",'Product')");
	}
	
	public function eraseerrorlogcomAction()
	{
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$result = $db->query("DELETE FROM {$table_prefix}amazonimport_errorlog_com");
		die;
		//$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$productid.",'Product')");
	}

	public function showshipmentsAction() {
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();

		$result = $db->query("select * from {$table_prefix}amazonimport_surestream_shipping");

		$i = 0;
		
		foreach ($result as $r){

			print_r($r);
	

			$i++;
		}
		
		
		echo $i." rows\n\n";
		
	
	}

	public function resetorderimporterAction()
	{
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();

		$result = $db->query("update {$table_prefix}amazonimport_surestream set orderimport_submission_id='', productimport_submission_id=''");

		echo "<p>The order importer has been reset.</p>";

		die;
	}
	
	public function setsurestreamAction() {
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$sql = "update {$table_prefix}amazonimport_surestream set state='WaitingToSubmitImageFeed' where marketplace='uk'";
		$result = $db->query($sql);
		
		echo "The state has been set";
		
		die;
	}
    
    public function checkfeeddeAction() {
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Client.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/RequestReportRequest.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/RequestReportResponse.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportRequestListRequest.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportRequestListResponse.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportRequest.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportResponse.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/IdList.php');
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
        
		$merchantid = Mage::getStoreConfig('amazonint/amazonde/mid');
		$marketplaceid = Mage::getModel('amazonimport/amazonimport')->getMarketplaceId('de');
		$feedid = $_REQUEST['feedid'];
        
        
		$amazonlink = Mage::getModel('amazonimport/amazonlink');
        
        
		$resultFileName = $amazonlink->getFeedResult(
                                                     $feedid, 'de', $marketplaceid, $merchantid,
                                                     "https://mws.amazonservices.de");
        
		if("NOTREADY" == $resultFileName) {
            
			echo "Feed result is not ready";
		}
		else {
            
			echo file_get_contents($resultFileName);
		}
	}
    
    public function checkfeedfrAction() {
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Client.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/RequestReportRequest.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/RequestReportResponse.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportRequestListRequest.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportRequestListResponse.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportRequest.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportResponse.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/IdList.php');
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
        
		$merchantid = Mage::getStoreConfig('amazonint/amazonfr/mid');
		$marketplaceid = Mage::getModel('amazonimport/amazonimport')->getMarketplaceId('fr');
		$feedid = $_REQUEST['feedid'];
        
        
		$amazonlink = Mage::getModel('amazonimport/amazonlink');
        
        
		$resultFileName = $amazonlink->getFeedResult(
                                                     $feedid, 'fr', $marketplaceid, $merchantid,
                                                     "https://mws.amazonservices.fr");
        
		if("NOTREADY" == $resultFileName) {
            
			echo "Feed result is not ready";
		}
		else {
            
			echo file_get_contents($resultFileName);
		}
	}
    
    public function checkfeedcomAction() {
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Client.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/RequestReportRequest.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/RequestReportResponse.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportRequestListRequest.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportRequestListResponse.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportRequest.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportResponse.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/IdList.php');
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
        
		$merchantid = Mage::getStoreConfig('amazonint/amazoncom/mid');
		$marketplaceid = Mage::getModel('amazonimport/amazonimport')->getMarketplaceId('com');
		$feedid = $_REQUEST['feedid'];
        
		$amazonlink = Mage::getModel('amazonimport/amazonlink');
        
		$resultFileName = $amazonlink->getFeedResult($feedid, 'com', $marketplaceid, $merchantid,
                                                     "https://mws.amazonservices.com");
        
		if ("NOTREADY" == $resultFileName) {
            
			echo "Feed result is not ready";
		}
		else {
            
			echo file_get_contents($resultFileName);
		}
	}

	public function checkfeedukAction() {
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Client.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/RequestReportRequest.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/RequestReportResponse.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportRequestListRequest.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportRequestListResponse.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportRequest.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportResponse.php');
		require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/IdList.php');
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();

		$merchantid = Mage::getStoreConfig('amazonint/amazonuk/mid');
		$marketplaceid = Mage::getModel('amazonimport/amazonimport')->getMarketplaceId('uk');
		$feedid = $_REQUEST['feedid'];


		$amazonlink = Mage::getModel('amazonimport/amazonlink');


		$resultFileName = $amazonlink->getFeedResult(
			$feedid, 'uk', $marketplaceid, $merchantid,
						"https://mws.amazonservices.co.uk");

		if("NOTREADY" == $resultFileName) {

			echo "Feed result is not ready";
		}
		else {
	
			echo file_get_contents($resultFileName);
		}
	}
	
	public function getsurestreamAction() {
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();

		$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_surestream");
		
		foreach ($result as $r){
			print_r($r);
			echo "\n";
		}
		die;
	}
	
	public function restoresurestreamAction() {
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
	
		$countries = array("com","uk","de","fr");
		foreach ($countries as $country){
				
			$db->query("INSERT INTO {$table_prefix}amazonimport_surestream (`marketplace`) VALUES ('".$country."');");
			
		}
		die;
						
	}
	
	public function microtime_float(){
		list ($msec, $sec) = explode(' ', microtime());
		$microtime = (float)$msec + (float)$sec;
		return $microtime;
	}

	public function synceverythingAction() {
		//=========
	
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
	
		$starttime = $this->microtime_float();
		//=========
		
		$maxruntime = 30;
		
		$country = 'uk';

		$queryString = "select setup.productid from {$table_prefix}amazonimport_setup_".$country." as setup, {$table_prefix}amazonimport_listthis_$country as listthis
			WHERE setup.initial_setup_complete = 1 AND setup.productid = listthis.productid AND listthis.is_active=1 AND setup.productid not in 
			(select productid from {$table_prefix}amazonimport_errorlog_".$country." where submission_type='Product')";

		$result = $db->query($queryString);

		while($row = $result->fetch(PDO::FETCH_ASSOC && $runtime < $maxruntime)){
			$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$row['productid'].",'Product')");
			$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$row['productid'].",'Image')");
			// is product configurable?
			if(Mage::getModel('catalog/product')->load($row['productid'])->getTypeId() != "simple"){
				$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$row['productid'].",'Relation')");
			}
			$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$row['productid'].",'Stock')");
			$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$row['productid'].",'Price')");

		$endtime = $this->microtime_float();
		$runtime = round($endtime - $starttime);
		


		}
	}
	
	public function showerrorlogdeAction() {
		$debugstr = "";
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();

		$result = $db->query("select * from {$table_prefix}amazonimport_errorlog_de");

		$i = 0;
		
		foreach ($result as $r){

			print_r($r);
	

			$i++;
		}
		
		
		echo $i." rows\n\n";
		
	}
	
	public function showerrorlogfrAction() {
		$debugstr = "";
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();

		$result = $db->query("select * from {$table_prefix}amazonimport_errorlog_fr");

		$i = 0;
		
		foreach ($result as $r){

			print_r($r);

			$i++;
		}
		
		
		echo $i." rows\n\n";
		
	}
	
	public function showerrorlogcomAction() {
		$debugstr = "";
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();

		$result = $db->query("select * from {$table_prefix}amazonimport_errorlog_com");

		$i = 0;
		
		foreach ($result as $r){

			print_r($r);
	

			$i++;
		}
		
		
		echo $i." rows\n\n";
		
	}
	
	public function showerrorlogAction() {
		$debugstr = "";
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();

		$result = $db->query("select * from {$table_prefix}amazonimport_errorlog_uk "); //where submission_type='Product'

		$i = 0;
		
		foreach ($result as $r){

			$valueToPrint = var_export($r, TRUE);
			
		//	if (strstr($valueToPrint, 'Product')) {
				print_r($r);
				
				
				$i++;
			//}

		}
		
		
		echo $i." rows\n\n";
		
	}

	//amazonimport_mapping_com
	public function mappingcomAction() {
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		
		$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_listthis_uk WHERE productid=523448");
		
		foreach ($result as $s) {
			if (isset($s['is_active'])) {
				echo $s['is_active'] == 1 ? "true" : "false";
			}
		}
		
		die;
	}


	public function getmodulesAction() {

		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());

		print_r($modules);

		echo "\n".in_array("Netz98_Picklist", $modules);

		die;

	}

	public function baseurlAction()
	{

		echo Mage::getStoreConfig('web/secure/base_url');



		$xml = $this->getRemoteXMLFileData("http://service.camiloo.co.uk/inc/modules/licensing.php?sku=CAM-AMZ20&domain="
		.Mage::getStoreConfig('web/secure/base_url'));
			
		print_r($xml);
			
		die;

	}

	public function testshipmentAction()
	{
		$amzTest = "202-5700953-4504360";
		$fulfilDate = date("c");

		try
		{

			if (version_compare(Mage::getVersion(), "1.4.1.0", ">="))
			{
				$orderEntityId = Mage::getModel('amazonimport/amazonimport')->flatordersGetOrderEntityId($amzTest);
					
				$order = Mage::getModel('sales/order')->load($orderEntityId);

				if($order) {

					$shipments = $order->getShipmentsCollection();
					 
					if($shipments) {
						foreach($shipments as $shipment){
							$fulfilDate = $shipment->getCreatedAt();
								
							break;
						}

					}

				}
					
			}
			else
			{
				$amazon_resource = Mage::getResourceModel('amazonimport/amazonimport_orderDetails_Collection');
				$amazon_data = $amazon_resource->addAttributeToSelect('amazon_order_id')
				->setAmazonOrderFilter($amzTest)->load();
					
				$order = null;

				if($amazon_data) {
					foreach($amazon_data as $amazonorder)
					{
						$order = Mage::getModel('sales/order')->load($amazonorder->getParentId());

						break;
					}
					if($order) {

						$shipments = $order->getShipmentsCollection();

						if($shipments) {
							foreach($shipments as $shipment){
								$fulfilDate = $shipment->getCreatedAt();

								break;
							}
								
						}
							
					}
				}
			}

		}
		catch (Exception $e) {
		}

		// Space -> T, append +00:00
		$fulfilDate = str_replace(' ', 'T', $fulfilDate).'+00:00';
			
		echo $fulfilDate;
			
		die;
			
	}
	
	public function createlicensetableAction() {
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		
		$result = $db->query("create table {$table_prefix}camiloo_magelicense(sku text, licensedata text)");
	}
}
?>
