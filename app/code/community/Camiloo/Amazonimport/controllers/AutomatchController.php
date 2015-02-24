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

class Camiloo_Amazonimport_AutomatchController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout();
		return $this;
	}


	public function microtime_diff( $start, $end=NULL ) {
		if( !$end ) {
			$end= microtime();
		}
		list($start_usec, $start_sec) = explode(" ", $start);
		list($end_usec, $end_sec) = explode(" ", $end);
		$diff_sec= intval($end_sec) - intval($start_sec);
		$diff_usec= floatval($end_usec) - floatval($start_usec);
		return floatval( $diff_sec ) + $diff_usec;
	}
	
	public function getproductgridAction(){
	 $this->getResponse()->setBody($this->getLayout()->createBlock('amazonimport/automatch_edit_tab_productlist')->setId('amazonimportGrid')->toHtml());	
	}
		
	public function masslistonamznoAction(){
		$count = 0;
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
		$listThisTable = $table_prefix."amazonimport_listthis_".$country;
		$setupTable = $table_prefix."amazonimport_setup_".$country;
		foreach($this->getRequest()->getPost('entity_id') as $productid){
			$db->query("UPDATE $listThisTable set is_active='0' where productid=$productid");
			$count++;
		}
		
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__($count.' products have been set to List On Amazon: No. Please note that this action does not remove items from Amazon - for security reasons this software cannot delete Amazon listings.'));
		$this->_redirect('*/*/');

	}
	public function massmovetoadvancedAction(){
		$count = 0;
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
		$listThisTable = $table_prefix."amazonimport_listthis_".$country;
		$setupTable = $table_prefix."amazonimport_setup_".$country;
		foreach($this->getRequest()->getPost('entity_id') as $productid){
			
			$saving = Mage::getModel('amazonimport/amazonimportsetup'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_selection_marketplace'))->getCollection()->addFieldToFilter('productid',$productid);
			if(sizeof($saving) == 0){
				$saving = Mage::getModel('amazonimport/amazonimportsetup'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_selection_marketplace'));
				$saving->setData('productid',$productid);
				$saving->setData('initial_setup_complete',0);
				$saving->setData('setup_type', 'manual');
				$saving->save();
			}
			else {
					
				foreach($saving as $s1) {
					$s1->setData('setup_type', 'manual');
					$s1->save();
					break;
				}
			}
			
			$count++;
		}
	
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__($count.' products have been moved to the Manual setup type and are now available for configuration under Review Products.'));
		$this->_redirect('*/*/');
	}
	public function masserasematchesAction(){	
		$count = 0;
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
		$listThisTable = $table_prefix."amazonimport_listthis_".$country;
		$setupTable = $table_prefix."amazonimport_setup_".$country;
		
		foreach($this->getRequest()->getPost('entity_id') as $productid){
			$db->query("UPDATE $setupTable set asincode='' where productid=$productid");
			$count++;
		}
		
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__($count.' search matches have been removed.'));
		$this->_redirect('*/*/');
	}
	public function masstogglerepricingAction(){
		$count = 0;
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
		$listThisTable = $table_prefix."amazonimport_listthis_".$country;
		$setupTable = $table_prefix."amazonimport_setup_".$country;
		foreach($this->getRequest()->getPost('entity_id') as $productid){
			$result = $db->query("SELECT * FROM $listThisTable where productid=$productid");
			$row = $result->fetch(PDO::FETCH_ASSOC);
			if($row['reprice_enabled'] == 1){
				$db->query("UPDATE $listThisTable SET reprice_enabled='0' where productid=$productid");
			}else{
				$db->query("UPDATE $listThisTable SET reprice_enabled='1' where productid=$productid");
			}
			$count++;
			
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__($count.' products have had repricing toggled.'));
		$this->_redirect('*/*/');
	}
	
	public function erasematchAction()
	{
		try {
			$id = $this->getRequest()->getParam('entity_id');
			
			
			// Delete asincode from setup table where product id = $id
			
			$db = Mage::getSingleton("core/resource")->getConnection("core_write");
			$table_prefix = Mage::getConfig()->getTablePrefix();
			$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
			$listThisTable = $table_prefix."amazonimport_listthis_".$country;
			$setupTable = $table_prefix."amazonimport_setup_".$country;
			
			$db->query("UPDATE $setupTable set asincode='' where productid=$id");
			
			
		}
		catch(Exception $e1) {
			
			
		}
		$this->_redirect('*/*/');
		
	}

	public function indexAction()
	{
		$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);
		$mdl = "";
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('amazonimport/amazonimportlistthis'.Mage::getModel('amazonimport/amazonimport')
			->loadSessionValue('camiloo_amazon_automatch_marketplace'))->getCollection()->addFieldToFilter('is_active',1);

		foreach($model as $mdl)
		{
			break;
		}
		
		if(is_object($mdl))
		{

			Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')
				->loadSessionValue('camiloo_amazon_automatch_marketplace'),'camiloo_amazon_automatch_marketplace');
			$this->loadLayout();
			
			if($iview)
			{
				$this->_addContent($this->getLayout()->createBlock('amazonimport/automatch_edit'));
				$this->_addLeft($this->getLayout()->createBlock('amazonimport/automatch_edit_tabs'));
			}
			$this->renderLayout();

		}
		else
		{	
			Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')
				->loadSessionValue('camiloo_amazon_automatch_marketplace'),'camiloo_amazon_automatch_marketplace');
			if($iview){
				Mage::getSingleton('adminhtml/session')->addError(
					Mage::helper('amazonimport')->__('You do not currently have any products selected to list on '
						.'Amazon in this country.<br />Before you can use \'Find Products on Amazon\' you must first select '
						.'the products which you would like to list.<br />To do this, please visit the menu item entitled Select Products To List.'));
			}
			$this->loadLayout();
			$this->renderLayout();
		}
	}

	public function runmatchAction()
	{

		/* Automatch v2.0 */
		/* Post params: autoapprovesingles	productid */

		/* Connect to Seller Central, navigate to the search form */

		/* Now submit a search query, get the result */

		/* Parse the result */

		/* Output the result */

	}

	/**
	 * 
	 * This function splits the results up as it's told to.
	 */
	public function breakitup($colim, $delim, $input)
	{
		// 
		$tempstr = explode("$colim","$input");
			
		foreach($tempstr as $key => $value){
			if($key > 0){
				$tempstr2 = explode("$delim","$value");
				$output[$key-1] = $tempstr2[0];
			}
		}
 
		if(!isset($output)){
			 
			return array();
				
		}else{
			return $output;
		}

	}


	/**
	 * Called on clicking 'Begin Search Process' on the Quick Setup screen. 
	 *
	 */
	public function startautomatchAction(){

		/*
		 * This will initialise some values needed for the Quick Setup process. 
		 * Also, we take in some values passed via the form. 
		 */

		ini_set("display_errors","on");
		$data = $this->getRequest()->getPost();
		
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
		if(!isset($data['attributes'])){
			$data['attributes']	= "";
		}
		$_SESSION['qsw_'.$country.'_attributes'] = serialize($data['attributes']);	
		$_SESSION['qsw_'.$country.'_merchantid'] = $data['merchantid'];
		
		if(isset($data['autoapprove'])){
			$_SESSION['qsw_'.$country.'_autoapprove'] = $data['autoapprove'];	
		}else{
			$_SESSION['qsw_'.$country.'_autoapprove'] = 0;
			$data['autoapprove'] = 0;
		}
		
		if(isset($data['autoskip'])){
			$_SESSION['qsw_'.$country.'_autoskip'] = $data['autoskip'];	
		}else{
			$_SESSION['qsw_'.$country.'_autoskip'] = 0;	
			$data['autoskip'] = 0;
		}
		
		if(isset($data['ignoreexisting'])){
			$_SESSION['qsw_'.$country.'_ignoreexisting'] = $data['ignoreexisting'];	
		}else{
			$_SESSION['qsw_'.$country.'_ignoreexisting'] = 0;	
			$data['ignoreexisting'] = 0;
		}
		
		if(isset($data['changetype'])){
			$_SESSION['qsw_'.$country.'_changetype'] = $data['changetype'];
		}else{
			$_SESSION['qsw_'.$country.'_changetype'] = 0;	
			$data['changetype'] = 0;
		}
		$cereal = serialize($data);
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue($cereal,'camiloo_amazon_automatch_settings');
		
		
		
		$_SESSION['qsw_'.$country.'_searchmode'] = $data['searchmode'];
		
		
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		
		if ($data['attributes'] == "")
		{
			echo "<script type='text/javascript'>
					alert('You must select at least one attribute to use when searching');
					parent.$('ignition-indicator').style.display = 'none';
				</script>";
		}
		else
		{
			$product = null;
			
			$listThisTable = $table_prefix."amazonimport_listthis_".$country;
			$setupTable = $table_prefix."amazonimport_setup_".$country;
			
			while (true)
			{
				# BUGFIX EEM-153-65403-c Allow me to choose a new match is wrong way around. Changed operator from == to !=
				if ($data['ignoreexisting'] != 1) {
						
					$sqlTemp = "SELECT * FROM $listThisTable as t1, $setupTable as t2 WHERE t1.is_active='1' and 
						t1.productid=t2.productid and t2.setup_type='auto' and t2.asincode='' ORDER BY t1.productid";
				}
				else
				{
					$sqlTemp = "SELECT * FROM $listThisTable as t1, $setupTable as t2 WHERE t1.is_active='1' and 
						t1.productid=t2.productid and t2.setup_type='auto' ORDER BY t1.productid";
				}
				
				$result = $db->query($sqlTemp);
				$size = $result->rowCount();
				if ($size == 0)
				{
					echo "<script type='text/javascript'>alert('There are currently no products waiting to be setup "
						."from your selection of products to list on Amazon');parent.$('ignition-indicator').style.display = 'none';</script>";
					die;
				}

				$startpoint = 0;
				$result = $db->query("$sqlTemp LIMIT $startpoint,1");
				
				$row = $result->fetch();
				
				if (!$row)
				{
					break;	// No more results
				}
				
				$product = Mage::getModel('catalog/product')->load($row['productid']);
				
				if (is_object($product))
				{
					// Found a product, we can present this to the user
					break;
				}
			}
			
			if (!is_object($product))
			{
				echo "<script type='text/javascript'>alert('There are currently no products waiting to be setup from your selection of products to list on Amazon');parent.$('ignition-indicator').style.display = 'none';</script>";
				die;
			}
			# BUGFIX EEM-153-65403-b - Apostrophe in product SKU caused system to freeze due to javascript malformation.
			echo "<script type='text/javascript'>
					parent.$('footerProgressCount').innerHTML = '$startpoint';
					parent.$('footerProgressOf').innerHTML = '".$size."';
					parent.$('footerProgressMatches').innerHTML = '0';
					parent.$('currentstatus').innerHTML = 'Initialising Setup';
					parent.$('searchresultoutputpane').innerHTML = '';
					parent.$('currentProductName').innerHTML = '';
					parent.$('currentProductSKU').innerHTML = '';
					parent.$('currentProductID').innerHTML = '';
					parent.$('currentProductImage').src = '';
					parent.Effect.Fade('automatchsettings');
					parent.Effect.Appear('automatcher');
					parent.$('currentstatus').innerHTML = 'Loading first product';
						parent.$('currentProductName').innerHTML = '".$this->sanitizeName($product->getName())."<br /> <a href=\"".str_replace("'","",$product->getProductUrl())."\" target=\"_blank\">View Product on website</a> /  <a href=\"".str_replace("'","",$this->getUrl('adminhtml/catalog_product/edit',array('id'=>$product->getId())))."/\" target=\"_blank\">View Product in admin area</a>';
					parent.$('currentProductSKU').innerHTML = '".str_replace("'","",$product->getSku())."';
					parent.$('currentProductID').innerHTML = '".$product->getId()."';
					parent.$('fetchproductid').value = '".$product->getId()."';
					parent.$('saveProgressCount').value = '$startpoint';
					parent.$('saveProgressMatches').value = '0';
					parent.$('saveproductcount').value = '".$size."';
					parent.$('savepercentage').value = '0';
					parent.$('saveproductid').value = '".$product->getId()."';
					parent.$('chosenasin').value = '0';";
				
			if ($product->getThumbnail() != "")
			{
				echo "parent.$('currentProductImage').src = '".Mage::getBaseUrl('media')."catalog/product".$product->getThumbnail()."';";
			}
			else
			{
				echo "parent.$('currentProductImage').src = '".Mage::getBaseUrl('skin')."frontend/default/default/images/no_image.jpg';";
			}
				
			echo "parent.$('currentstatus').innerHTML = 'Getting results ...';
					setTimeout(\"parent.$('fetchform').submit();\",500);
				</script>";
		}
	}

	public function sanitizeName($nameTemp) {
	
		$nameTemp = str_replace("'", "", $nameTemp);
		$nameTemp = str_replace("\n", " ", $nameTemp);
		return $nameTemp;	
	}
		
	public function fetchAction() {

		$data = unserialize(Mage::getModel('amazonimport/amazonimport')
		    ->loadSessionValue('camiloo_amazon_automatch_settings'));

		$postdata = $this->getRequest()->getPost();

		$product = Mage::getModel('catalog/product')
		    ->load($postdata['fetchproductid']);
	
		$searcharray = array(
		    "uk" => "http://www.amazon.co.uk/mn/search/ajax/?rh=i%3Aaps%2Ck%3A",
			"com" => "http://www.amazon.com/mn/search/ajax/?rh=i%3Aaps%2Ck%3A",
			"fr" => "http://www.amazon.fr/mn/search/ajax/?rh=i%3Aaps%2Ck%3A",
			"de" => "http://www.amazon.de/mn/search/ajax/?rh=i%3Aaps%2Ck%3A");
		
		$dparray = array("uk" => "http://www.amazon.co.uk/dp/",
                       "com" => "http://www.amazon.com/dp/",
                       "fr" => "http://www.amazon.fr/dp/",
                       "de" => "http://www.amazon.de/dp/");
		
		$country = Mage::getModel('amazonimport/amazonimport')
		    ->loadSessionValue('camiloo_amazon_automatch_marketplace');
		
		$searchtermarray = array();
		if ($data['attributes'] != "") {
			if (is_array($data['attributes'])) {
				foreach ($data['attributes'] as $attribute) {
					$searchstring = $this->getMappedDataFromProduct(
					    $attribute, $product);
					
					if ($searchstring != "") {
						$searchtermarray[] = $searchstring;
					}	
				}
			}else{
				$searchstring = $this->getMappedDataFromProduct($data['attributes'],$product);
				if($searchstring != ""){
					$searchtermarray[] = $searchstring;
				}	
			}
			
		}
		$matches = 0;


		foreach ($searchtermarray as $key => $searchterm) {
			if ($searchterm == "N/A") {
				unset($searchtermarray[$key]);
			}
		}

		foreach ($searchtermarray as $searchterm) {
			if ($matches == 0) {
				
				$resultValues = array();
				$colcounter = 0;
				$variations = ($data['searchmode'] == 2) ? true : false;
				$results = "";
				
				if($_SESSION['qsw_'.$country.'_merchantid'] != ""){
					$searchUrls[] = $searcharray[$country]. urlencode($searchterm). "%2Cp_6%3A".$_SESSION['qsw_'.$country.'_merchantid']."&page=1&keywords=". urlencode($searchterm). "&ie=UTF8&section=ATF,BTF";
				}else{
					$searchUrls[] = $searcharray[$country]. urlencode($searchterm). "&page=1&keywords=". urlencode($searchterm). "&ie=UTF8&section=ATF,BTF";
				}
				
				if (!$variations) {
				    
				    for ($i = 2; $i <= 5; $i++) {
				    
				        $searchUrls[] = $searcharray[$country]
				            .urlencode($searchterm)."&page=$i&keywords="
				            .urlencode($searchterm)."&ie=UTF8&section=BTF";
					}
				}
						
                foreach ($searchUrls as $searchUrl) {
					
                    // run the search
                    $curlme = $this->curlmeupNoHeader($searchUrl);
                    $curlme = explode("&&&", $curlme);
                    $scraperesult = array();
                    
                    foreach ($curlme as $curl) {
                        $curlme = json_decode($curl, true);
                        if (is_array($curlme)) {
                            foreach ($curlme as $key => $value) {
                                $scraperesult[''.$key.''] = $value;
                            }
                        }
                    }
                    
                    if (isset($scraperesult['results-atf'])) {
                        $results .= $scraperesult['results-atf']['data']['value'];
                    }
                    
                    if (isset($scraperesult['results-btf'])) {
                        $results .= $scraperesult['results-btf']['data']['value'];
                    }
                    
                    if (isset($scraperesult['results-atf-next'])) {
                        $results .= $scraperesult['results-atf-next']['data']['value'];
                    }
                }
						
                if (isset($results)) {
							
                    $results = str_replace('\"','"',$results);
                    $results = $this->breakitup(
                        '<div id="result_', '<div id="result_', $results);
							
                    foreach ($results as $result) {
								
                        $resultValue = array();
                 
                        $temp = $this->breakitup(
                            'class="title" href="','</a>', $result);
                        $temp = explode(">", $temp[0], 2);
                        $resultValue['name'] = $temp[1];
                        
                        $temp = $this->breakitup('class="image"', '</a>',
                            $result);
                        $temp = $this->breakitup('src="','"',$temp[0]);
                        $resultValue['image'] = $temp[0];
                        
						$temp = $this->breakitup('class="price">', '</span>', $result);
						if (isset($temp[0])) {
						    $resultValue['price'] = str_replace('£','&pound;',$temp[0]);
						}
						else {
						    $resultValue['price'] = 0;
						}
                        $temp = $this->breakitup('name="','"',$result);
                        $resultValue['asin'] = $temp[0];
                    
                        $resultValues[''.$temp[0].''] = $resultValue;
                        $matches = $matches + 1;
								
                    }
					
					$result = '<table border="0" cellpadding="5" cellspacing="0" width="100%" class="searchresulttable">';
					$result .= '<tbody>';
					
					foreach($resultValues as $asin=>$value){
						

						if ($colcounter == 0) {
							$result .= '<tr id="itemLine-'.$asin.'">';
						}
					  
						$result .= '<td align="center" style="width:25%; padding:10px;height:130px; border-left: 1px solid #F0F0F0; border-bottom: 1px dashed #F0F0F0; text-align: center;">
						  <img src="'.$value['image'].'"/><br />
						  <b>'.$value['name'].'</b><br />
						  <center>'.$value['price'].'</center><br />
							<a target="_blank" style="font-size: 10px;" href="'.$dparray[$country].''.$asin.'">View Product on Amazon</a>
							<br />';
							
							if($variations == false){
	
							$result .= '<button class="awesomeButton buttonLarge primaryLargeButton " type="button" onclick="itemSelected(\''.$asin.'\');">
								<span class="button_label">This matches <br />my product</span>
							</button>';
							
							}else{
								
							// at this point, we need to load up and break up the detail page.
							$child = $this->curlmeupNoHeader($dparray[$country].$asin);


							// Get the ASIN of the configurable parent product
							$cfgParentAsin = $this->breakitup("<link rel=\"canonical\" href=\"", "\" />", $child);

							if (sizeof($cfgParentAsin) > 0) {

								$cfgParentAsin = explode("/", $cfgParentAsin[0]);

								if (sizeof($cfgParentAsin) > 0) {
									$cfgParentAsin = $cfgParentAsin[sizeof($cfgParentAsin)-1];
								}
								else {
									$cfgParentAsin = $asin;
								}
							}
							else {
								$cfgParentAsin = $asin;
							}

							$children = $this->breakitup('useTwister" value="','"',$child);
							
								if(sizeof($children) < 1){
									$result .= '<button class="awesomeButton buttonLarge primaryLargeButton " type="button" onclick="itemSelected(\''.$asin.'\');"><span class="button_label">This matches <br />my product</span>
							</button>';
								}else{
								
									
									// how many dimensions does this vary by?
									
									$dimensions = $this->breakitup('name="variationDimensionKeys" value="','"',$child);
									$dimensions = explode(",",$dimensions[0]);
									if(!is_array($dimensions)){
										$dimensions[0] = $dimensions;			 
									}
									
									$tmp = "";

									foreach($dimensions as $dimension){
										$tmp .= ucwords(str_replace("_"," ",$dimension))." / ";
									}
									$tmp = substr($tmp,0,strlen($tmp)-3);
									$result .= $tmp;
									
									$result .= '<br />';
									$result .= '<select name="variation_'.$asin.'" id="variation_'.$asin.'">';
									
									$childrenData = $this->breakitup('variationDimensionValue.','>',$child);

									$result .= "<option value=\"$cfgParentAsin\">Configurable Parent</option>\n";

									foreach($childrenData as $childData){
										
		
											$childasin = explode('"',$childData,2);
											$childData = $childasin[1];
											$childasin = $childasin[0];
											$result .= '<option value="'.$childasin.'">';
											// continue trimming the data, the explode by commas [if applicable]
											
											$childData = explode('value="',$childData,2);
											$childData = explode('"',$childData[1],2);
											$childData = explode(",",$childData[0]);
											if(!is_array($childData)){
												$childData[0] = $childData;			 
											}
											
									
											$tmp = "";
											foreach($childData as $childDataItem){
												$tmp .= $childDataItem." / ";
											}
											$tmp = substr($tmp,0,strlen($tmp)-3);
											$result .= $tmp;
											
											$result .= '</option>';
											
									}
									
									$result .= '</select>';
									
									$result .= '<br /><br />
									<button class="awesomeButton buttonLarge primaryLargeButton " type="button" onclick="itemSelected(document.getElementById(\'variation_'.$asin.'\').options[document.getElementById(\'variation_'.$asin.'\').selectedIndex].value);">
									<span class="button_label">This matches <br />my product</span>
								</button>';
								
								}
							}
							
							
							$result .= '<br />&nbsp;
						</td>';

						$colcounter = $colcounter + 1;
							
						
						
						if($colcounter == 4){
							$result .= '</tr>';
							$colcounter = 0;
						}
							
	
					}
				
					if($colcounter > 0){
						while($colcounter < 4){
							$result .= '<td style="width:25%; border-bottom: 1px dashed #F0F0F0; text-align: center;">&nbsp;</td>';
							$colcounter = $colcounter + 1;
						}
						if($colcounter == 4){
							$result .= '</tr>';	
						}
					}
					
					$result .= '</tbody>';
					$result .= '</table>';

					if($matches == 1){
						$onlymatch = $asin;	
					}
			
				} else {
					$matches = 0;
				}
		    }

            // no matches eval trigger
            if ($matches == 0)
            {
                if ($data['autoskip'] == 1)
                {
                    $result = "<h4>No matches found - skipping to next product</h4>";
                    echo '<script type="text/javascript">
                                    parent.$(\'chosenasin\').value = \'0\';
                                    setTimeout("parent.$(\'saveMatchSelectionform\').submit();",500);
                                  </script>';
                    $displaystring = "No matches found, skipping.";
                }
                else if($data['autoskip'] == 2)
                {
                    $result = "<h4>No matches found - skipping to next product and moving product to Advanced Setup</h4>";
                    echo '<script type="text/javascript">
                                    parent.$(\'chosenasin\').value = \'SKIP_MOVE_TO_MANUAL\';
                                    setTimeout("parent.$(\'saveMatchSelectionform\').submit();",500);
                                  </script>';
                    $displaystring = "No matches found, skipping.";
                }else {
                    $result = "<h4>No matches found</h4>";
                    $displaystring = "No matches found. Awaiting your input.";
                }
                    
            }
            else if ($matches == 1)
            {
                if($data['autoapprove'] == 1 && $data['searchmode'] == 1 && isset($onlymatch)){
                    // onlymatch contains the asin, we pass this back to the save form.
                    echo '<script type="text/javascript">
                                    parent.$(\'chosenasin\').value = \''.$onlymatch.'\';
                                    setTimeout("parent.$(\'saveMatchSelectionform\').submit();",500);
                                  </script>';
                    $displaystring = "Found 1 match. Auto-approving.";
                }else{
                    $displaystring = "Found 1 match. Awaiting your input.";
                }
            } else {
                $displaystring = "Found $matches matches. Awaiting your decision.";
            }
		}
		
		if (!isset($result)) {
		    $result = "<h4>No matches found</h4>";
		}
		if (!isset($displaystring)) {
		    $displaystring = "No matches found";
		}

		$result = rawurlencode($result);
		$result = preg_replace('/f%C3%BCr/i', 'fur', $result);
		
		echo '<script type="text/javascript">
						parent.$("currentstatus").innerHTML = "'.$displaystring.'";
						parent.$(\'searchresultoutputpane\').innerHTML = unescape(\''.$result.'\');
						parent.$(\'automatchbutton\').style.marginTop = \'4px\';
						parent.$(\'automatchbutton\').style.clear = \'both\';
						parent.$(\'automatchbutton\').style.display = \'block\';
						parent.$(\'automatchbutton\').style.float = \'right\';
						parent.$(\'automatchbutton_two\').style.marginTop = \'4px\';
						parent.$(\'automatchbutton_two\').style.marginRight = \'0px\';
						parent.$(\'automatchbutton_two\').style.clear = \'none\';
						parent.$(\'automatchbutton_two\').style.display = \'block\';
						parent.$(\'automatchbutton_two\').style.float = \'right\';
					  </script>';
	}

	public function saveMatchAction(){

		$data = unserialize(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_settings'));
		$postdata = $this->getRequest()->getPost();
		
		$nextpagecount = $postdata['saveProgressCount'] + 1;

		$progresspcent = round($nextpagecount / $postdata['saveproductcount'] * 100,2);
		$progressdisplay = $progresspcent."%";

		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();

		if(strlen($postdata['chosenasin']) > 1){
			$spm = $postdata['saveProgressMatches'] + 1;
			
			$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
			$models = Mage::getModel('amazonimportsetup'.$country.'/amazonimportsetup'.$country)->getCollection()->addFieldToFilter('productid',$postdata['saveproductid']);
			if(sizeof($models) > 0){
				foreach($models as $saving){
					break;
				}
			}else{
				$saving = Mage::getModel('amazonimportsetup'.$country.'/amazonimportsetup'.$country);
			}
				
			if($postdata['chosenasin'] != "SKIP_MOVE_TO_MANUAL"){	
				$saving->setData('asincode',$postdata['chosenasin']);
				$saving->setData('setup_type','auto');
			}else{
				$saving->setData('asincode','');
				$saving->setData('setup_type','manual');
			}
			$saving->setData('productid',$postdata['saveproductid']);
			$saving->setData('initial_setup_complete',0);
			$saving->save();
			
			// ============== Register our interest in the ASIN ==============

			try {
				$_asin = $postdata['chosenasin'];
				
				if ($_asin != "" && strlen($_asin) > 0) {
					
					Mage::getModel('amazonimport/reprice')->registerAsin($_asin, "new");
				}
			
			}
			catch (Exception $e) {
				
				
			}
			// ============== ============== ==============

			$result = $db->query("select * from {$table_prefix}amazonimport_setup_".$country." WHERE initial_setup_complete = 1 AND productid=".$postdata['saveproductid']." AND productid not in (select productid from {$table_prefix}amazonimport_errorlog_".$country." where productid=".$postdata['saveproductid']." AND submission_type='Product')");
			while($row = $result->fetch(PDO::FETCH_ASSOC))
			{
				$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$postdata['saveproductid'].",'Product')");
				$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$postdata['saveproductid'].",'Image')");
				// is product configurable?
				if(Mage::getModel('catalog/product')->load($postdata['saveproductid'])->getTypeId() != "simple"){
					$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$postdata['saveproductid'].",'Relation')");
				}
				$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$postdata['saveproductid'].",'Stock')");
				$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$postdata['saveproductid'].",'Price')");
			}
			
		}else{
			$spm = $postdata['saveProgressMatches'];
		}
		
		$spc = $postdata['saveProgressCount'] + 1;
		
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
		
		$listThisTable = $table_prefix."amazonimport_listthis_".$country;
		$setupTable = $table_prefix."amazonimport_setup_".$country;

		# BUGFIX EEM-153-65403-c Allow me to choose a new match is wrong way around. Changed operator from == to !=
		if($data['ignoreexisting'] != 1){
				
			$sqlTemp = "SELECT * FROM $listThisTable as t1, $setupTable as t2 WHERE t1.is_active='1' and 
						t1.productid=t2.productid and t2.setup_type='auto' and t2.asincode='' ORDER BY t1.productid";
		}
		else
		{
			$sqlTemp = "SELECT * FROM $listThisTable as t1, $setupTable as t2 WHERE t1.is_active='1' and 
				t1.productid=t2.productid and t2.setup_type='auto' ORDER BY t1.productid";
		}
		
		$result = $db->query("$sqlTemp LIMIT $nextpagecount,1");
		
		$row = $result->fetch();
		$product = Mage::getModel('catalog/product')->load($row['productid']);

		if($postdata['saveproductcount'] == $spc){

			echo "<script type='text/javascript'>
								parent.$('chosenasin').value = 'D';
								parent.$('saveproductid').value = '".$row['productid']."';
								parent.$('saveProgressCount').value = '".$spc."';
								parent.$('saveProgressMatches').value = '".$spm."';
								parent.$('progresspercent').innerHTML = '&nbsp;100%';
								parent.$('currentprogress').morph('width:100% !important;background:green;');
								parent.$('currentstatus').innerHTML = 'Setup Complete.';
								parent.$('footerProgressCount').innerHTML = '".$spc."';
								parent.$('footerProgressMatches').innerHTML = '".$spm."';
								parent.$('automatchbutton').style.display = 'none';
								parent.$('automatcher').style.display = 'none';
								parent.amazonimportGridJsObject.doFilter();
								alert('The Wizard has completed successfully. Please now continue to Review Products to complete setup for your products.');
						</script>";


		}else{
				
				
			if($product->getId() == ""){
					
				$result = $db->query("DELETE FROM {$table_prefix}amazonimport_listthis_".$country." WHERE productid='".$row['productid']."'");
				
				// dead product - move to the next - tell the save action we had a dead product match.
				echo "<script type='text/javascript'>
								parent.$('chosenasin').value = 'D';
								parent.$('saveproductid').value = '".$row['productid']."';
								parent.$('saveProgressCount').value = '".$spc."';
								parent.$('saveProgressMatches').value = '".$spm."';
								parent.$('progresspercent').innerHTML = '&nbsp;".$progressdisplay."';
								parent.$('currentprogress').morph('width:".$progressdisplay." !important;background:green;');
								parent.$('currentstatus').innerHTML = '';
								parent.$('footerProgressCount').innerHTML = '".$spc."';
								parent.$('footerProgressMatches').innerHTML = '".$spm."';
								setTimeout(\"parent.$('saveMatchSelectionform').submit();\",500);
						</script>";
					
					
			}else{
				# BUGFIX EEM-153-65403-b - Apostrophe in product SKU caused system to freeze due to javascript malformation.
				echo "<script type='text/javascript'>
						parent.$('footerProgressCount').innerHTML = '".$spc."';
						parent.$('footerProgressMatches').innerHTML = '".$spm."';
						parent.$('currentstatus').innerHTML = 'Getting results ...';
						parent.$('currentProductName').innerHTML = '".$this->sanitizeName($product->getName())."<br /> <a href=\"".str_replace("'","",$product->getProductUrl())."\" target=\"_blank\">View Product on website</a> /  <a href=\"".str_replace("'","",$this->getUrl('adminhtml/catalog_product/edit',array('id'=>$product->getId())))."/\" target=\"_blank\">View Product in admin area</a>';
						parent.$('currentProductSKU').innerHTML = '".str_replace("'","",$product->getSku())."';
						parent.$('currentProductID').innerHTML = '".$product->getId()."';
						parent.$('fetchproductid').value = '".$product->getId()."';
						parent.$('saveproductid').value = '".$product->getId()."';
						parent.$('progresspercent').innerHTML = '&nbsp;".$progressdisplay."';
						parent.$('currentprogress').morph('width:".$progressdisplay." !important;background:green;');
						parent.$('saveProgressCount').value = '".$spc."';
						parent.$('saveProgressMatches').value = '".$spm."';
						parent.$('chosenasin').value = '0';";
				if($product->getThumbnail() != "" && $product->getThumbnail() != "no_selection")
				{
					echo "parent.$('currentProductImage').src = '".Mage::getBaseUrl('media')."catalog/product".$product->getThumbnail()."';";
				}
				else
				{
					echo "parent.$('currentProductImage').src = '".Mage::getBaseUrl('skin')."frontend/default/default/images/no_image.jpg';";
				}

				echo "parent.$('currentstatus').innerHTML = 'Getting results...';
										setTimeout(\"parent.$('fetchform').submit();\",500);";
				echo "</script>";
			}
		}
	}

	public function getMappedDataFromProduct($attribute,$product)
	{
		$mkt = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');

		$store = Mage::getStoreConfig('amazonint/amazon'.$mkt.'/store');
		$mval = "";

		if ($product->getData($attribute) != "") {

			try {
				$mval = $product->getAttributeText("".$attribute."");
				if(is_array($mval)){
					$mval = implode("",$mval);
				}
				if($mval == ""){
					$mval = $product->getData($attribute);
				}
			}
			catch (Exception $x) {}
		}

		return $mval;
		 
	}

	public function reloadmappingsAction(){

		$data = $this->getRequest()->getPost();
		$currentvt = $data['currentvt'];
		$productid = $data['productid'];


		echo "<script type='text/javascript'>parent.document.getElementById('mappingarea').innerHTML = unescape('";
		ob_start();
		?>
<table cellspacing="0" class="form-list">
	<tbody>

	<?php

	$col = Mage::getModel('amazonimportcategorise'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace').'/amazonimportcategorise'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace'))->getCollection()->addFieldToFilter('productid',array('eq'=>$productid));
	foreach($col as $model){
		break;
	}
	$row = $model->getData();

	if(isset($row2['variation_theme'])){
		$currentvt = $row2['variation_theme'];
	}else{
		$currentvt = "";
	}

	$xml = Mage::getModel('amazonimport/amazonimport')->getTemplateXml();

	$node = $xml->xpath('//ProductData/'.$row['producttype'].'/ProductType/'.$row['productdatatype'].'/VariationData');
	if(sizeof($node) == 0){
		$node = $xml->xpath('//ProductData/'.$row['producttype'].'/VariationData');
		$startofcombined = $row['producttype'].'VariationData';
	}else{
		$startofcombined = $row['producttype'].'ProductType'.$row['productdatatype'].'VariationData';
	}


	Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace'),'camiloo_amazon_mapping_marketplace');


	foreach($node[0] as $vdata){
			
		if(("".$vdata->getName()."" != "VariationTheme")&&("".$vdata->getName()."" != "Parentage")){
			$ismapped = false;
			$combined = $startofcombined.$vdata->getName();
			$val = Mage::getModel('amazonimportmapping'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace').'/amazonimportmapping'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace'))->getCollection()->addFieldToFilter('xmlkey',array('eq'=>$combined));
			foreach($val as $model){
				$ismapped = true;
				break;
			}

			?>

		<tr>
			<td class="label"><label for="name"><?php echo ucwords($this->from_camel_case("".$vdata->getName().""));	?></label></td>
			<td class="value"><?php 
			if($ismapped == true){
				echo "<div id='$combined-values'>Mapped to attribute with code ".$model->getMappingvalue().'</div>';
				$valuetosend = $model->getMappingvalue();
			}else{
				echo "<div id='$combined-values'>Not mapped</div>";
				$valuetosend = "";
			}

			if(isset($vdata['CamElementRepeatLimit'])){
				echo "<br /><small>Can contain up to ".$vdata['CamElementRepeatLimit']." values separated by | marks.</small>";
			}
			unset($ismapped);
			?></td>
			<td class="scope-label"></td>
			<td class="value"><?php echo "<div id='$combined-buttons'>"; ?>
			<button class="scalable" type="button"
				onclick="displaySelectForm('<?php echo $combined; ?>','<?php echo $valuetosend; ?>');"
				name="submit" value="Select Attribute"><span>Select Attribute</span></button>
			<button class="scalable" type="button"
				onclick="displayEnterCodeForm('<?php echo $combined; ?>','<?php echo $valuetosend; ?>');"
				name="submit" value="Enter Attribute Code"><span>Enter Attribute
			Code</span></button>
			</div>
			</td>
		</tr>

		<?php
		}
	}?>
	</tbody>
</table>

	<?php
	$pagecontent=ob_get_contents();
	ob_end_clean();
	echo rawurlencode($pagecontent);
	echo "');</script>";


	}

	public function from_camel_case($str) {
		$str[1] = strtolower($str[1]);
		$func = create_function('$c', 'return " " . strtolower($c[1]);');
		return preg_replace_callback('/([A-Z])/', $func, $str);
	}




	public function cloneAction() {

		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace'),'camiloo_amazon_automatch_marketplace');
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('amazonimport/automatch_clone_edit'));
		$this->_addLeft($this->getLayout()->createBlock('amazonimport/automatch_clone_edit_tabs'));
		$this->renderLayout();
	}

	public function bulkAction() {

		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace'),'camiloo_amazon_automatch_marketplace');
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('amazonimport/automatch_bulk_edit'));
		$this->_addLeft($this->getLayout()->createBlock('amazonimport/automatch_bulk_edit_tabs'));
		$this->renderLayout();
	}
		
	public function comAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("com",'camiloo_amazon_automatch_marketplace');
		$this->indexAction();
	}

	public function ukAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("uk",'camiloo_amazon_automatch_marketplace');
		$this->indexAction();
	}

	public function frAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("fr",'camiloo_amazon_automatch_marketplace');
		$this->indexAction();
	}

	public function deAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("de",'camiloo_amazon_automatch_marketplace');
		$this->indexAction();
	}

	public function clonefromAction() {

		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace'),'camiloo_amazon_automatch_marketplace');
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('amazonimport/automatch_clone_edit'));
		$this->_addLeft($this->getLayout()->createBlock('amazonimport/automatch_clone_edit_tabs'));
		$this->renderLayout();
	}

	public function getattributesAction(){
		$data = $this->getRequest()->getPost();
		$element = $data['getattributes-xmlkey'];
		$curval = $data['getattributes-keyvalue'];

		$attribout = "<select name='".$element."-newvalue' id='".$element."-newvalue' />";

		$attribout .= "<option value=''>--- Not mapped ---</option>";
		$collection = Mage::getResourceModel('eav/entity_attribute_collection')
		->setEntityTypeFilter( Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId() );

		$attribs = array();
		$count = 0;
		foreach($collection as $item){
			if(($item->getFrontendInput() != "media_image")&&($item->getFrontendInput() != "gallery")){
				$attribout .= "<option value='".$item->getAttributeCode()."' ";
					
				if($item->getAttributeCode() == $curval){
					$attribout .= 'selected="selected"';
				}
					
					
				$attribout .= ">".$item->getFrontendLabel()." (".$item->getAttributeCode()." ".$item->getFrontendInput().")"."</option>";
			}
		}
			
		$attribout .= '</select>';

		echo "<script type='text/javascript'>parent.document.getElementById('".$element."-values').innerHTML = unescape('".rawurlencode($attribout)."');</script>";
		echo "<script type='text/javascript'>parent.document.getElementById('".$element."-buttons').innerHTML = unescape('";
		ob_start();
		?>
<button class="scalable" type="button"
	onclick="saveEnterCode('<?php echo $element; ?>','<?php echo $curval; ?>');"
	name="submit" value="Save"><span>Save</span></button>
<button class="scalable back" type="button"
	onclick="cancelUpdate('<?php echo $element; ?>','<?php echo $curval; ?>');"
	name="submit" value="Cancel"><span>Cancel</span></button>
		<?php
		$pagecontent=ob_get_contents();
		ob_end_clean();
		echo rawurlencode($pagecontent);
		echo "');</script>";

	}

	public function texteditAction(){
		$data = $this->getRequest()->getPost();
		$element = $data['textedit-xmlkey'];
		$curval = $data['textedit-keyvalue'];

		echo "<script type='text/javascript'>parent.document.getElementById('".$element."-values').innerHTML = unescape('";
		ob_start();
		?>
<input
	type="text" name="<?php echo $element; ?>-newvalue"
	id="<?php echo $element; ?>-newvalue" value="<?php echo $curval; ?>" />
		<?php
		$pagecontent=ob_get_contents();
		ob_end_clean();
		echo rawurlencode($pagecontent);
		echo "');</script>";
		echo "<script type='text/javascript'>parent.document.getElementById('".$element."-buttons').innerHTML = unescape('";
		ob_start();
		?>
<button class="scalable" type="button"
	onclick="saveEnterCode('<?php echo $element; ?>','<?php echo $curval; ?>');"
	name="submit" value="Save"><span>Save</span></button>
<button class="scalable back" type="button"
	onclick="cancelUpdate('<?php echo $element; ?>','<?php echo $curval; ?>');"
	name="submit" value="Cancel"><span>Cancel</span></button>
		<?php
		$pagecontent= ob_get_contents();
		ob_end_clean();
		echo rawurlencode($pagecontent);
		echo "');</script>";
	}

	public function canceleditAction(){
		$data = $this->getRequest()->getPost();
		$element = $data['canceledit-xmlkey'];
		$curval = $data['canceledit-keyvalue'];

		echo "<script type='text/javascript'>parent.document.getElementById('".$element."-values').innerHTML = unescape('";
		ob_start();

		if($curval == ""){
			?>
Not mapped
			<?php
		}else{
			?>
Mapped to attribute with code
			<?php echo $curval; ?>
			<?php
		}
		 
		$pagecontent=ob_get_contents();
		ob_end_clean();
		echo rawurlencode($pagecontent);
		echo "');</script>";
		echo "<script type='text/javascript'>parent.document.getElementById('".$element."-buttons').innerHTML = unescape('";
		ob_start();
		?>
<button class="scalable" type="button"
	onclick="displaySelectForm('<?php echo $element; ?>','<?php echo $curval; ?>');"
	name="submit" value="Select Attribute"><span>Select Attribute</span></button>
&nbsp;
<button class="scalable" type="button"
	onclick="displayEnterCodeForm('<?php echo $element; ?>','<?php echo $curval; ?>');"
	name="submit" value="Enter Attribute Code"><span>Enter Attribute Code</span></button>
		<?php
		$pagecontent= ob_get_contents();
		ob_end_clean();
		echo rawurlencode($pagecontent);
		echo "');</script>";
	}

	public function saveAction(){
		$data = $this->getRequest()->getPost();

		$redirectBack   = $this->getRequest()->getParam('back', false);

		// first, we must check if there is a row for this product id - if so, delete it.
		$model = Mage::getModel('amazonimportsetup'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace').'/amazonimportsetup'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace'))->getCollection()->addFieldToFilter('productid',array($data['configurable_product_id']));
		if(sizeof($model) > 0){
			foreach($model as $mdl){
				$mdl->delete();
			}
		}

		$model = Mage::getModel('amazonimport/amazonimportsetup'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace'));
		$model->setData($data);
		$model->save();

		if ($redirectBack) {
			$this->_redirect('*/*/edit', array(
                'id'    => $data['configurable_product_id'],
                '_current'=>true
			));
		} else {
			$this->_redirect('*/*/');
		}

		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__('Variation theme has been set'));
		$this->_redirect('*/*/');


	}
		function curlmeupNoHeader($urltograb){
			// this function gets the requested data
			$session = curl_init("$urltograb");
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($session, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($session, CURLOPT_TIMEOUT, 30);
			curl_setopt($session, CURLOPT_COOKIEJAR, "/tmp/cookie.txt");
			curl_setopt($session, CURLOPT_COOKIEFILE, "/tmp/cookie.txt");
			curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
			$result = curl_exec($session);
			return $result;
		}
	
	function curlmeup($urltograb){
		// this function gets the requested data
		$session = curl_init("$urltograb");
		curl_setopt($session, CURLOPT_HEADER, true);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($session, CURLOPT_TIMEOUT, 30);
		curl_setopt($session, CURLOPT_COOKIEJAR, "/tmp/cookie.txt");
		curl_setopt($session, CURLOPT_COOKIEFILE, "/tmp/cookie.txt");
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($session);
		
	//	echo "Curl error ".(curl_error($session))."\n";

		/* FollowLocation replacement */
		if(strpos($result,"TTP/1.1") == 0){
			// no matches at all? thats a little odd.
			// issue some javascript to reload the page.
		/*	echo '<script type="text/javascript">
							'."setTimeout(\"parent.$('fetchform').submit();\",3000);".'
						  </script>';
			die();
				*/
			return $result;
		}else{
			list($header, $data) = explode("\n\r", $result, 2);
			$http_code = curl_getinfo($session, CURLINFO_HTTP_CODE);
			if ($http_code == 301 || $http_code == 302){
				$matches = array();
				preg_match('/Location:(.*?)\n/', $header, $matches);
				$url = @parse_url(trim(array_pop($matches)));
				if (!$url)
				{
					//couldn't process the url to redirect to
					$curl_loops = 0;
					return $result;
				}
				$last_url = parse_url(curl_getinfo($session, CURLINFO_EFFECTIVE_URL));
				if (!$url['scheme'])
				$url['scheme'] = $last_url['scheme'];
				if (!$url['host'])
				$url['host'] = $last_url['host'];
				if (!$url['path'])
				$url['path'] = $last_url['path'];
				$new_url = $url['scheme'].'://'.$url['host'].$url['path'].(array_key_exists('query', $url) && $url['query'] ? '?'.$url['query'] : '');
				return $this->curlmeup($new_url);
			}else{
				curl_close($session);
				return $result;
			}
		}
		/* FollowLocation replacement end */
			
			


	}
	
	function curlposterVariations($urltograb, $poster){
		$session = curl_init("$urltograb");
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_POST, 1);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_POSTFIELDS, $poster);
		curl_setopt($session, CURLOPT_CONNECTTIMEOUT, 8);
		curl_setopt($session, CURLOPT_COOKIEJAR, "/tmp/cookie.txt");
		curl_setopt($session, CURLOPT_COOKIEFILE, "/tmp/cookie.txt");
		curl_setopt($session, CURLOPT_TIMEOUT, 16);
		curl_setopt($session, CURLOPT_VERBOSE, TRUE);
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($session);
		curl_close($session);
		return $result;
	}
	

	function curlposter($urltograb, $poster){
		$session = curl_init("$urltograb");
		curl_setopt($session, CURLOPT_HEADER, true);
		curl_setopt($session, CURLOPT_POST,1);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_POSTFIELDS,$poster);
		curl_setopt($session, CURLOPT_CONNECTTIMEOUT, 8);
		curl_setopt($session, CURLOPT_COOKIEJAR, "/tmp/cookie.txt");
		curl_setopt($session, CURLOPT_COOKIEFILE, "/tmp/cookie.txt");
		curl_setopt($session, CURLOPT_TIMEOUT, 16);
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($session);


		/* FollowLocation replacement */


		if(strpos($result,"TTP/1.1") == 0){
			// no matches at all? thats a little odd.
			// issue some javascript to reload the page.
		/*	echo '<script type="text/javascript">
							'."setTimeout(\"parent.$('fetchform').submit();\",3000);".'
						  </script>';
			die();*/
			return $result;
				
		}else{

			list($header, $data) = explode("\n\r", $result, 2);
			$http_code = curl_getinfo($session, CURLINFO_HTTP_CODE);
			if ($http_code == 301 || $http_code == 302){
				$matches = array();
				preg_match('/Location:(.*?)\n/', $header, $matches);
				$url = @parse_url(trim(array_pop($matches)));
				if (!$url)
				{
					//couldn't process the url to redirect to
					$curl_loops = 0;
					return $result;
				}
				$last_url = parse_url(curl_getinfo($session, CURLINFO_EFFECTIVE_URL));
				if (!$url['scheme'])
				$url['scheme'] = $last_url['scheme'];
				if (!$url['host'])
				$url['host'] = $last_url['host'];
				if (!$url['path'])
				$url['path'] = $last_url['path'];
				$new_url = $url['scheme'].'://'.$url['host'].$url['path'].(array_key_exists('query', $url) && $url['query'] ? '?'.$url['query'] : '');
				return $this->curlmeup($new_url);
			}else{
				return $result;
			}
		}
		/* FollowLocation replacement end */
	}

	function nobody($urltograb){
		// this function gets the requested data
		$session = curl_init("$urltograb");
		curl_setopt($session, CURLOPT_HEADER, true);
		curl_setopt($session, CURLOPT_NOBODY, true);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($session, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($session, CURLOPT_TIMEOUT, 1);
		curl_setopt($session, CURLOPT_COOKIEJAR, "/tmp/cookie.txt");
		curl_setopt($session, CURLOPT_COOKIEFILE, "/tmp/cookie.txt");
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($session);
		return $result;
	}

}

?>