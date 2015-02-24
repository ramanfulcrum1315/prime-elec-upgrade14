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

class Camiloo_Amazonimport_ManualsetupController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout();
		return $this;
	}

	public function editAction()
	{
		$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);

		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')
			->loadSessionValue('camiloo_amazon_manualsetup_marketplace'), 'camiloo_amazon_manualsetup_marketplace');
		
		if(isset($_POST['product']))
		{
			$productid = implode(",",$_POST['product']);
		}
		else
		{
			$productid = $this->getRequest()->getParam('id');
		}

		// new- bulk handling logic.
		if(strpos($productid,",") > 0){
			$idsample = explode(",",$productid);
			$idsample = $idsample[0];
		}else{
			$idsample = $productid;
		}

		$cats = Mage::getModel('amazonimportcategorise'.Mage::getModel('amazonimport/amazonimport')
			->loadSessionValue('camiloo_amazon_manualsetup_marketplace').'/amazonimportcategorise'.Mage::getModel('amazonimport/amazonimport')
			->loadSessionValue('camiloo_amazon_manualsetup_marketplace'))->getCollection()->addFieldToFilter('productid',array('eq'=>$idsample));
		
		foreach($cats as $categorised)
		{
			break;
		}

		if(!isset($categorised)){			
			Mage::register('pdt',$this->getRequest()->getParam('productdatatype'));
			$db = Mage::getSingleton('core/resource')->getConnection('core_write');
			$table_prefix = Mage::getConfig()->getTablePrefix();
			$mkt = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');

			$query = "SELECT * FROM {$table_prefix}catalog_product_".$mkt." GROUP BY productdatatype";
			$querytwo = "SELECT * FROM {$table_prefix}amazonimport_categorymapping WHERE itemtype != '' AND country_id='".$mkt."' GROUP BY itemtype;";
		
		}else{
			Mage::register('pdt',$categorised->getData('productdatatype'));
			Mage::register('pt',$categorised->getData('pt'));
		}
		
		$mainblock = $this->getLayout()->createBlock('amazonimport/manualsetup_edit');

		$block = $this->getLayout()->createBlock('amazonimport/manualsetup_edit_tabs');

		$this->loadLayout();
		if($iview){
			$this->_addContent($mainblock);
			$this->_addLeft($block);
		}
		$this->renderLayout();

	}

	public function from_camel_case($str) {
		$str[1] = strtolower($str[1]);
		$func = create_function('$c', 'return " " . strtolower($c[1]);');
		return preg_replace_callback('/([A-Z])/', $func, $str);
	}


	public function indexAction()
	{
                /* 
                   Bugfix for issue KDO-449-54740 - MN
                   Sometimes Magento skips over table creation during upgrade. 
                   Added new method 'checkTables' to Amazonimport - calling in place will eradicate issue.
                */
                Mage::getModel('amazonimport/amazonimport')->checkTables();
		$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);

		$mdl = "";

		$id = $this->getRequest()->getParam('id');

		$mkt = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');

		$model = Mage::getModel('amazonimport/amazonimportlistthis'.$mkt)->getCollection()->addFieldToFilter('is_active',1);
		
		foreach ($model as $mdl)
		{
			break;
		}
		
		if (is_object($mdl))
		{
			// need to find which products on the system have been categorised, and what they have been categorised as.
			// from there, we can loop through and display the required grids.
			$db = Mage::getSingleton('core/resource')->getConnection('core_write');
			$table_prefix = Mage::getConfig()->getTablePrefix();
			
			$query = "SELECT * FROM {$table_prefix}amazonimport_categorise_".$mkt." GROUP BY productdatatype";
			$querytwo = "SELECT * FROM {$table_prefix}amazonimport_categorymapping WHERE itemtype != '' AND country_id='".$mkt."' GROUP BY itemtype;";
			$ptype = array();
			$productdatatypearray = array();
			$blocks = array();
			$blocknames = array();
			$catTable = 'amazonimportcategorise'.$mkt.'/amazonimportcategorise'.$mkt;
			$setupTable = 'amazonimportsetup'.$mkt.'/amazonimportsetup'.$mkt;
			$listThisTable = 'amazonimportlistthis'.$mkt.'/amazonimportlistthis'.$mkt;
				
			// we have a result set with active category mappings and one with pdt overrides.
				
			$result = $db->query($query);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			foreach ($rows as $row)
			{	
		 		$collection = Mage::getModel('catalog/product')
		 			->getCollection()
		 			->addAttributeToSelect('sku')
		 			->addAttributeToSelect('name')
		 			->joinTable($catTable, 'productid=entity_id',
		 				array('productid'=>'productid','productdatatype'=>'productdatatype'), null, 'left') 
		 			->joinTable($setupTable, 'productid=productid',
		 				array('initial_setup_complete'=>'initial_setup_complete','setup_type'=>'setup_type','setup_type'=>'setup_type'), null, 'left')
	 				->joinTable($listThisTable, 'productid=entity_id', array('is_active'=>'is_active'), null, 'left')
		 			->addFieldToFilter('productdatatype', array('eq' => $row['productdatatype']))
		 			->addFieldToFilter('is_active', array('eq'=>'1'));
		 	 
			 		 if ($collection->getSize() > 0)
					 {				 
						$ptype[''.$row['productdatatype'].''.$row['producttype'].''] = $row;
						$productdatatypearray[] = $row['productdatatype'];
					 }
					 
			}
			
			$result = $db->query($querytwo);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			foreach ($rows as $row)
			{
					$itemtype = explode("/",$row['itemtype']);
					$producttype = $itemtype[0];
					$ptype[''.$itemtype[sizeof($itemtype)-1].''.$producttype.''] = array('cat'=>true,'producttype'=>$producttype,'productdatatype'=>$itemtype[sizeof($itemtype)-1]);
					$productdatatypearray[] = $itemtype[sizeof($itemtype)-1];
			}
			
			
			
            $blocks[] = $this->getLayout()->createBlock('amazonimport/manualsetup_griddivider')->setData('productdatatype',"Uncategorised");
            $blocks[] = $this->getLayout()->createBlock('amazonimport/manualsetup_ucgrid')->setData('productdatatype',"Uncategorised");
            $blocknames[] = "Uncategorised";
			
			
						
			$xml = Mage::getModel('amazonimport/amazonimport')->getTemplateXml($mkt);

			foreach ($ptype as $row)
			{
			    $cat = "cat";
					
			 	$xp1 = '//ProductData/'.$row['producttype']
			 	    .'/ProductType/'.$row['productdatatype'].'/VariationData';
			 	    
			 	if (isset($row['productdatatype']) && 
			 	    isset($row['producttype']))
			 	{
					$xp1 = str_replace("\"", "", $xp1);
			 	    $test = $xml->xpath($xp1);
			 	
					if(sizeof($test) == 0){
						 
						$test = $xml->xpath('//ProductData/'.$row['producttype'].'/VariationData');
						if(sizeof($test) > 0){
	
						$blocks[] = $this->getLayout()->createBlock('amazonimport/manualsetup_griddivider')->setData('productdatatype',$row['productdatatype']);
						$blocks[] = $this->getLayout()->createBlock('amazonimport/manualsetup_'.$cat.'grid')->setData('productdatatype',$row['productdatatype']);
						$blocknames[] = $row['productdatatype'];
		
						}else{
						
						$blocks[] = $this->getLayout()->createBlock('amazonimport/manualsetup_griddivider')->setData('productdatatype',$row['productdatatype']);
						$blocks[] = $this->getLayout()->createBlock('amazonimport/manualsetup_'.$cat.'grid')->setData('productdatatype',$row['productdatatype']);
						$blocknames[] = $row['productdatatype'];
						
						}
					}else{
	
						$blocks[] = $this->getLayout()->createBlock('amazonimport/manualsetup_griddivider')->setData('productdatatype',$row['productdatatype']);
						$blocks[] = $this->getLayout()->createBlock('amazonimport/manualsetup_'.$cat.'grid')->setData('productdatatype',$row['productdatatype']);
						$blocknames[] = $row['productdatatype'];
						 
					}
				}
			 }
	
			$headBlocks = array($this->getLayout()->createBlock('amazonimport/manualsetup_gridheader'),$this->getLayout()->createBlock('amazonimport/manualsetup_griddivider')->setData('divtype','first')->setData('tab_names',$blocknames));
			$footerBlock = $this->getLayout()->createBlock('amazonimport/manualsetup_griddivider')->setData('divtype','last')->setData('tab_names',$blocknames);
		 $this->loadLayout();
		 if($iview)
		 {
			foreach($headBlocks as $block)
		 	{
				$this->_addContent($block);
			}
			 
			foreach($blocks as $block)
		 	{
				$block->setData('divtype','normal');
				$this->_addContent($block);
			}
			$this->_addContent($footerBlock);
		 }

		 $this->renderLayout();

		}
		else
		{

			Mage::getModel('amazonimport/amazonimport')->saveSessionValue($mkt, 'camiloo_amazon_manualsetup_marketplace');
			if($iview){
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amazonimport')->__('You do not currently have any products selected to list on Amazon in this country. Please click into Select Products to List first.'));
			}
			$this->loadLayout();
			$this->renderLayout();
		}
	}

	public function comAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("com",'camiloo_amazon_manualsetup_marketplace');
		$this->indexAction();
	}

	public function ukAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("uk",'camiloo_amazon_manualsetup_marketplace');
		$this->indexAction();
	}

	public function frAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("fr",'camiloo_amazon_manualsetup_marketplace');
		$this->indexAction();
	}

	public function deAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("de",'camiloo_amazon_manualsetup_marketplace');
		$this->indexAction();
	}


	public function getManualsetupValue($xmlpath,$productid){

		$ismapped = false;
		$model = "";
		$mktplace = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');
		$val = Mage::getModel('amazonimportmanualsetup'.$mktplace.'/amazonimportmanualsetup'.$mktplace)->getCollection()->addFieldToFilter('xmlkey',array('eq'=>$xmlpath))->addFieldToFilter('productid',array('eq'=>$productid));
		foreach($val as $model){
			break;
		}

		if(is_object($model)){
			return $model->getManualsetupvalue();
		}else{
			return "";
		}

	}

	public function getgridAction(){
		
		 $pdt = $this->getRequest()->getParam('productdatatype');
		 $this->getResponse()->setBody($this->getLayout()->createBlock('amazonimport/manualsetup_grid')->setData('productdatatype',$pdt)->toHtml());	
	}
	
	
	public function getbrowsenodegridAction(){
	 $this->getResponse()->setBody($this->getLayout()->createBlock('amazonimport/manualsetup_categorychange_edit_tab_browsenodegrid')->setId('browsenodegrid')->toHtml());	
	}
	
	
	public function getcatconditionAction(){
		 $this->getResponse()->setBody($this->getLayout()->createBlock('amazonimport/manualsetup_categorychange_edit_tab_condition')->toHtml());	
	}
	public function getcatconditionnoteAction(){
		 $this->getResponse()->setBody($this->getLayout()->createBlock('amazonimport/manualsetup_categorychange_edit_tab_conditionnote')->toHtml());
	}
	
	
	public function getchangecategoryAction(){
		 $this->getResponse()->setBody($this->getLayout()->createBlock('amazonimport/manualsetup_categorychange_edit_tab_bng1header')->toHtml()
									   .$this->getLayout()->createBlock('amazonimport/manualsetup_categorychange_edit_tab_browsenodegrid')->toHtml()
									   .$this->getLayout()->createBlock('amazonimport/manualsetup_categorychange_edit_tab_bng1footer')->toHtml());	
	}
	
	public function getchangecategory2Action(){
		 $this->getResponse()->setBody($this->getLayout()->createBlock('amazonimport/manualsetup_categorychange_edit_tab_bng2header')->toHtml()
									   .$this->getLayout()->createBlock('amazonimport/manualsetup_categorychange_edit_tab_browsenodegrid')->toHtml()
									   .$this->getLayout()->createBlock('amazonimport/manualsetup_categorychange_edit_tab_bng2footer')->toHtml());	
	}
	
	public function getchangecategory3Action(){
		
		$bn1 = $this->getRequest()->getParam('bn1');
		
		 $this->getResponse()->setBody($this->getLayout()->createBlock('amazonimport/manualsetup_categorychange_edit_tab_further')->setData('primarycat',$bn1)->toHtml());	
	}
	public function getchangecategory4Action(){
		
		if($this->getRequest()->getParam('isprocesscomplete') == "yes"){
		
			$this->getResponse()->setBody($this->getLayout()->createBlock('amazonimport/manualsetup_categorychange_edit_tab_further')->setData('complete','true')->toHtml());	
		
		
		}else{
		
			$itype = $this->getRequest()->getParam('itype');
			$this->getResponse()->setBody($this->getLayout()->createBlock('amazonimport/manualsetup_categorychange_edit_tab_further')->setData('itemtype',$itype)->toHtml());	
		
		}
	}
		
	public function getcatgridAction(){
		
		 $pdt = $this->getRequest()->getParam('productdatatype');
		 $this->getResponse()->setBody($this->getLayout()->createBlock('amazonimport/manualsetup_catgrid')->setData('productdatatype',$pdt)->toHtml());	
	}
	public function getucgridAction(){
		
		 $this->getResponse()->setBody($this->getLayout()->createBlock('amazonimport/manualsetup_ucgrid')->toHtml());	
	}
	
	
	public function getchangeconditionAction(){
		 $this->getResponse()->setBody($this->getLayout()->createBlock('amazonimport/manualsetup_conditionchange_edit_tab_condition')->toHtml());	
	}
	public function getchangeconditionnoteAction(){
		 $this->getResponse()->setBody($this->getLayout()->createBlock('amazonimport/manualsetup_conditionchange_edit_tab_conditionnote')->toHtml());
	}
	public function getchangeconditionthanksAction(){
		 $this->getResponse()->setBody($this->getLayout()->createBlock('amazonimport/manualsetup_conditionchange_edit_tab_thanks')->toHtml());
	}
	
	
	public function removeanyoverridesAction(){
	
		$products = $_POST['product'];

		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');
	
	
		foreach($products as $id){
			if($id != ""){
				$db->query("DELETE FROM {$table_prefix}amazonimport_categorise_$country WHERE productid = $id");
				$db->query("DELETE FROM {$table_prefix}amazonimport_variations_$country WHERE configurable_product_id = $id");
			}
		}
	
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__('Removed category mapping overrides from selected products'));
		$this->_redirect('*/*/');

	
	}
	
	public function finalsavechangeAction(){
		
		$data = $this->getRequest()->getPost();
		if($data['sendform-bn1'] == ""){
			
			// condition update only.
			$itemtype = explode("/",$data['sendform-itemtype']);
			$producttype = $itemtype[0];
			$productdatatype = $itemtype[sizeof($itemtype)-1];
		
			// this change will have affected a grid as well as the grid the update was triggered upon. 
			// we must therefore also return this
			$affected = "grid".$productdatatype;
			$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');
		
			// clear out any old settings, as we have a new one coming in.
			$db = Mage::getSingleton("core/resource")->getConnection("core_write");
			$table_prefix = Mage::getConfig()->getTablePrefix();
		
			$ids = explode(",",$data['sendform-idvalues']);
	
			foreach($ids as $id){
				if($id != ""){
			
				$db->query("UPDATE {$table_prefix}amazonimport_categorise_$country  SET `condition`='".$data['sendform-condition']."', `condition_note`='".mysql_escape_string($data['sendform-conditionnote'])."' WHERE productid=$id");
				
				}
			}
			echo "<script type='text/javascript'>
					parent.conditionupdatecomplete('');
				  </script>";
			
		}else{
		
			$itemtype = explode("/",$data['sendform-itemtype']);
			$producttype = $itemtype[0];
			$productdatatype = $itemtype[sizeof($itemtype)-1];
		
			// this change will have affected a grid as well as the grid the update was triggered upon. 
			// we must therefore also return this
			$affected = "grid".$productdatatype;
			$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');
		
			// clear out any old settings, as we have a new one coming in.
			$db = Mage::getSingleton("core/resource")->getConnection("core_write");
			$table_prefix = Mage::getConfig()->getTablePrefix();
		
			$ids = explode(",",$data['sendform-idvalues']);
	
			foreach($ids as $id){
				if($id != ""){
				$db->query("DELETE FROM {$table_prefix}amazonimport_categorise_$country WHERE productid = $id");
				$db->query("DELETE FROM {$table_prefix}amazonimport_variations_$country WHERE configurable_product_id = $id");
				
				$db->query("INSERT INTO {$table_prefix}amazonimport_categorise_$country (`productid`,`browsenode1`,`browsenode2`,`productdatatype`,`producttype`,`condition`,`condition_note`) VALUES ($id,'".$data['sendform-bn1']."','".$data['sendform-bn2']."','".$productdatatype."','".$producttype."','".$data['sendform-condition']."','".$data['sendform-conditionnote']."')");
							$db->query("INSERT INTO {$table_prefix}amazonimport_variations_$country (`configurable_product_id`,`variation_theme`) VALUES ($id,'".$data['sendform-variationtheme']."')");
				}
			}
			echo "<script type='text/javascript'>
					parent.missioncomplete('".$affected."');
				  </script>";
				  
		}
	}

	public function getentryformAction(){
	    $mkt = Mage::getModel('amazonimport/amazonimport')->loadSessionValue( 'camiloo_amazon_manualsetup_marketplace');

		Mage::getModel('amazonimport/amazonimport')->saveSessionValue($mkt,
				'camiloo_amazon_manualsetup_marketplace');

		$data = $this->getRequest()->getPost();
		$realelement = $data['getentryform-xmlkey'];
		$productid = $data['productid'];
		$repIndex = $data['getentryform-repelemindex'];

		$currentvalue = rawurldecode($data['getentryform-currentvalue']);
		$element = str_replace("","",$data['getentryform-xmlkey']);

		// first of all, we should load the xml element
		$xml = Mage::getModel('amazonimport/amazonimport')-> getTemplateXml($mkt);

		$_exprTempForXPath = $element;

		if (strpos($element, "-") !== false) {
			$parts = explode("-", $element);
			$element = $parts[0].'['.$parts[1].']';
				
			$_exprTempForXPath = $parts[0].'[1]';
				
			$realelement = $parts[0];
		}

		$node = $xml->xpath("//".$_exprTempForXPath);
		$node = $node[0];
		$outputattributes = array();

		// and see if there are attributes...
		if(isset($node->Attributes)){

			foreach($node->Attributes->children() as $attribute){

				if ($repIndex == -1)
				{
					$newlocation = $realelement."/Attributes/".$attribute->getName();
				}
				else {
					$newlocation = $realelement."[$repIndex]/Attributes/".$attribute->getName();
				}
				// if so, what are the current values of these attributes?
				$currentattribute['value'] = $this->getManualsetupValue($newlocation, $productid);
					
				$currentattribute['newlocation'] = $newlocation;
					
				// how should we display this attribute
				if(isset($attribute->CamOption[0])){
					$currentattribute['type'] = "select";
					$options = array();
					foreach($attribute->CamOption as $option){
						if(strlen($option->Label[0]) > 2){
							$options[''.(string) $option->Label[0].''] = ucwords($this->from_camel_case((string) $option->Label[0]));
						}else{
							$options[''.(string) $option->Label[0].''] = ucwords((string) $option->Label[0]);
						}
					}

					$currentattribute['options'] = $options;
				}else{
					$currentattribute['type'] = "text";
				}

				$currentattribute['name'] = $attribute->getName();
					
				$outputattributes[] = $currentattribute;
					
			}

		}
			

		// next, what is the current value of the main element?

		$_manualValKey = $repIndex == -1 ? $realelement : ($realelement.'['.$repIndex.']');

		if($this->getManualsetupValue($_manualValKey, $productid) != ""){
			$currentvalue = $this->getManualsetupValue($_manualValKey, $productid);
			$sepChar = "|";
			$currentvalue = explode($sepChar, $currentvalue);
		}
		if(!is_array($currentvalue))
		{
			$currentvalue = array($currentvalue);
		}

		// how should we display this element?
		if(isset($node->CamOption[0])){
			$type = "select";
			$class = "";
			$options = array();

			foreach($node->CamOption as $option){
				$options[''.(string) $option->Label[0].''] = ucwords($this->from_camel_case((string) $option->Label[0]));
			}

		}else{

			$options = array();

			if($node['CamType'] == "String"){
				$type = "text";
				$class = "";
			}else if($node['CamType'] == "Integer"){
				$type = "text";
				$class = "validate-digits";
			}else if($node['CamType'] == "Decimal"){
				$type = "text";
				$class = "validate-number";
			}else if($node['CamType'] == "Boolean"){
				$type = "checkbox";
				$class = "";
			}else if($node['CamType'] == "DateAndTime"){
				$type = "calendar";
				$class = "";
			}
		}
		$lowerlimit = ""; $upperlimit = ""; $repeat = 1; $maxlength = ""; $minlength = "";	$decimals = "";
			
		if(isset($node['CamGreaterThan'])){
			$lowerlimit = $node['CamGreaterThan'] + 1;
		}
		if(isset($node['CamLessThan'])){
			$upperlimit = $node['CamLessThan'] - 1;
		}
		if(isset($node['CamLessThanOrEqual'])){
			$upperlimit = $node['CamLessThanOrEqual'];
		}
		if(isset($node['CamGreaterThanOrEqual'])){
			$lowerlimit = $node['CamGreaterThanOrEqual'];
		}
		if(isset($node['CamElementRepeatLimit'])){
			$repeat = (int) $node['CamElementRepeatLimit'];
		}
		if(isset($node['CamMaxLength'])){
			$maxlength = $node['CamMaxLength'];
		}
		if(isset($node['CamMinLength'])){
			$minlength = $node['CamMinLength'];
		}
		if(isset($node['CamTotalDigits'])){
			$maxlength = $node['CamTotalDigits'];
		}
		if(isset($node['CamFractionDigits'])){
			$decimals = $node['CamFractionDigits'];
		}

			
		$idarray = array();
		// return display output form.

		if ($repIndex == -1)
		{
			echo "<script type='text/javascript'>
				parent.document.getElementById('".$realelement."-values').innerHTML = unescape('";

		}
		else {
			echo "<script type='text/javascript'>
				parent.document.getElementById('".$realelement."-".$repIndex."-values').innerHTML = unescape('";
		}

		ob_start();
		?>

<input
	type="hidden" name="form_key" id="form_key"
	value="<?=Mage::getSingleton('core/session')->getFormKey() ?>" />


		<?php
		$selectwasoutput = false;

		for ($i = 1; $i <= $repeat; $i++)
		{
			$_elemId = $repIndex == -1 ? ($realelement."-elementvalue")
			: ($realelement."-".$repIndex."-elementvalue");

			switch($type)
			{

				case "select":
					if( ($repeat > 1) && ($selectwasoutput == false) )
					{
						$selectwasoutput = true;


						?>
<select name="<?php echo $_elemId ?>" id="<?php echo $_elemId; ?>"
	multiple="multiple" onchange=" if ($(this).getValue().length > <?php echo $repeat; ?>) { $(this).setValue(last_valid_selection); } else {  last_valid_selection = $(this).getValue(); }">
	<?php foreach($options as $key=>$value){ ?>
	<option value="<?php echo $key; ?>"
	<?php if(array_search($key,$currentvalue) !== false){ ?>
		selected="selected" <?php } ?>><?php echo utf8_decode($value); ?></option>
		<?php } ?>
</select><br /><span style="font-size:10px;">Up to <?php echo $repeat; ?> values can be selected. Select multiple values by holding down Ctrl + clicking. Mac users, please press command + click.</span>
		<?php
					}else{
						if($selectwasoutput == false){
						?>
<select name="<?php echo $_elemId ?>" id="<?php echo $_elemId; ?>">
	<option value="">--- Please Select ---</option>
	<?php foreach($options as $key=>$value){ ?>
	<option value="<?php echo $key; ?>"
	<?php if(array_search($key,$currentvalue) !== false){ ?>
		selected="selected" <?php } ?>><?php echo utf8_decode($value); ?></option>
		<?php } ?>
</select>
		<?php
						}
					}

					$idarray[] = $_elemId;
						
					break;

case "checkbox":

	$options = array('1'=>'Yes','0'=>'No');
	?>
<select name="<?php echo $_elemId; ?>"
	id="<?php echo $_elemId; echo $i; ?>">
	<option value="">--- Please Select ---</option>
	<?php foreach($options as $key=>$value){ ?>
	<option value="<?php echo $key; ?>"
	<?php if((array_search($key,$currentvalue) !== false)&&($key != "")){ ?>
		selected="selected" <?php } ?>><?php echo utf8_decode($value);  ?></option>
		<?php } ?>
</select>

		<?php


		$idarray[] = $_elemId.$i;

		break;


case "calendar":
	?>
<input name="<?php echo $_elemId; ?>" id="<?php echo $_elemId; ?>" value=""
	class=" input-text" type="text" style="width: 110px;"
	value="<?php if(isset($currentvalue[$i-1])){ echo $currentvalue[$i-1]; } ?>" />
<img
	src="/skin/adminhtml/default/default/images/grid-cal.gif" alt=""
	class="v-middle" id="calzone-trigger" title="Select Date" style="">
	<?php

	$idarray[] = $_elemId;
	break;

case "text":
	if($maxlength > 100)
	{
		?>
<textarea class="<?php echo $class; ?>"
	id="<?php echo $_elemId; echo $i; ?>" name="<?php echo $_elemId; ?>[]"><?php if(isset($currentvalue[$i-1])){ echo $currentvalue[$i-1]; } ?></textarea>

		<?php
	}else{
		?>
<input
	type="text" class="<?php echo $class; ?>"
	id="<?php echo $_elemId; echo $i; ?>" name="<?php echo $_elemId; ?>[]"
	value="<?php if(isset($currentvalue[$i-1])){ echo $currentvalue[$i-1]; } ?>" />
		<?php
	}

	$idarray[] = $_elemId.$i;
	break;



			}

			foreach($outputattributes as $attribute){

				$realelementtemp  = $attribute['newlocation'];
				$currentattributevalue  = explode("|",$attribute['value']);
				$attributetype  = $attribute['type'];
				$attributeoptions  = $attribute['options'];
				if(!is_array($currentattributevalue)){
					$currentattributevalue = array($currentattributevalue);
				}else{
					if(isset($currentattributevalue[$i-1])){
						$currentattributevalue = array($currentattributevalue[$i-1]);
					}else{
						$currentattributevalue = array('');
					}
				}
				/*
				 * $data['getentryform-xmlkey']."/Attributes/".$attribute['name'];
				 *
				 $_elemId = $repIndex == -1 ? ($realelement."-elementvalue")
				 : ($realelement."-".$repIndex."-elementvalue");
				 */
				switch($attributetype){

					case "text":
						?>
<input type="text"
	id="<?php echo $realelementtemp; ?>-elementvalue<?php echo $i; ?>"
	name="<?php echo $realelementtemp; ?>-elementvalue[]"
	value="<?php echo $currentattributevalue; ?>"
	class="<?php echo $class; ?>" />
						<?php


						$idarray[] = $realelementtemp."-elementvalue".$i;
						break;
							
case "select":
	?>
<select name="<?php echo $realelementtemp ?>-elementvalue[]"
	id="<?php echo $realelementtemp; ?>-elementvalue<?php echo $i; ?>"
	style="width: 100px;">
	<option value="">--- Please Select ---</option>
	<?php foreach($attributeoptions as $key=>$value){ ?>
	<option value="<?php echo $key; ?>"
	<?php if(array_search($key,$currentattributevalue) !== false){  ?>
		selected="selected" <?php } ?>><?php echo utf8_decode($value);  ?></option>
		<?php } ?>
</select>
		<?php


		$idarray[] = $realelementtemp."-elementvalue".$i;
		break;

				}
					
			}

		}
		?>

		<?php
		$pagecontent=ob_get_contents();
		ob_end_clean();
		echo rawurlencode($pagecontent);
		echo "');
        
        ";
		if($type == "calendar"){
			?>
parent.Calendar.setup({ inputField: "<?php echo $repIndex == -1 ? ($realelement."")
			: ($realelement."-".$repIndex.""); ?>-elementvalue", ifFormat: "%d/%m/%Y", showsTime: false, button:
"calzone-trigger", align: "Bl", singleClick : true });

			<?php

		}

		foreach($idarray as $element){
			?>
parent.idarray.push("<?php echo $element; ?>");
			<?php

		}

		echo "</script>";

		// return save / cancel buttons.

		if ($repIndex == -1) {

			echo "<script type='text/javascript'>parent.document.getElementById('".$realelement."-buttons').innerHTML = unescape('";
		}
		else {
			echo "<script type='text/javascript'>parent.document.getElementById('".$realelement."-".$repIndex."-buttons').innerHTML = unescape('";
				
		}

		ob_start();
		?>
<button class="scalable" type="button"
	onclick="saveEntryFormWithIndex(<?php echo $repIndex; ?>);"
	name="submit" value="Save"><span>Save</span></button>
<button class="scalable back" type="button"
	onclick="cancelUpdateWithIndex(<?php echo $repIndex; ?>);"
	name="submit" value="Cancel"><span>Cancel</span></button>
		<?php
		$pagecontent = ob_get_contents();
		ob_end_clean();
		echo rawurlencode($pagecontent);
		echo "');</script>";

	}

	public function getManualsetupAttribute($xmlpath,$productid){

		$ismapped = false;
		$model = "";
		$mktplace = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');
		$val = Mage::getModel('amazonimportmanualsetup'.$mktplace.'/amazonimportmanualsetup'.$mktplace)->getCollection()->addFieldToFilter('xmlkey',array('eq'=>$xmlpath))->addFieldToFilter('productid',array('eq'=>$productid));
		foreach($val as $model){
			break;
		}

		if(is_object($model)){
			return $model;
		}else{
			return false;
		}
	}

	public function getMappedDataFromProduct($attribute,$productid){
			
			
		if(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace') == "uk"){
			$store = Mage::getStoreConfig('amazonint/amazonuk/store');
		}
		if(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace') == "fr"){
			$store = Mage::getStoreConfig('amazonint/amazonfr/store');
		}
		if(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace') == "de"){
			$store = Mage::getStoreConfig('amazonint/amazonde/store');
		}
		if(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace') == "com"){
			$store = Mage::getStoreConfig('amazonint/amazoncom/store');
		}
			
		$product = Mage::getModel('catalog/product')->setStoreId($store)->load($productid);
			
		if($product->getData($attribute) != ""){
			$mval = $product->getAttributeText("".$attribute."");
			if(is_array($mval)){
				$mval = implode("",$mval);
			}
			if($mval == ""){
				$mval = $product->getData($attribute);
			}

		}else{
			$mval = "";
		}

		return $mval;
			
	}

	public function getManualsetupChildren($xmlpath,$productid){

			
		$xmlpath = $xmlpath."/Attributes/%";
		$ismapped = false;
		$model = "";
		$mktplace = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');
		$val = Mage::getModel('amazonimportmanualsetup'.$mktplace.'/amazonimportmanualsetup'.$mktplace)->getCollection()->addFieldToFilter('xmlkey',array('like'=>$xmlpath))->addFieldToFilter('productid',array('eq'=>$productid));
		foreach($val as $model){
			break;
		}

		if(is_object($model)){
			return $model;
		}else{
			return false;
		}

	}


	public function getMappedValue($xmlpath){
			
		$ismapped = false;
		$model = "";
		$mktplace = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');
		$val = Mage::getModel('amazonimportmapping'.$mktplace.'/amazonimportmapping'.$mktplace)->getCollection()->addFieldToFilter('xmlkey',array('eq'=>$xmlpath));
		foreach($val as $model){
			break;
		}

		if(is_object($model)){
			return $model;
		}else{
			return false;
		}

			
	}

	public function savevalueAction()
	{
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace'),'camiloo_amazon_manualsetup_marketplace');
		$productid = $this->getRequest()->getParam('id');

		$index = $_POST['saveentry_element_index'];

		$productids = explode(",",$productid);
		if(!is_array($productids)){
			$productids = array($productid);
		}

		unset($_POST['form_key']);
		$newarray = array();
		$posted = unserialize($_POST['serialized_values']);

		foreach($posted as $key=>$value){
			$key = explode("-elementvalue",$key);
			$key = $key[0];
			$newarray[$key][] = $value;
		}
		


		foreach($newarray as $key=>$posted)
		{
			if(is_array($posted)){
				foreach($posted as $pkey=>$poster){
					if($poster == ""){
						unset($posted[$pkey]);
					}else{
	                   	if(is_array($posted[$pkey])){
							$sepChar = "|";
							$posted[$pkey] = implode($sepChar,$posted[$pkey]);
						}
					}
				}
				$sepChar = "|";
				$posted = implode($sepChar,$posted);
			}
				
				

			if(!isset($firstkey)){
				$firstkey = $key;
			}

			foreach($productids as $productid)
			{


				// May need to convert ELEM-1 to ELEM[1]
				if (strpos($key, "-") !== false) {
					$parts = explode("-", $key);
						
					$key = $parts[0].'['.$parts[1].']';
				}

				$model = Mage::getModel('amazonimportmanualsetup'.
				Mage::getModel('amazonimport/amazonimport')->loadSessionValue(
						'camiloo_amazon_manualsetup_marketplace').
					'/amazonimportmanualsetup'.Mage::getModel('amazonimport/amazonimport')
				->loadSessionValue('camiloo_amazon_manualsetup_marketplace'))
				->getCollection()
				->addFieldToFilter('productid',array('eq' => $productid))
				->addFieldToFilter('xmlkey',array('eq' => $key));

					
				if(sizeof($model) > 0){
					foreach($model as $mdl){
						break;
					}
					if($posted == ""){
						$mdl->delete();
					}

				}else{
					$mdl = Mage::getModel('amazonimportmanualsetup'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace').'/amazonimportmanualsetup'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace'));
					$mdl->setData('xmlkey',$key);
					$mdl->setData('productid',$productid);
				}
				if($posted != ""){
					$mdl->setData('manualsetupvalue',$posted);
					$mdl->save();
				}
			}
		}

		// saving complete - we now need to restore the original field so further tweaking could potentially take place.
		echo "<script type='text/javascript'>parent.document.getElementById('".$firstkey."-values').innerHTML = unescape('";
		ob_start();
		if(sizeof($productids) < 2){
			$_firstkey = $firstkey;
			if (strpos($firstkey, "-") !== false) {
				$parts = explode("-", $firstkey);

				$_firstkey = $parts[0].'['.$parts[1].']';
			}

			$model = $this->getMappedValue($_firstkey, $productid);
			$manualdata = $this->getManualsetupAttribute($_firstkey, $productid);

			if(is_object($manualdata)){

				$mdata = $manualdata->getManualsetupvalue();

				$manualdatachildren = $this->getManualsetupChildren($_firstkey, $productid);
				if(is_object($manualdatachildren)){
					$mdata .= " ".$manualdatachildren->getManualsetupvalue();
				}

				?>

<b>Manually set to:</b>
<br />
				<?php echo str_replace("|",", ",strip_tags($mdata)); ?>

				<?php
				$valuetosend = strip_tags(rawurlencode($mdata));
				$overridden = true;

			}else{
				if((is_object($model))&&($model->getMappingvalue() != "")){
					$mval = $this->getMappedDataFromProduct($model->getMappingvalue(), $productid);
					?>
<b>Mapped to Product Value:</b>
<br />
<span class='mappeddisplay'><?php echo strip_tags($mval); ?></span>
					<?php
					$valuetosend = strip_tags(rawurlencode($mval));
					$overridden = false;
				}else{
					?>
Not set
					<?php
					$valuetosend = "";
					$overridden = false;
				}
			}

		}else{
			?>
Bulk Changes Saved.
			<?php
		}

		$pagecontent = ob_get_contents();
		ob_end_clean();
		echo rawurlencode($pagecontent);
		echo "');</script>";
			
			
		echo "<script type='text/javascript'>parent.document.getElementById('".$firstkey."-buttons').innerHTML = unescape('";
		ob_start();

		$firstkey = strpos($firstkey, "-") !== false ? substr($firstkey, 0, strpos($firstkey, "-")) : $firstkey;

			
			
		if(sizeof($productids) < 2){
			if((is_object($model))&&($overridden == false)&&($model->getMappingvalue() != "")){



				?>

<button class="scalable" type="button"
	onclick="displayEntryFormWithIndex('<?php echo $firstkey; ?>','<?php echo $valuetosend; ?>', <?php echo $index; ?>);"
	name="submit" value="Override Mapping"><span>Bulk Override Mapping</span></button>
</div>
				<?php

			}else{
				?>
<button class="scalable" type="button"
	onclick="displayEntryFormWithIndex('<?php echo $firstkey; ?>','<?php echo $valuetosend; ?>', <?php echo $index; ?>);"
	name="submit" value="Edit"><span>Edit</span></button>
</div>
				<?php
			}
		}else{
			?>
<button class="scalable" type="button"
	onclick="displayEntryFormWithIndex('<?php echo $firstkey; ?>','', <?php echo $index; ?>);"
	name="submit" value="Edit"><span>Bulk Edit</span></button>
</div>
			<?php
		}

		$pagecontent=ob_get_contents();
		ob_end_clean();
		echo rawurlencode($pagecontent);
		echo "');</script>";
	}

	public function saveAction(){

		$complete = $_POST['has_setup_completed'];
		$productid = $_POST['productid'];

		$productids = explode(",",$productid);
		if(!is_array($productids)){
			$productids = array($productid);
		}

		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();

		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');

		foreach($productids as $productid){
			// save the data
			$model = Mage::getModel('amazonimportsetup'.$country.'/amazonimportsetup'.$country)->getCollection()->addFieldToFilter('productid',array('eq'=>$productid));
			foreach($model as $mdl){
				break;
			}

			if($complete){
			$result = $db->query("UPDATE {$table_prefix}amazonimport_errorlog_".$country." set result='', result_description='', messageid=0 WHERE productid='".$productid."' AND result != ''");
			}

			if(is_object($mdl)){
				$mdl->setData('initial_setup_complete',$complete);
				$mdl->save();
			}else{
				// this product was not in the setup table for some reason? Lets add it in.
				$model = Mage::getModel('amazonimportsetup'.$country.'/amazonimportsetup'.$country);
				$model->setData('initial_setup_complete',$complete);
				$model->setData('productid',$productid);
				$model->setData('setup_type','manual');
				$model->save();
			}

			$result = $db->query("select * from {$table_prefix}amazonimport_setup_".$country." WHERE initial_setup_complete = 1 AND productid=".$productid." AND productid not in (select productid from {$table_prefix}amazonimport_errorlog_".$country." where productid=".$productid." AND submission_type='Product')");
			while($row = $result->fetch(PDO::FETCH_ASSOC)){
				
				$db->query("REPLACE INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$productid.",'Product')");
				$db->query("REPLACE INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$productid.",'Image')");
				// is product configurable?
				if(Mage::getModel('catalog/product')->load($productid)->getTypeId() != "simple"){
					$db->query("REPLACE INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$productid.",'Relation')");
				}
				$db->query("REPLACE INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$productid.",'Stock')");
				$db->query("REPLACE INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$productid.",'Price')");
					
			}
		}

		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__('Setup Flag Saved'));
		// return user to gridview.
		$this->_redirect('*/*/');

	}


	public function microtime_float(){
		list ($msec, $sec) = explode(' ', microtime());
		$microtime = (float)$msec + (float)$sec;
		return $microtime;
	}
	
	public function massmarkAction(){
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');

		$setup = $this->getRequest()->getParam('setup');
		$products = $_POST['product'];
		foreach ($products as $product) {
			if ($setup == 0) {
				$db->query("DELETE FROM {$table_prefix}amazonimport_errorlog_".$country." WHERE productid=$product");
			} else {
				$db->query("REPLACE INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$product.",'Product')");
				$db->query("REPLACE INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$product.",'Image')");	
				$db->query("REPLACE INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$product.",'Relation')");
			}
			$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_setup_".$country." WHERE productid=".$product);
            $hasdata = 0;
            foreach ($result as $row) {
                $hasdata = 1;
                break;
            }
            
            if ($hasdata == 1) {
                $db->query("UPDATE {$table_prefix}amazonimport_setup_".$country." SET initial_setup_complete = $setup WHERE productid=".$product);
            } else {
                $db->query("INSERT INTO {$table_prefix}amazonimport_setup_".$country." (`initial_setup_complete`,`productid`,`setup_type`,`asincode`) VALUES ($setup,$product,'manual','');");
            }
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__(sizeof($products)." product settings have been updated"));
		$this->_redirect('*/*/');
	}
	
	
	
	public function massresubmitAction(){
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');
		$products = $_POST['product'];
		foreach($products as $product){
			$db->query("DELETE FROM {$table_prefix}amazonimport_errorlog_".$country." WHERE productid=$product");
			
			$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$product.",'Product')");
			
			$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$product.",'Image')");
			
			$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$product.",'Relation')");
		}
			
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__(sizeof($products)." products will be resubmitted to Amazon shortly"));
		$this->_redirect('*/*/');
	}

}

?>