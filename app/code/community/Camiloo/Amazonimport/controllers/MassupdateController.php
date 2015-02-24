<?php
/**
 * Camiloo Limited
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.camiloo.co.uk/license.txt
 *
 * @category   Camiloo
 * @package    Camiloo_Amazonimport
 * @copyright  Copyright (c) 2011 Camiloo Limited (http://www.camiloo.co.uk)
 * @license    http://www.camiloo.co.uk/license.txt
 */
class Camiloo_Amazonimport_MassupdateController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() {
		$this->loadLayout();
		return $this;
	}
	
	public function indexAction()
    {
    	$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);

		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(
		    Mage::getModel('amazonimport/amazonimport')->loadSessionValue(
		        'camiloo_amazon_massupdate_marketplace'),
		        'camiloo_amazon_massupdate_marketplace');
		
		$this->loadLayout();
		if ($iview)
		{
			$this->_addContent($this->getLayout()->createBlock(
			    'amazonimport/massupdate_edit'));
			$this->_addLeft($this->getLayout()->createBlock(
			    'amazonimport/massupdate_edit_tabs'));
		}
		$this->renderLayout();
	}
	
	public function uploadAction() {
	    ini_set('auto_detect_line_endings', 1);

		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		
		if ($_POST['regenerate'] == 1)
		{
			Mage::getModel('amazonimport/amazonimport')->saveSessionValue(
			    0,'camiloo_amazon_massupdate_upload_inprogress');
		}
		
		if (Mage::getModel('amazonimport/amazonimport')->loadSessionValue(
		    'camiloo_amazon_massupdate_upload_inprogress') < 1)
		{
			$filename = $_FILES['csvfile']['tmp_name'];
			
			if ($filename == '') {
                echo '<script type="text/javascript">alert("No file uploaded");  </script>';	
                exit;
			}

			// create a secure name for the temp file.
			$country = Mage::getModel('amazonimport/amazonimport')
			    ->loadSessionValue('camiloo_amazon_upload_marketplace');
			
			$name = time()."_massupdate_upload_".$country;
			$tempFileName = "/tmp/".$name.".csv";
			
			//
			// Copy filename to tempFileName, discarding the first line
			//
			$totalsize = $this->deleteFirstNLines($filename, $tempFileName, 2);
			$currentpagecount = 0;
			$pagesize = ceil($totalsize / 100);
			
			// instantiate the upload.
			Mage::getModel('amazonimport/amazonimport')->saveSessionValue(
			    1,'camiloo_amazon_massupdate_upload_inprogress');
			Mage::getModel('amazonimport/amazonimport')->saveSessionValue(
			    $totalsize,'camiloo_amazon_massupdate_upload_totalsize');
			Mage::getModel('amazonimport/amazonimport')->saveSessionValue(
			    $currentpagecount,'camiloo_amazon_massupdate_upload_currentpagecount');
			Mage::getModel('amazonimport/amazonimport')->saveSessionValue(
			    $pagesize,'camiloo_amazon_massupdate_upload_pagesize');
			Mage::getModel('amazonimport/amazonimport')->saveSessionValue(
			    $name,'camiloo_amazon_massupdate_upload_filename');
			Mage::getModel('amazonimport/amazonimport')->saveSessionValue(
			    "",'camiloo_amazon_massupdate_upload_timecsv');

			echo '<script type="text/javascript">parent.processfileupload();'
			    .'parent.updatestatusupload("0%",'
			    .'"0 out of $totalsize products processed","");</script>';	
		}
		else if (Mage::getModel('amazonimport/amazonimport')->loadSessionValue(
		    'camiloo_amazon_massupdate_upload_inprogress') < 2)
		{
			// run a 'job' from the upload.
			$totalsize = Mage::getModel('amazonimport/amazonimport')
			    ->loadSessionValue(
			    'camiloo_amazon_massupdate_upload_totalsize');
			$currentpagecount = Mage::getModel('amazonimport/amazonimport')
			    ->loadSessionValue(
			    'camiloo_amazon_massupdate_upload_currentpagecount');
			$pagesize = Mage::getModel('amazonimport/amazonimport')
			    ->loadSessionValue(
			    'camiloo_amazon_massupdate_upload_pagesize');
			$name = Mage::getModel('amazonimport/amazonimport')
			    ->loadSessionValue(
			    'camiloo_amazon_massupdate_upload_filename');

			// move to the next page
			$currentpagecount++;
			Mage::getModel('amazonimport/amazonimport')->saveSessionValue(
			    $currentpagecount,
			    'camiloo_amazon_massupdate_upload_currentpagecount');

			// calculate the amount of results output so far, and the 
			// amount to output this time.
			if (($pagesize * $currentpagecount) < $totalsize)
			{
				$currentcount = $pagesize * $currentpagecount;
				$thistime = $pagesize;
				$percent = "".floor($currentcount / $totalsize * 100)."%";
				$pagesleft = floor(($totalsize - $currentcount) / $pagesize);
			}
			else
			{
				$currentcount = $totalsize;
				$thistime = $totalsize - ($pagesize * ($currentpagecount - 1));
				$percent = "100%";
				$pagesleft = 0;
			}
			$tempFileName = "/tmp/".$name.".csv";
			$handle = fopen($tempFileName, "r");
			$start = microtime();
			$country = Mage::getModel('amazonimport/amazonimport')
			    ->loadSessionValue('camiloo_amazon_upload_marketplace');
			
			for ($i = 1; $i <= $thistime; $i++) {
				
				$value = fgetcsv($handle);
				
				if (sizeof($value) != 16 && sizeof($value) != 1) {
					
					echo '<script type="text/javascript">
						alert("There was an error in your CSV file: Incorrect number of columns: '.sizeof($value).'");
					</script>';
					return;
				}
				else {
					// value row is valid.... 
					// Reference rows -------------------------------
					$row_productId = $value[0];
					$row_sku = $value[1];
					$row_productName = $value[2];
					$row_productType = $value[3];

					$row_canPublish = $value[4];        ///
					$row_useFindOnAmazon = $value[5];   //---
					$row_asinCode = $value[6];          //---
					$row_canUseRepricing = $value[7];   ///
					$row_minimumPricing = $value[8];    ///

					$row_primaryCategory = $value[9];
					$row_secondaryCategory = $value[10];
					$row_itemType = $value[11];
					$row_variationTheme = $value[12];
					$row_condition = $value[13];
					$row_conditionNode = $value[14];

					$row_setupComplete = $value[15]; //---
					
					if ($row_productId != '') {
					    
					    if ($row_canPublish != '' || 
					        $row_canUseRepricing != '' ||
					        $row_minimumPricing != '')
					    {
					        $tblListThis 
					        = "{$table_prefix}amazonimport_listthis_{$country}";
					        
					        $_sql = "SELECT * FROM $tblListThis WHERE productid
					        = $row_productId";
					        $rowFound = false;
					        
					        $result = $db->query($_sql);
					        foreach ($result as $row) {
					            
					            $rowFound = true;
					            break;
					        }
					        
					        if ($rowFound) {
					            // UPDATE
					            $_sql = "UPDATE $tblListThis SET ";
					            
					            if ($row_canPublish != '') {
                                    $_sql .= "is_active = $row_canPublish,";
                                }
                                if ($row_canUseRepricing != '') {
                                    $_sql .= "reprice_enabled = $row_canUseRepricing,";
                                }
                                if ($row_minimumPricing != '') {
                                    $_sql .= "minimum_price = $row_minimumPricing,";
                                }
                                
                                if ($_sql[strlen($_sql)-1] == ',') {
                                    $_sql[strlen($_sql)-1] = " ";
                                }
                                
                                $_sql .= "WHERE productid = $row_productId";
					            
					            $db->query($_sql);
					        }
					        else {
					            
					            if ($row_canPublish == "") {
					                $row_canPublish = "0";
					            }
					            if ($row_canUseRepricing == "") {
					                $row_canUseRepricing = "0";
					            }
					            if ($row_minimumPricing == "") {
					                $row_minimumPricing = "0";
					            }
					            
					            // INSERT
					            $db->query("INSERT INTO $tblListThis 
					                (productid, is_active, reprice_enabled,
                                minimum_price) VALUES
                                ($row_productId, $row_canPublish,
                                $row_canUseRepricing, $row_minimumPricing)");
					        }
                        }
                        
                        if ($row_useFindOnAmazon != '' ||
                            $row_asinCode != '' ||
                            $row_setupComplete != '')
                        {
                            $tblSetup = "{$table_prefix}amazonimport_setup_{$country}";
                        
                            $_sql = "SELECT * FROM $tblSetup WHERE productid
					        = $row_productId";
                            $rowFound = false;
					        
					        $result = $db->query($_sql);
					        foreach ($result as $row) {
					            
					            $rowFound = true;
					            break;
					        }
					        
					        if ($rowFound) {
					            // UPDATE
					            $_sql = "UPDATE $tblSetup SET ";
					            
					            if ($row_useFindOnAmazon != '')
					            {
					                if ($row_useFindOnAmazon == 1)
					                {
					                    $_sql .= "setup_type = 'auto',";
					                }
					                else {
					                    $_sql .= "setup_type = 'manual',";
					                }
					            }
					            if ($row_asinCode != '')
					            {
					                $_sql .= "asincode = '$row_asinCode',";
					            }
					            if ($row_setupComplete != '')
					            {
					                $_sql .= "initial_setup_complete = $row_setupComplete,";
					            }
					            if ($_sql[strlen($_sql)-1] == ',') {
                                    $_sql[strlen($_sql)-1] = " ";
                                }
					            
					            $_sql .= "WHERE productid = $row_productId";
					            
					            $db->query($_sql);
					        }
					        else {
					            // INSERT
					            $db->query("INSERT INTO $tblSetup 
					                (setup_type, asincode, productid,
					                initial_setup_complete) VALUES (
					                '".($row_useFindOnAmazon == 1 
					                    ? "auto" : "manual")."', 
					                '$row_asinCode', $row_productId, $row_setupComplete)");
					        }
					        
                        }
                        
                        if ($row_primaryCategory != ''
                            || $row_secondaryCategory != ''
                            || $row_itemType != ''
                            || $row_condition != ''
                            || $row_conditionNode != '')
                        {
                            $tblCategorise = "{$table_prefix}amazonimport_categorise_{$country}";
                            
                            $_sql = "SELECT * FROM $tblCategorise 
                                WHERE productid = $row_productId";
                            $rowFound = false;
					        
					        $result = $db->query($_sql);
					        foreach ($result as $row) {
					            
					            $rowFound = true;
					            break;
					        }
					        
					        if ($rowFound) {
					            // UPDATE
					            $_sql = "UPDATE $tblCategorise SET ";
					            
					            if ($row_primaryCategory != '') {
					                $_sql .= "browsenode1 = '$row_primaryCategory',";
					            }
					            if ($row_secondaryCategory != '') {
					                $_sql .= "browsenode2 = '$row_secondaryCategory',";
					            }
					            if ($row_itemType != '') {
					                
					                $itArr = explode('/', $row_itemType);
					                if (count($itArr) >= 3) {
					                
					                    $_sql .= "producttype = '{$itArr[0]}',
					                    productdatatype = '{$itArr[count($itArr)-1]}',";
					                
					                }
					            }
					            if ($row_condition != '') {
					                $_sql .= "`condition` = '$row_condition',";
					            }
					            if ($row_conditionNode != '') {
					                $_sql .= "condition_note = '$row_conditionNode',";
					            }
					            if ($_sql[strlen($_sql)-1] == ',') {
                                    $_sql[strlen($_sql)-1] = " ";
                                }
                                
                                $_sql .= "WHERE productid = $row_productId";
					            
					            $db->query($_sql);
					        }
					        else {
					            // INSERT
					            $itArr = explode('/', $row_itemType);
                                if (count($itArr) >= 3) {
					                    
                                    $db->query("INSERT INTO $tblCategorise 
                                        (productid, browsenode1, browsenode2,
                                        productdatatype, producttype, `condition`,
                                        condition_note) VALUES (
                                        $row_productId, '$row_primaryCategory',
                                        '$row_secondaryCategory', 
                                        '{$itArr[count($itArr)-1]}',
                                        '{$itArr[0]}',
                                        '$row_condition', '$row_conditionNode'
                                        )");
					            }
					        }
                        }
                        
                        if ($row_variationTheme != '') {
                            $tblVariations = "{$table_prefix}amazonimport_variations_{$country}";
                            
                            $db->query("DELETE FROM $tblVariations WHERE
                                configurable_product_id = $row_productId");
                            
                            $db->query("INSERT INTO $tblVariations (
                                configurable_product_id, variation_theme)
                                VALUES( $row_productId, 
                                    '$row_variationTheme')");
                        }
					}

					
					
					////// end function
				}
				
			}
			copy($tempFileName, $tempFileName."TMP");
			$this->deleteFirstNLines($tempFileName."TMP", 
			    $tempFileName, $thistime);
				
			$end = microtime();
			$timetaken = $this->microtime_diff($start,$end);
			$timetaken = $timetaken + (1/5);

			$times = Mage::getModel('amazonimport/amazonimport')
			    ->loadSessionValue(
			    'camiloo_amazon_massupdate_upload_timecsv');
            
			if ($percent != "100%")
			{
				$times .= $timetaken.",";
				Mage::getModel('amazonimport/amazonimport')->saveSessionValue(
				    $times,'camiloo_amazon_massupdate_upload_timecsv');
			}
			else 
			{
				$times .= $timetaken;
				Mage::getModel('amazonimport/amazonimport')->saveSessionValue(
				    "",'camiloo_amazon_massupdate_upload_timecsv');
			}

			$times = explode(",",$times);
			$totaltime = 0;

			foreach ($times as $time) {
				$totaltime = $totaltime + $time;
			}

			$timecount = sizeof($times);
			$timeleft = floor(abs(($totaltime / $timecount) * $pagesleft));

			if ($percent != "100%") {
				// we are not complete yet.
				echo '<script type="text/javascript">
					parent.updatestatusupload("'.$percent.'","'.$currentcount.' out of '.$totalsize.' products processed","'.$timeleft.' seconds remaining");		
				</script>';		
			} else {
				// we are complete - display the upload div.
				echo '<script type="text/javascript">
					parent.gouploadcomplete();		
				</script>';		
			}
		}
	}

	public function microtime_diff($start, $end = NULL)
	{
		if (!$end)
		{
			$end = microtime();
		}
		list($start_usec, $start_sec) = explode(" ", $start);
		list($end_usec, $end_sec) = explode(" ", $end);
		$diff_sec = intval($end_sec) - intval($start_sec);
		$diff_usec = floatval($end_usec) - floatval($start_usec);
		return floatval( $diff_sec ) + $diff_usec;
	}

	public function comAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(
		    "com",'camiloo_amazon_massupdate_marketplace');
		$this->indexAction();		
	}
	
	public function ukAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(
		    "uk",'camiloo_amazon_massupdate_marketplace');
		$this->indexAction();		
	}
	
	public function frAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(
		    "fr",'camiloo_amazon_massupdate_marketplace');
		$this->indexAction();		
	}
	
	public function deAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(
		    "de",'camiloo_amazon_massupdate_marketplace');
		$this->indexAction();		
	}

	/**
	 * Deletes the first $numLinesToDelete lines from the $src 
	 * file and outputs the result to $dest.
	 * The file $src isn't modified. $dest is overwritten if it already exists.
	 * Returns the number of lines in the file $src.
	 */
	private function deleteFirstNLines($src, $dest, $numLinesToDelete)
	{
		$handle = fopen($src, "r");
		$handleDest = fopen($dest, "w");
		
		$currCount = 0;

		while ($nextLine = fgets($handle))
		{	    
			$currCount++;
			
			if ($currCount > $numLinesToDelete)
			{
			    fwrite($handleDest, $nextLine);
			}
		}
		fclose($handle);
		fclose($handleDest);

		return $currCount;
	}

}