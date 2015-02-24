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

class Camiloo_Amazonimport_CategoriseController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize requested category and put it into registry.
     * Root category can be returned, if inappropriate store/category is specified
     *
     * @param bool $getRootInstead
     * @return Mage_Catalog_Model_Category
     */
	public $_isOveriddenFromHereOnIn = false; 
	 
    protected function _initCategory($getRootInstead = false)
    {
        $categoryId = (int) $this->getRequest()->getParam('id',false);
        $storeId    = (int) $this->getRequest()->getParam('store');
        $category = Mage::getModel('catalog/category');
        $category->setStoreId($storeId);

        if ($categoryId) {
            $category->load($categoryId);
            if ($storeId) {
                $rootId = Mage::app()->getStore($storeId)->getRootCategoryId();
                if (!in_array($rootId, $category->getPathIds())) {
                    // load root category instead wrong one
                    if ($getRootInstead) {
                        $category->load($rootId);
                    }
                    else {
                        $this->_redirect('*/*/', array('_current'=>true, 'id'=>null));
                        return false;
                    }
                }
            }
        }

        if ($activeTabId = (string) $this->getRequest()->getParam('active_tab_id')) {
            Mage::getSingleton('admin/session')->setActiveTabId($activeTabId);
        }

        Mage::register('category', $category);
        Mage::register('current_category', $category);
        return $category;
    }
    /**
     * Catalog categories index action
     */
    public function indexAction()
    {
                /* 
                   Bugfix for issue KDO-449-54740 - MN
                   Sometimes Magento skips over table creation during upgrade. 
                   Added new method 'checkTables' to Amazonimport - calling in place will eradicate issue.
                */
                Mage::getModel('amazonimport/amazonimport')->checkTables();
		 $this->_forward('edit');
    }
	
	public function displayproducttypeAction(){
		
	}
	
	public function saveproducttypeAction(){
		
	}
	
	public function updatebrowsenodesAction(){
	
	 require_once(Mage::getBaseDir()."/lib/PHPExcel.php");
	 $country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_categorise_marketplace');
	 ini_set("memory_limit","512M");
	 $amz = Mage::getModel('amazonimport/amazonimport');
	 $urls = array();
	 
	 	$amz->updateCache('bn',$country);
	 	$amz->updateCache('fd',$country);
	 
	 //$db = Mage::getSingleton("core/resource")->getConnection("core_write");
	 //$table_prefix = Mage::getConfig()->getTablePrefix();
	 //$_sql = "DELETE FROM {$table_prefix}amazonimport_browsenodes WHERE country_id='$country';";
	 //$db->query($_sql);
									
	 
	
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__('Successfully updated Amazon Category database'));
		$this->_redirect('*/*/edit');
            
		 
	}

	public function setcategorymapAction(){
		
		$postdata = $this->getRequest()->getPost();
		$categoryid = $postdata['category_id'];
		$country = $postdata['country'];
		$browsenode = $postdata['browsenode']; // this is bnid now
		$itemtype = $postdata['itemtype'];
		$vtheme = $postdata['vtheme'];
		$action = $postdata['action'];
	
		$category = Mage::getModel('catalog/category')->load($categoryid);
		$level = $category->getLevel();
		
		// we need to cascade this part downwards.
		$this->traverseTree($categoryid, $level, true);
		
		echo "<script type='text/javascript'>
		parent.document.getElementById('outputcatvalue').innerHTML = unescape('".rawurlencode(utf8_decode($this->getMappings($categoryid, $country, false)))."');
		parent.document.getElementById('outputitvalue').innerHTML = unescape('".rawurlencode('<center><b>Current Setting:</b>'.$this->getCategoryItemType($categoryid, $country, false).'</center>')."');
		parent.document.getElementById('outputvtvalue').innerHTML = unescape('".rawurlencode('<center><b>Current Setting:</b>'.$this->getCategoryVariationTheme($categoryid, $country, false).'</center>')."');
		parent.document.getElementById('outputcondvalue').innerHTML = unescape('".rawurlencode('<center><b>Current Setting:</b>'.$this->getCategoryCondition($categoryid, $country, false).'</center>')."');
		parent.document.getElementById('outputcnotevalue').innerHTML = unescape('".rawurlencode('<center><b>Current Setting:</b>'.$this->getCategoryConditionNote($categoryid, $country, false).'</center>')."');		
		</script>";	
		
	}
	
	public function traverseTree($categoryid, $level, $isMainUpdatePoint){
		
		$category = Mage::getModel('catalog/category')->load($categoryid);
		$level = $category->getLevel();
				
		// update settings on this category...
		$return = $this->doTheUpdates($categoryid, $level, $isMainUpdatePoint);
		
		// then check for children and loop through them too!
		foreach($category->getChildrenCategories() as $childcat){
			if($return == "continue"){
				// only descend until we reach an override if we're cascading a new setting
				$this->traverseTree($childcat->getId(), $childcat->getLevel(), false);
			}
		}
		
	}
	
	public function doTheUpdates($categoryid, $level, $isMainUpdatePoint){
		
				$category = Mage::getModel('catalog/category')->load($categoryid);
				$level = $category->getLevel();
				$path = $category->getPath();
				
				$postdata = $this->getRequest()->getPost();
				$country = $postdata['country'];
				$browsenode = $postdata['browsenode'];
				$itemtype = $postdata['itemtype'];
				$vtheme = $postdata['vtheme'];
				$action = $postdata['action'];
				$condition = $postdata['condition'];
				$conditionnote = $postdata['conditionnote'];
				$cmap = Mage::getModel('amazonimport/amazonimportcategorymapping')->getCollection()
					->addFieldToFilter('category_id',$categoryid)
					->addFieldToFilter('country_id',$country);			
				$db = Mage::getSingleton("core/resource")->getConnection("core_write");
				$table_prefix = Mage::getConfig()->getTablePrefix();
					
				if($cmap->getSize() > 0){
					$exists = 1;	
					// okay, is this inherited?
					foreach($cmap as $catmap){
						break;	
					}
					if($catmap->getData('inherited') == 1){
						$inherited = true;	
					}else{
						$inherited = false;	
					}
				}else{
					$exists = 0;
					$inherited = 0;
				}
				
			$db = Mage::getSingleton("core/resource")->getConnection("core_write");
			$table_prefix = Mage::getConfig()->getTablePrefix();
			
			// so we're now going to be updating not only the node set, but also any children UNTIL we hit an override
			// at which point we stop and check any other child branches.
				
			switch($action){
				
				case "S1":
				
					// if the node is in the database, we know that we are not at an inheritance any more
					// if this is where the command was issued, as a user can only update the inheritance point
					// or create a new one.
					if($exists && $isMainUpdatePoint){
						// therefore, if exists and $isMainUpdatePoint is true, set inherited to 0 and update
						$db->query("UPDATE {$table_prefix}amazonimport_categorymapping
								   	SET browsenode1='$browsenode', inherited=0, level=$level
									WHERE category_id='$categoryid' AND country_id='$country'");
					}else if($exists && $isMainUpdatePoint == false){
						// else if exists but isMainUpdatePoint isn't true, we need to check the value of $inherited.
						if($inherited){
							// if this is true, the row has no override and we should update the values to complete cascade.
							$db->query("UPDATE {$table_prefix}amazonimport_categorymapping
										SET browsenode1='$browsenode', inherited=1, level=$level
										WHERE category_id='$categoryid' AND country_id='$country'");
						}else{
							// if this is false, the row has an override and we shouldn't cascade this setting onto here.
							return "stop";	// stop further descendancy.
						}
					}else if($exists == false && $isMainUpdatePoint){
					// else if not exists, but is the main update point, this is the row that has just been created.
							$db->query("INSERT INTO {$table_prefix}amazonimport_categorymapping
								   	    (`category_id`,`country_id`,`browsenode1`,`inherited`,`level`) VALUES
										('$categoryid','$country','$browsenode','0','$level')");
					}else if($exists == false && $isMainUpdatePoint == false){
							// else if not exists and not main update point, we need to cascade down
							$db->query("INSERT INTO {$table_prefix}amazonimport_categorymapping
								   	    (`category_id`,`country_id`,`browsenode1`,`inherited`,`level`) VALUES
										('$categoryid','$country','$browsenode','1','$level')");
					}
					
					/**** TODO ****/
					// Handle what to do when nodes move... challenges left right + centre with this...
				
				break;
				
				case "S2":
					
					// if the node is in the database, we know that we are not at an inheritance any more
					// if this is where the command was issued, as a user can only update the inheritance point
					// or create a new one.
					if($exists && $isMainUpdatePoint){
						// therefore, if exists and $isMainUpdatePoint is true, set inherited to 0 and update
						$db->query("UPDATE {$table_prefix}amazonimport_categorymapping
								   	SET browsenode2='$browsenode', inherited=0, level=$level
									WHERE category_id='$categoryid' AND country_id='$country'");
					}else if($exists && $isMainUpdatePoint == false){
						// else if exists but isMainUpdatePoint isn't true, we need to check the value of $inherited.
						if($inherited){
							// if this is true, the row has no override and we should update the values to complete cascade.
							$db->query("UPDATE {$table_prefix}amazonimport_categorymapping
										SET browsenode2='$browsenode', inherited=1, level=$level
										WHERE category_id='$categoryid' AND country_id='$country'");
						}else{
							// if this is false, the row has an override and we shouldn't cascade this setting onto here.
							return "stop";	// stop further descendancy.
						}
					}else if($exists == false && $isMainUpdatePoint){
					// else if not exists, but is the main update point, this is the row that has just been created.
							$db->query("INSERT INTO {$table_prefix}amazonimport_categorymapping
								   	    (`category_id`,`country_id`,`browsenode2`,`inherited`,`level`) VALUES
										('$categoryid','$country','$browsenode','0','$level')");
					}else if($exists == false && $isMainUpdatePoint == false){
							// else if not exists and not main update point, we need to cascade down
							$db->query("INSERT INTO {$table_prefix}amazonimport_categorymapping
								   	    (`category_id`,`country_id`,`browsenode2`,`inherited`,`level`) VALUES
										('$categoryid','$country','$browsenode','1','$level')");
					}
					
				break;
				
				case "U1":
				
					// if the node is in the database, we know that we are not at an inheritance any more
					// if this is where the command was issued, as a user can only update the inheritance point
					// or create a new one.
					if($exists && $isMainUpdatePoint){
						// therefore, if exists and $isMainUpdatePoint is true, set inherited to 0 and update
						$db->query("UPDATE {$table_prefix}amazonimport_categorymapping
								   	SET browsenode1='', inherited=0, level=$level
									WHERE category_id='$categoryid' AND country_id='$country'");
					}else if($exists && $isMainUpdatePoint == false){
						// else if exists but isMainUpdatePoint isn't true, we need to check the value of $inherited.
						if($inherited){
							// if this is true, the row has no override and we should update the values to complete cascade.
							$db->query("UPDATE {$table_prefix}amazonimport_categorymapping
										SET browsenode1='', inherited=1, level=$level
										WHERE category_id='$categoryid' AND country_id='$country'");
						}else{
							// if this is false, the row has an override and we shouldn't cascade this setting onto here.
							return "stop";	// stop further descendancy.
						}
					}else if($exists == false && $isMainUpdatePoint){
					// else if not exists, but is the main update point, this is the row that has just been created.
							$db->query("INSERT INTO {$table_prefix}amazonimport_categorymapping
								   	    (`category_id`,`country_id`,`browsenode1`,`inherited`,`level`) VALUES
										('$categoryid','$country','','0','$level')");
					}else if($exists == false && $isMainUpdatePoint == false){
							// else if not exists and not main update point, we need to cascade down
							$db->query("INSERT INTO {$table_prefix}amazonimport_categorymapping
								   	    (`category_id`,`country_id`,`browsenode1`,`inherited`,`level`) VALUES
										('$categoryid','$country','','1','$level')");
					}
					
				
				break;
				
				case "U2":
									
					// if the node is in the database, we know that we are not at an inheritance any more
					// if this is where the command was issued, as a user can only update the inheritance point
					// or create a new one.
					if($exists && $isMainUpdatePoint){
						// therefore, if exists and $isMainUpdatePoint is true, set inherited to 0 and update
						$db->query("UPDATE {$table_prefix}amazonimport_categorymapping
								   	SET browsenode2='', inherited=0, level=$level
									WHERE category_id='$categoryid' AND country_id='$country'");
					}else if($exists && $isMainUpdatePoint == false){
						// else if exists but isMainUpdatePoint isn't true, we need to check the value of $inherited.
						if($inherited){
							// if this is true, the row has no override and we should update the values to complete cascade.
							$db->query("UPDATE {$table_prefix}amazonimport_categorymapping
										SET browsenode2='', inherited=1, level=$level
										WHERE category_id='$categoryid' AND country_id='$country'");
						}else{
							// if this is false, the row has an override and we shouldn't cascade this setting onto here.
							$this->_isOveriddenFromHereOnIn = true;	// stop further descendancy.
						}
					}else if($exists == false && $isMainUpdatePoint){
					// else if not exists, but is the main update point, this is the row that has just been created.
							$db->query("INSERT INTO {$table_prefix}amazonimport_categorymapping
								   	    (`category_id`,`country_id`,`browsenode2`,`inherited`,`level`) VALUES
										('$categoryid','$country','','0','$level')");
					}else if($exists == false && $isMainUpdatePoint == false){
							// else if not exists and not main update point, we need to cascade down
							$db->query("INSERT INTO {$table_prefix}amazonimport_categorymapping
								   	    (`category_id`,`country_id`,`browsenode2`,`inherited`,`level`) VALUES
										('$categoryid','$country','','1','$level')");
					}
									
				break;
				
				
				case "UpdateIT":
												
					// if the node is in the database, we know that we are not at an inheritance any more
					// if this is where the command was issued, as a user can only update the inheritance point
					// or create a new one.
					if($exists && $isMainUpdatePoint){
						// therefore, if exists and $isMainUpdatePoint is true, set inherited to 0 and update
						$db->query("UPDATE {$table_prefix}amazonimport_categorymapping
								   	SET itemtype='$itemtype', inherited=0, level=$level
									WHERE category_id='$categoryid' AND country_id='$country'");
					}else if($exists && $isMainUpdatePoint == false){
						// else if exists but isMainUpdatePoint isn't true, we need to check the value of $inherited.
						if($inherited){
							// if this is true, the row has no override and we should update the values to complete cascade.
							$db->query("UPDATE {$table_prefix}amazonimport_categorymapping
										SET itemtype='$itemtype', inherited=1, level=$level
										WHERE category_id='$categoryid' AND country_id='$country'");
						}else{
							// if this is false, the row has an override and we shouldn't cascade this setting onto here.
							return "stop";	// stop further descendancy.
						}
					}
						
						
				break;
				
				case "UpdateVT":
												
					// if the node is in the database, we know that we are not at an inheritance any more
					// if this is where the command was issued, as a user can only update the inheritance point
					// or create a new one.
					if($exists && $isMainUpdatePoint){
						// therefore, if exists and $isMainUpdatePoint is true, set inherited to 0 and update
						$db->query("UPDATE {$table_prefix}amazonimport_categorymapping
								   	SET variation_theme='$vtheme', inherited=0, level=$level
									WHERE category_id='$categoryid' AND country_id='$country'");
					}else if($exists && $isMainUpdatePoint == false){
						// else if exists but isMainUpdatePoint isn't true, we need to check the value of $inherited.
						if($inherited){
							// if this is true, the row has no override and we should update the values to complete cascade.
							$db->query("UPDATE {$table_prefix}amazonimport_categorymapping
										SET variation_theme='$vtheme', inherited=1, level=$level
										WHERE category_id='$categoryid' AND country_id='$country'");
						}else{
							// if this is false, the row has an override and we shouldn't cascade this setting onto here.
							return "stop";	// stop further descendancy.
						}
					}
						
						
				break;
				
				
				case "UpdateCondition":
												
					// if the node is in the database, we know that we are not at an inheritance any more
					// if this is where the command was issued, as a user can only update the inheritance point
					// or create a new one.
					if($exists && $isMainUpdatePoint){
						// therefore, if exists and $isMainUpdatePoint is true, set inherited to 0 and update
						$db->query("UPDATE {$table_prefix}amazonimport_categorymapping
								   	SET `condition`='$condition', inherited=0, level=$level
									WHERE category_id='$categoryid' AND country_id='$country'");
					}else if($exists && $isMainUpdatePoint == false){
						// else if exists but isMainUpdatePoint isn't true, we need to check the value of $inherited.
						if($inherited){
							// if this is true, the row has no override and we should update the values to complete cascade.
							$db->query("UPDATE {$table_prefix}amazonimport_categorymapping
										SET `condition`='$condition', inherited=1, level=$level
										WHERE category_id='$categoryid' AND country_id='$country'");
						}else{
							// if this is false, the row has an override and we shouldn't cascade this setting onto here.
							return "stop";	// stop further descendancy.
						}
					}
						
						
				break;
				
				
				
				case "UpdateCNote":
												
					// if the node is in the database, we know that we are not at an inheritance any more
					// if this is where the command was issued, as a user can only update the inheritance point
					// or create a new one.
					if($exists && $isMainUpdatePoint){
						// therefore, if exists and $isMainUpdatePoint is true, set inherited to 0 and update
						$db->query("UPDATE {$table_prefix}amazonimport_categorymapping
								   	SET condition_note='$conditionnote', inherited=0, level=$level
									WHERE category_id='$categoryid' AND country_id='$country'");
					}else if($exists && $isMainUpdatePoint == false){
						// else if exists but isMainUpdatePoint isn't true, we need to check the value of $inherited.
						if($inherited){
							// if this is true, the row has no override and we should update the values to complete cascade.
							$db->query("UPDATE {$table_prefix}amazonimport_categorymapping
										SET condition_note='$conditionnote', inherited=1, level=$level
										WHERE category_id='$categoryid' AND country_id='$country'");
						}else{
							// if this is false, the row has an override and we shouldn't cascade this setting onto here.
							return "stop";	// stop further descendancy.
						}
					}
						
						
				break;
				
				
			}	
			
		return "continue";
	}
	
  public function getCategoryVariationTheme($categoryid, $country, $inherited=false){
	  list($cm, $inherited) = $this->getVTITMappings($categoryid, $country, "VT");  
	  $output = "";
	  $amazoncore = Mage::getModel('amazonimport/amazonimport');
						
	  if(is_object($cm)){
		  
				if($cm->getData('inherited')){
					if($cm->getData('itemtype') != ""){
					  	if($cm->getData('variation_theme') != ""){
							$output = "<b>(Inherited from parent category)</b> ".$amazoncore->FormatCamelCase($cm->getData('variation_theme'));
						}else{
							$output = "<b>(Inherited from parent category)</b> None selected yet. <br /><br /> Please visit the parent category to set the variation theme, or alternatively please select a primary category under 'Category On Amazon' on this screen to override the inheritance.";
						}
					}else{
						$output = "<b>(Inherited from parent category)</b> None selected yet. <br /><br />Please visit the parent category to select an item type before continuing, or alternatively please select a primary category under 'Category On Amazon' on this screen to override the inheritance.";
					}
				}else{
					if($cm->getData('itemtype') != ""){
						
						if($cm->getData('variation_theme') != ""){
							$output = " ".$amazoncore->FormatCamelCase($cm->getData('variation_theme'))."<br /><br />";
						}else{
							$output = ' None selected yet.'."<br /><br />";
						}
						$amazoncore = Mage::getModel('amazonimport/amazonimport');
						$possibles = $amazoncore->getPossibleVariationThemesSelect($cm->getData('itemtype'));
						
						
						if($possibles == false){
							$output .= "There are no Variation Themes available for this type of Product.";
						}else{
							$output .= "<select name='variationtheme' onchange='updateVariationTheme(this.options[this.selectedIndex].value);'>";
							$output .= $possibles;
							$output .= "</select>";
						}
						
					}else{
						$output .= "Please select an item type before continuing.";
					}
				}
	  }else{
		$output = "Please select a primary Amazon category before continuing.";		
	  }
  	
	  return $output;
  
  }
  
	public function getCategoryItemType($categoryid, $country, $inherited=false){
	
	  list($cm, $inherited) = $this->getVTITMappings($categoryid, $country, "IT");  
	  $output = "";
	  $amazoncore = Mage::getModel('amazonimport/amazonimport');
	  if (is_object($cm)) {
	  	if($cm->getData('inherited')){
			if($cm->getData('itemtype') != ""){
				$output = " <b>(Inherited from parent category)</b> ".$amazoncore->computeHumanReadibleItemtype($cm, $country);
			}else{
				$output = " <b>(Inherited from parent category)</b> None selected yet. <br /><br /> Please visit the parent category to select an item type before continuing, or alternatively please select a primary category under 'Category On Amazon' on this screen to override the inheritance.";
			}
		}else{
			if($cm->getData('browsenode1') != 0){
				if($cm->getData('itemtype') != ""){
					$output = ' '.$amazoncore->computeHumanReadibleItemtype($cm, $country)."<br /><br />";
				}else{
					$output = " None selected<br /><br />";
				}
				$output .= "<select name='itemtype' onchange='updateItemType(this.options[this.selectedIndex].value);'>";
				$amazoncore = Mage::getModel('amazonimport/amazonimport');
				$bn = Mage::getModel('amazonimport/amazonimportbrowsenodes')->load($cm->getData('browsenode1'));

				$output .= $amazoncore->getPossibleItemtypesSelect($bn->getData('category_name'), $country, $cm->getItemtype());
				$output .= "</select>";
			}else{
				$output .= "Please select a primary Amazon category before continuing.";
			}
		}
	  }else{
		$output = "Please select a primary Amazon category before continuing.";		
	  }
	
	  return $output;
	}

	public function checkforVTITparents($categoryid, $country, $type){
	 
	 	// check for parent categories, and check their mapping settings too.	 
	 	$cat = Mage::getModel('catalog/category')->load($categoryid);
		
	 	if($cat->getParentId()){
	 		return $this->getVTITMappings($cat->getParentId(), $country, $type, true);
		}else{
			return array(0,0);	
		}
		
	}

	public function getVTITMappings($categoryid, $country, $type, $inherited=false){
		
		$cat = Mage::getModel('amazonimport/amazonimportcategorymapping')->getCollection()
			->addFieldToFilter('category_id',$categoryid)
			->addFieldToFilter('country_id',$country);
		$cm = "";
		
		foreach($cat as $cm){
			break;	
		}
			
		if(is_object($cm)){	
			if($type == "VT"){
				if($cm->getData('browsenode1') == 0){
						return $this->checkforVTITparents($categoryid, $country, $type);
				}else{
						return array($cm, $inherited);
				}
			}else{
				if($cm->getData('browsenode1') == 0){
						return $this->checkforVTITparents($categoryid, $country, $type);
				}else{
						return array($cm, $inherited);
				}
			}
		
		}else{
				return $this->checkforVTITparents($categoryid, $country, $type);
		}
	
	}
	
	public function getCategoryCondition($categoryid, $country){
	  list($cm, $inherited) = $this->getVTITMappings($categoryid, $country, "COND");  
	  $output = "";
	  $amazoncore = Mage::getModel('amazonimport/amazonimport');
	  
	  if(is_object($cm)){
		  
				if($cm->getData('inherited')){
					  	if($cm->getData('condition') != ""){
							$output = " <b>(Inherited from parent category)</b> ".$amazoncore->FormatCamelCase($cm->getData('condition'));
						}else{
							$output = " <b>(Inherited from parent category)</b> None selected yet. <br /><br />Please visit the parent category to set the item condition for products within this category, or alternatively please select a primary category under 'Category On Amazon' on this screen to override the inheritance.";
						}
				}else{
						if($cm->getData('condition') != ""){
							$output = ' '.$amazoncore->FormatCamelCase($cm->getData('condition'))."<br /><br />";
						}else{
							$output = ' None selected yet.'."<br /><br />";
						}
						$possibles = $amazoncore->getConditionSelectOptions($cm->getData('condition'));
						
						$output .= "<select name='condition' onchange='updateCondition(this.options[this.selectedIndex].value);'>";
						$output .= $possibles;
						$output .= "</select>";
				
				}
	  }else{
		$output = "Please select a primary Amazon category before continuing.";		
	  }
  	
	  return $output;
  
  }
  
    public function getCategoryConditionNote($categoryid, $country){
	  list($cm, $inherited) = $this->getVTITMappings($categoryid, $country, "CONDNOTE");  
	  $output = "";
	  $amazoncore = Mage::getModel('amazonimport/amazonimport');
	  
	  if(is_object($cm)){
		  
				if($cm->getData('inherited')){
					  	if($cm->getData('condition_note') != ""){
							$output = " <b>(Inherited from parent category)</b> ".$amazoncore->FormatCamelCase($cm->getData('condition_note'));
						}else{
							$output = " <b>(Inherited from parent category)</b> None selected yet. <br /><br />Please visit the parent category to enter a condition note, or alternatively please select a primary category under 'Category On Amazon' on this screen to override the inheritance.";
						}
				}else{
						if($cm->getData('condition') != ""){
							$output = ' '.$amazoncore->FormatCamelCase($cm->getData('condition_note'))."<br /><br />";
						}else{
							$output = ' None selected yet.'."<br /><br />";
						}
						$output .= "<textarea name='theconditionnote' id='theconditionnote'></textarea><br />";
						$output .= "<button class='scalable save' onclick='updateConditionNote($(\"theconditionnote\").value);'>Save</button>";
				
				}
	  }else{
		$output = "Please select a primary Amazon category before continuing.";		
	  }
  	
	  return $output;
  
  }
  
	
	
	public function checkforparents($categoryid, $country){
	 
	 	// check for parent categories, and check their mapping settings too.	 
	 	$cat = Mage::getModel('catalog/category')->load($categoryid);
	 	
	 
	 	if($cat->getParentId()){
	 		return $this->getMappings($cat->getParentId(), $country, true);
		}else{
			return "<center><b>No category or categories selected</b></center>";	
		}
		
	}

	public function getMappings($categoryid, $country, $inherited=false){
		
		$cat = Mage::getModel('amazonimport/amazonimportcategorymapping')->getCollection()
			->addFieldToFilter('category_id',$categoryid)
			->addFieldToFilter('country_id',$country);
		$cm = "";
		
		foreach($cat as $cm){
			break;	
		}
			
		if(is_object($cm)){	
			
			if(($cm->getData('browsenode1') == 0)&&($cm->getData('browsenode2') == 0)){
					$output = $this->checkforparents($categoryid, $country);
			}else{
					if(($cm->getData('browsenode1') != 0)&&($cm->getData('browsenode2') != 0)){
						// get the browsenode from the database.
						$bn1 = Mage::getModel('amazonimport/amazonimportbrowsenodes')->load($cm->getData('browsenode1'));
						$bn2 = Mage::getModel('amazonimport/amazonimportbrowsenodes')->load($cm->getData('browsenode2'));
						if($inherited == false){
							$output = "<center><b>Primary Category:</b> ".$bn1->getData('category_tree_location')." [ <a href='#' onclick='unsetPrimaryCategory();'>Remove</a> ] </center><br />";
							$output .= "<center><b>Secondary Category:</b> ".$bn2->getData('category_tree_location')." [ <a href='#' onclick='unsetSecondaryCategory();'>Remove</a> ] </center>";
						}else{
							$output = "<center><b>Inherited Primary Category:</b> ".$bn1->getData('category_tree_location')."<br />";
							$output .= "<center><b>Inherited Secondary Category:</b> ".$bn2->getData('category_tree_location')."</center>";
						}
						
					}else if($cm->getData('browsenode1') != 0){
						$bn1 = Mage::getModel('amazonimport/amazonimportbrowsenodes')->load($cm->getData('browsenode1'));
						if($inherited == false){
							$output = "<center><b>Primary Category:</b> ".$bn1->getData('category_tree_location')." [ <a href='#' onclick='unsetPrimaryCategory();'>Remove</a> ] </center>";
						}else{
							$output = "<center><b>Inherited Primary Category:</b> ".$bn1->getData('category_tree_location')."</center>";
						}
						
					}else if($cm->getData('browsenode2') != 0){
						$bn1 = Mage::getModel('amazonimport/amazonimportbrowsenodes')->load($cm->getData('browsenode2'));
						if($inherited == false){
							$output = "<center><b>Secondary Category:</b> ".$bn1->getData('category_tree_location')." [ <a href='#' onclick='unsetSecondaryCategory();'>Remove</a> ] </center>";
						}else{
							$output = "<center><b>Inherited Secondary Category:</b> ".$bn1->getData('category_tree_location')."</center>";
						}
					}
			}
		}else{
			$output = $this->checkforparents($categoryid, $country);
		}
	
		return $output;
		
	}

    /**
     * Add new category form
     */
    public function addAction()
    {
        Mage::getSingleton('admin/session')->unsActiveTabId();
        $this->_forward('edit');
    }

    /**
     * Edit category page
     */
    public function editAction()
    {
        $params['_current'] = true;
        $redirect = false;

        $storeId = (int) $this->getRequest()->getParam('store');
        $parentId = (int) $this->getRequest()->getParam('parent');
        $_prevStoreId = Mage::getSingleton('admin/session')
            ->getLastViewedStore(true);

        if ($_prevStoreId != null && !$this->getRequest()->getQuery('isAjax')) {
            $params['store'] = $_prevStoreId;
            $redirect = true;
        }

        $categoryId = (int) $this->getRequest()->getParam('id');
		
		if($categoryId < 1){
			$categoryId = Mage::app()->getStore($storeId)->getRootCategoryId();	
		}
        $_prevCategoryId = Mage::getSingleton('admin/session')
            ->getLastEditedCategory(true);


        if ($_prevCategoryId
            && !$this->getRequest()->getQuery('isAjax')
            && !$this->getRequest()->getParam('clear')) {
           // $params['id'] = $_prevCategoryId;
             $this->getRequest()->setParam('id',$_prevCategoryId);
            //$redirect = true;
        }
	
         if ($redirect) {
            $this->_redirect('*/*/edit', $params);
            return;
        }

        if ($storeId && !$categoryId && !$parentId) {
            $store = Mage::app()->getStore($storeId);
            $_prevCategoryId = (int) $store->getRootCategoryId();
            $this->getRequest()->setParam('id', $_prevCategoryId);
        }

        if (!($category = $this->_initCategory(true))) {
            return;
        }

        /**
         * Check if we have data in session (if duering category save was exceprion)
         */
        $data = Mage::getSingleton('adminhtml/session')->getCategoryData(true);
        if (isset($data['general'])) {
            $category->addData($data['general']);
        }

        /**
         * Build response for ajax request
         */
        if ($this->getRequest()->getQuery('isAjax')) {
            // prepare breadcrumbs of selected category, if any
            $breadcrumbsPath = $category->getPath();
            if (empty($breadcrumbsPath)) {
                // but if no category, and it is deleted - prepare breadcrumbs from path, saved in session
                $breadcrumbsPath = Mage::getSingleton('admin/session')->getDeletedPath(true);
                if (!empty($breadcrumbsPath)) {
                    $breadcrumbsPath = explode('/', $breadcrumbsPath);
                    // no need to get parent breadcrumbs if deleting category level 1
                    if (count($breadcrumbsPath) <= 1) {
                        $breadcrumbsPath = '';
                    }
                    else {
                        array_pop($breadcrumbsPath);
                        $breadcrumbsPath = implode('/', $breadcrumbsPath);
                    }
                }
            }

            Mage::getSingleton('admin/session')
                ->setLastViewedStore($this->getRequest()->getParam('store'));
            Mage::getSingleton('admin/session')
                ->setLastEditedCategory($category->getId());
//            $this->_initLayoutMessages('adminhtml/session');
            $this->loadLayout();
            $this->getResponse()->setBody(Zend_Json::encode(array(
                'messages' => $this->getLayout()->getMessagesBlock()->getGroupedHtml(),
                'content' => $this->getLayout()->createBlock('amazonimport/categorise_edit')->toHtml())));
            return;
        }

        $this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('amazonimport/categorise_edit'));
		$this->_addLeft($this->getLayout()->createBlock('amazonimport/categorise_tree'));
		$this->_setActiveMenu('amazonimport/categorise');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
            ->setContainerCssClass('catalog-categories');

		$block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($storeId);
        }

        $this->renderLayout();
    }

    /**
     * WYSIWYG editor action for ajax request
     *
     */
    public function wysiwygAction()
    {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock('adminhtml/catalog_helper_form_wysiwyg_content', '', array(
            'editor_element_id' => $elementId,
            'store_id'          => $storeId,
            'store_media_url'   => $storeMediaUrl,
        ));

        $this->getResponse()->setBody($content->toHtml());
    }

    /**
     * Get tree node (Ajax version)
     */
    public function categoriesJsonAction()
    {
        if ($this->getRequest()->getParam('expand_all')) {
            Mage::getSingleton('admin/session')->setIsTreeWasExpanded(true);
        } else {
            Mage::getSingleton('admin/session')->setIsTreeWasExpanded(false);
        }
        if ($categoryId = (int) $this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $categoryId);

            if (!$category = $this->_initCategory()) {
                return;
            }
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('adminhtml/catalog_category_tree')
                    ->getTreeJson($category)
            );
        }
    }

    /**
     * Category save
     */
    public function saveAction()
    {
        // do things here.
    }

    public function treeAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        $categoryId = (int) $this->getRequest()->getParam('id');

        if ($storeId) {
            if (!$categoryId) {
                $store = Mage::app()->getStore($storeId);
                $rootId = $store->getRootCategoryId();
                $this->getRequest()->setParam('id', $rootId);
            }
        }

        $category = $this->_initCategory(true);

        $block = $this->getLayout()->createBlock('adminhtml/catalog_category_tree');
        $root  = $block->getRoot();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            'data' => $block->getTree(),
            'parameters' => array(
                'text'        => $block->buildNodeName($root),
                'draggable'   => false,
                'allowDrop'   => false,
                'id'          => (int) $root->getId(),
                'expanded'    => (int) $block->getIsWasExpanded(),
                'store_id'    => (int) $block->getStore()->getId(),
                'category_id' => (int) $category->getId(),
                'root_visible'=> (int) $root->getIsVisible()
        ))));
    }

    /**
    * Build response for refresh input element 'path' in form
    */
    public function refreshPathAction()
    {
        if ($id = (int) $this->getRequest()->getParam('id')) {
            $category = Mage::getModel('catalog/category')->load($id);
            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(array(
                   'id' => $id,
                   'path' => $category->getPath(),
                ))
            );
        }
    }



	  public function from_camel_case($str) {
	    $str[1] = strtolower($str[1]);
	    $func = create_function('$c', 'return " " . strtolower($c[1]);');
	    return preg_replace_callback('/([A-Z])/', $func, $str);
	  }
	
	
	public function getbrowsenodegridAction(){
	 $this->getResponse()->setBody($this->getLayout()->createBlock('amazonimport/categorise_edit_tab_browsenodegrid')->setId('browsenodegrid')->toHtml());	
	}
	
	
	public function reloadproducttypeAction(){
		
		$data = $this->getRequest()->getPost();
		$pdt = $data['producttype_for_datatype'];
		$currentpdt = $data['currentpdt'];
		
		$xml = Mage::getModel('amazonimport/amazonimport')->getTemplateXml();
		
		foreach($xml->ProductData[0] as $producttype){
			
			if($producttype->getName() == $pdt){
								
				if($pdt == "Clothing"){
					
					$typesarray = array();
					
					$typestemp = $producttype->ClassificationData->ClothingType->children();
					foreach($typestemp as $type){
						$typesarray["".(string) $type[0]->Value.""] = ucwords($this->from_camel_case((string) $type[0]->Value));
					}
					
				}else{
					
					// bugfix 07072010 - check for productdatatype as a CamOption; display as drop down.
					try{
						
						if($producttype->ProductType->children()->getName() == "CamOption"){
							
							foreach($producttype->ProductType->children() as $type){
								$typesarray["".(string) $type[0]->Value.""] = ucwords($this->from_camel_case((string) $type[0]->Value));												
							}
						
						
						}else{
						
							if(isset($producttype->ProductType)){
								
								$typesarray = array();
								
								foreach($producttype->ProductType->children() as $type){
									$typesarray["".(string) $type->getName().""] = ucwords($this->from_camel_case((string) $type->getName()));												
								}
								
							}
						}
						
					}catch (Exception $e){
											
							if(isset($producttype->ProductType)){
								
								$typesarray = array();
								
								foreach($producttype->ProductType->children() as $type){
									$typesarray["".(string) $type->getName().""] = ucwords($this->from_camel_case((string) $type->getName()));												
								}
								
							}
						
					}
				}
				
			}
			
			$producttype = "";			
		}
		
		
		if(isset($typesarray)){
		$output = '<table cellspacing="0" class="form-list">
            <tbody>

<tr>
    <td class="label"><label for="productdatatype">Product Data Type</label></td>
    <td class="value">
    <select id="productdatatype" name="productdatatype" class=" select">';
	
		$output .= '<option value="">--- Please Select ---</option>';
	
		foreach($typesarray as $key=>$value){
			$output .= '<option value="'.$key.'"';
			
		
			if($key == $currentpdt){
				$output .= ' selected="selected"';
			}
			
			$output .= '>'.utf8_decode($value).'</option>';
		}
		
	$output .= '</select>
	</td>
    <td class="scope-label"></td>
        <td><small>&nbsp;</small></td>
</tr>

            </tbody>
        </table>';
		
								
		echo "<script type='text/javascript'>
				parent.document.getElementById('displayproducttype').innerHTML = unescape('".rawurlencode($output)."');		
			  </script>";
		
		}else{
			$output = '<input type="hidden" id="productdatatype" name="productdatatype" value="">'."Either no 'Product Type' has been selected on the 'Category' tab, or this category does not require 'Product Data Type' to be set.";
			echo "<script type='text/javascript'>
				parent.document.getElementById('displayproducttype').innerHTML = unescape('".rawurlencode($output)."');		
			  </script>";
		
			
		}
		
	}
	
	
	
	public function reloadproducttypehelpAction(){
		
		$data = $this->getRequest()->getPost();
		$pdt = $data['producttype_for_datatype'];
		
		$xml = Mage::getModel('amazonimport/amazonimport')->getTemplateXml();
		
		foreach($xml->ProductData[0] as $producttype){
			
			if($producttype->getName() == $pdt){
								
				if($pdt == "Clothing"){
					
					$typesarray = array();
					
					$typestemp = $producttype->ClassificationData->ClothingType->children();
					foreach($typestemp as $type){
						$typesarray["".(string) $type[0]->Value.""] = ucwords($this->from_camel_case((string) $type[0]->Value));
					}
					
				}else{
					
					if(isset($producttype->ProductType)){
						
						$typesarray = array();
						
						foreach($producttype->ProductType->children() as $type){
							$typesarray["".(string) $type->getName().""] = ucwords($this->from_camel_case((string) $type->getName()));												
						}
						
					}
					
				}
				
			}
			
			$producttype = "";			
		}
		
		
		if(isset($typesarray)){
		$output = '<select id="productdatatype" name="productdatatype" class=" select" onchange="document.getElementById(\'showoptionvalue_productdatatype\').innerHTML = this.options[this.selectedIndex].value;">';
	
		$output .= '<option value="">--- Please Select ---</option>';
	
		foreach($typesarray as $key=>$value){
			$output .= '<option value="'.$key.'">'.utf8_decode($value).'</option>';
		}
		
	$output .= '</select>';
		
								
		echo "<script type='text/javascript'>
				parent.document.getElementById('showoptions_productdatatype').innerHTML = unescape('".rawurlencode($output)."');		
			  </script>";
		
		}else{
			$output = '<input type="hidden" id="productdatatype" name="productdatatype" value="">'."No Value - leave this column blank for this product.";
			echo "<script type='text/javascript'>
				parent.document.getElementById('showoptionvalue_productdatatype').innerHTML = unescape('".rawurlencode($output)."');		
			  </script>";
		
			
		}
		
	}

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/categories');
    }

public function comAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("com",'camiloo_amazon_categorise_marketplace');
		$this->indexAction();		
	}
	
	public function ukAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("uk",'camiloo_amazon_categorise_marketplace');
		$this->indexAction();		
	}
	
	public function frAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("fr",'camiloo_amazon_categorise_marketplace');
		$this->indexAction();		
	}
	
	public function deAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("de",'camiloo_amazon_categorise_marketplace');
		$this->indexAction();		
	}

/*
	protected function _initAction() {
		$this->loadLayout();	
		return $this;
	}   
	
	public function indexAction() {
		$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);
		
		$this->loadLayout();
		if($iview == true){
		$this->_addContent($this->getLayout()->createBlock('amazonimport/categorise_gridheader'));
		$this->_addContent($this->getLayout()->createBlock('amazonimport/categorise'));
		}
		$this->renderLayout();
	}
	
	public function editAction() {
		$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('amazonimport/amazonimportcategorise'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_categorise_marketplace'))->getCollection()->addFieldToFilter('productid',array($id));
		if(sizeof($model) > 0){
			$data = $model->getData();	
		}else{
			$data = array(array());
		}
		
		Mage::register('amazonimport_categorisedata', $data);
		
		$this->loadLayout();
		if($iview == true){
		$this->_addContent($this->getLayout()->createBlock('amazonimport/categorise_edit'));
		$this->_addLeft($this->getLayout()->createBlock('amazonimport/categorise_edit_tabs'));
		}
		$this->renderLayout();
	}
	
	public function saveAction(){
		
		$data = $this->getRequest()->getPost();
		
		// first, we must check if there is a row for this product id - if so, delete it.
		$model = Mage::getModel('amazonimport/amazonimportcategorise'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_categorise_marketplace'))->getCollection()->addFieldToFilter('productid',array($data['productid']));
		if(sizeof($model) > 0){
			foreach($model as $mdl){
				$mdl->delete();
			}	
		}
		
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_categorise_marketplace');
		$model = Mage::getModel('amazonimport/amazonimportcategorise'.$country);
		$model->setData('productid',$data['productid']);
		$model->setBrowsenode1($data['browsenode1']);
		$model->setBrowsenode2($data['browsenode2']);
		$model->setProducttype($data['producttype']);
		if	(isset($data['productdatatype'])) {
			$model->setProductdatatype($data['productdatatype']);
		}
		else
		{
			$model->setProductdatatype(null);
		}
		$model->setCategory($data['category']);
		$model->setCondition($data['condition']);
		$model->setConditionNote($data['condition_note']);
		$model->save();	
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$result = $db->query("select * from {$table_prefix}amazonimport_setup_".$country." WHERE initial_setup_complete = 1 AND productid=".$data['productid']." AND productid not in (select productid from {$table_prefix}amazonimport_errorlog_".$country." where productid=".$data['productid']." AND submission_type='Product')");
		while($row = $result->fetch(PDO::FETCH_ASSOC)){
			$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$data['productid'].",'Product')");
		}
		
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__('Product has been categorised'));
		$this->_redirect('////');	
	
	}
	
		

	
	
	public function bulkprocessAction(){
		$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);
		
		$this->loadLayout();
		if($iview == true){
		$this->_addContent($this->getLayout()->createBlock('amazonimport/categorise_bulk_edit'));
		$this->_addLeft($this->getLayout()->createBlock('amazonimport/categorise_bulk_edit_tabs'));
		}
		$this->renderLayout();
	}
	
	
	*/
	
}

?>