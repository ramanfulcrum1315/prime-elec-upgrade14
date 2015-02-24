<?php

class Camiloo_Amazonimport_Block_Manualsetup_Edit_Tab_Defaults extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
  	parent::__construct();  
	$this->setTemplate('amazonimport/manualsetup/defaults.phtml');
  }
  
  public function from_camel_case($string) {
      $output = "";
      foreach( str_split( $string ) as $char ) {
          strtoupper( $char ) == $char and $output and $output .= " ";
 		  $output .= $char;
	  }
	  return $output;
  }
  
  public function getMappedValue($xmlpath, $index = -1){
  
		$ismapped = false;
		$model = "";
		$mktplace = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_mapping_marketplace');
		
		if ($index == -1) {
		
			$val = Mage::getModel('amazonimportmapping'.$mktplace.'/amazonimportmapping'.$mktplace)
				->getCollection()->addFieldToFilter('xmlkey',array('eq'=>$xmlpath));
		
		}
		else {
			
			$val = Mage::getModel('amazonimportmapping'.$mktplace.'/amazonimportmapping'.$mktplace)
				->getCollection()->addFieldToFilter('xmlkey',array('eq'=>  ($xmlpath.'['.$index.']') ));
		}
		
		foreach($val as $model){
			break;
		}

		if (is_object($model))
		{
			return $model;
		}else{
			return false;
		}
  }
  
   
  public function getManualsetupValue($xmlpath, $index = -1){

  		$ismapped = false;
  		$model = "";
  		$mktplace = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');
  		
  		if($index == -1)
  		{
  		
	  		$val = Mage::getModel('amazonimportmanualsetup'.$mktplace.'/amazonimportmanualsetup'.$mktplace)
	  			->getCollection()->addFieldToFilter('xmlkey',array('eq'=>$xmlpath));
	  	}
  		else {
  			$val = Mage::getModel('amazonimportmanualsetup'.$mktplace.'/amazonimportmanualsetup'.$mktplace)
  				->getCollection()->addFieldToFilter('xmlkey',array('eq'=>($xmlpath.'['.$index.']')));
  		
  			
  		}
  		
  		foreach($val as $model){
  			break;	
  		}

  		if(is_object($model)){
  			return $model;
  		}else{
  			return false;
  		}
  }
  
  public function getManualsetupAttribute($xmlpath, $index = -1){

  		$ismapped = false;
  		$model = "";
		
		if(strpos($_SERVER['REQUEST_URI'],"reviewerr") > 0){
			$pid = $this->getProductid();
		}else{
			$pid = $this->getRequest()->getParam('id');
		}
		
  		$mktplace = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');
  		
  		if ($index == -1)
  		{
	  		$val = Mage::getModel('amazonimportmanualsetup'.$mktplace.'/amazonimportmanualsetup'.$mktplace)
	  			->getCollection()->addFieldToFilter('xmlkey',array('eq'=>$xmlpath))->addFieldToFilter('productid',array('eq'=>$pid));
  		}
  		else {
  			$val = Mage::getModel('amazonimportmanualsetup'.$mktplace.'/amazonimportmanualsetup'.$mktplace)
  				->getCollection()->addFieldToFilter('xmlkey',array('eq'=>($xmlpath.'['.$index.']')))->addFieldToFilter('productid',array('eq'=>$pid));
  		}
  		
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
	    $product->getResource()->getAttribute($attribute)->setStoreId($store);

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
  
  
	public function convert_xls_format($string) {
    	$output = "";
        foreach( str_split( $string ) as $char ) {
        	strtoupper( $char ) == $char and $output and $output .= "-";
 			$output .= $char;
		}
		return strtolower($output);
	}

  
  public function getManualsetupChildren($xmlpath, $index = -1)
  {
  		if ($index == -1) {
  			$xmlpath = $xmlpath."/Attributes/%";
  		}
  		else {
  			$xmlpath = $xmlpath."[$index]/Attributes/%";
  		}
  		
  		$ismapped = false;
  		$model = "";
		
		if(strpos($_SERVER['REQUEST_URI'],"reviewerr") > 0){
			$pid = $this->getProductid();
		}else{
			$pid = $this->getRequest()->getParam('id');	
		}

		
  		$mktplace = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');
  		$val = Mage::getModel('amazonimportmanualsetup'.$mktplace.'/amazonimportmanualsetup'.$mktplace)
  			->getCollection()->addFieldToFilter('xmlkey',array('like'=>$xmlpath))->addFieldToFilter('productid',array('eq'=>$pid));
  		
  		foreach($val as $model)
  		{
  			break;	
  		}

  		if(is_object($model)){
  			return $model;
  		}else{
  			return false;
  		}
  }
  
  public function outputChildren($currentlocation, $node, $index = -1){
  	
       	    $elements = $node;
  	    	$amzcore = Mage::getModel('amazonimport/amazonimport');
			$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');
  			/* we have been passed the productdatatype, lets go back and get the producttype */
			$temp = explode("/",$this->getData('producttype'));
			$passedptype = $temp[sizeof($temp)-1];
			
			
		
	         foreach($elements as $producttype)
	         {
				$nextlocation = $currentlocation."/".$producttype->getName();
	         	if(strpos($currentlocation,"ProductData")){
					$temp = explode("/",$currentlocation);
					$ptype = $temp[2];
					$catlimited = $amzcore->convertCategoryNameToProductDataType($ptype,$country,true);
	         	}else{
					$catlimited = false;
				}
				
	         	
	  			// check level two children, then check if its a CamOption element.
	  			if((count($producttype->children()) >0)
	  				&&("".$producttype->children()->getName()."" != "CamOption")
	  				&&("".$producttype->children()->getName()."" != "Attributes")
	  				&&("".$producttype->getName()."" != "Condition"))
	  			{
	  				
	  				if (isset($producttype['CamElementRepeatLimit']))
					{
						$repeatMax = $producttype['CamElementRepeatLimit'];
						for ($i = 1; $i <= $repeatMax; $i++) {
							
							$elemIdModified = $nextlocation."-".$i;
							
							
				?>
<div class="entry-edit">
<div class="entry-edit-head collapseable">
<a id="<?php echo $elemIdModified ?>-head" href="#" onclick="Fieldset.toggleCollapse('<?php echo $elemIdModified ?>'); return false;" class="closed"> 
<?php echo ucwords($this->from_camel_case("".$producttype->getName()))." ($i of $repeatMax)"; ?>
</a>
</div>

<input id="<?php echo $elemIdModified ?>-state" name="config_state[<?php echo $elemIdModified ?>]" type="hidden" value="0">
<fieldset class="config collapseable" id="<?php echo $elemIdModified ?>">
				<?php $this->outputChildren($nextlocation, $producttype->children(), $i); ?>
</fieldset>
<script type="text/javascript">Fieldset.applyCollapse('<?php echo $elemIdModified ?>')</script>
</div>
				<?php
							
						}
					}
	  				else
	  				{
	  			?>
	      <div class="entry-edit">
			    <div class="entry-edit-head collapseable">
			    	<a id="<?php echo $nextlocation; ?>-head" href="#" onclick="Fieldset.toggleCollapse('<?php echo $nextlocation; ?>'); return false;" class="closed">
						<?php echo ucwords($this->from_camel_case("".$producttype->getName()."")) ?>
					</a>
				</div>
				<input id="<?php echo $nextlocation; ?>-state" name="config_state[<?php echo $nextlocation; ?>]" type="hidden" value="0">
				 <fieldset class="config collapseable" id="<?php echo $nextlocation; ?>">
				        <?php $this->outputChildren($nextlocation, $producttype->children()); ?>
				 </fieldset>
	 			<script type="text/javascript">Fieldset.applyCollapse('<?php echo $nextlocation; ?>')</script>
		</div>
	        
	        <?php 
	  				}
	  			}else{
			
	  					
	  				$model = $this->getMappedValue($nextlocation, $index);
	  				
	  				if(($producttype->getName() != "RecommendedBrowseNode")
	  				&&($producttype->getName() != "ItemType")
	  				&&($producttype->getName() != "Parentage")
	  				&&($producttype->getName() != "Condition")){ ?>
  
            <table cellspacing="0" class="form-list odd">
            <tbody>
		<?php
				if($producttype->getName() == "SKU"){
					$name = "sku";	
				}else if($producttype->getName() == "MSRP"){
					$name = "msrp";	
				}else if($producttype->getName() == "CPSIA"){
					$name = "cpsia";	
				}else if($producttype->getName() == "FEDAS_ID"){
					$name = "fedas";	
				}else{
					$name = $producttype->getName();
				}
				$fielddesckey = $this->convert_xls_format($name);
				$db = Mage::getSingleton("core/resource")->getConnection("core_write");
				$table_prefix = Mage::getConfig()->getTablePrefix();
				$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
				if($catlimited){
				$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_fielddescriptions WHERE country_id='$country' AND (fieldname LIKE '%$fielddesckey%' OR  fieldname LIKE '%$name%') AND category_name LIKE '%$catlimited%'");
				}
				
				$row = array();
				
				if($catlimited){
					$row = $result->fetch(PDO::FETCH_ASSOC);
				}
				if(isset($row['value'])){
					$fielddesc = "<b>Definition And Use:</b><br/>".$row['value']."<br/><br/><b>Accepted Values:</b><br/>".$row['accepted_values']."<br/><br/><b>Example Value:</b>".$row['example'];
					$fielddesc = 'title="'.rawurlencode($fielddesc).'"';
					$required = $row['is_required'];
				}else{
					// try the new fallover array in amzcore
					$fallOverArray = $amzcore->getFallOverArray();
					if(isset($fallOverArray[''.$producttype->getName().''])){
						$fielddesc = 'title="'.rawurlencode($fallOverArray[''.$producttype->getName().'']).'"';
					}else{
						$fielddesc = "";	
					}
					$required = "";
				}
				if($required == "Required"){
					$required = "<b>Required</b>";	
				}
	
				
				
			?>
		<tr>
        
            <td class="label amzhelp" <?php echo $fielddesc; ?>>
			<?php echo ucwords($this->from_camel_case("".$producttype->getName().""));	?>
            <br /><span style="font-size:10px;"><?php echo $required; ?></span>
            </td>
    			<td class="value"><?php 
    			
  				if(!isset($_POST['product'])){
				
    				$manualdata = $this->getManualsetupAttribute($nextlocation, $index);
    				if(is_object($manualdata)){
    					
    					$mdata = $manualdata->getManualsetupvalue();
    					
    					$manualdatachildren = $this->getManualsetupChildren($nextlocation, $index);
    					if(is_object($manualdatachildren)){
    						$mdata .= " ".$manualdatachildren->getManualsetupvalue();
    					}
    					
    					$sepChar = "|";
    					
    					if ($index == -1) {
    						echo "<div id='".$nextlocation."-values'><b>Manually set to:</b><br />".str_replace($sepChar,", ",strip_tags($mdata))."</div>";
    					}
    					else {
    						echo "<div id='".$nextlocation."-".$index."-values'><b>Manually set to:</b><br />".str_replace($sepChar,", ",strip_tags($mdata))."</div>";
    					}
    					
    					$valuetosend = strip_tags(rawurlencode($mdata));
    					$overridden = true;	
    				}else{
    					if((is_object($model))&&($model->getMappingvalue() != "")){
							
							if(strpos($_SERVER['REQUEST_URI'],"reviewerr") > 0){
								$pid = $this->getProductid();
							}else{
								$pid = $this->getRequest()->getParam('id');	
							}
							
							
    						$mval = $this->getMappedDataFromProduct($model->getMappingvalue(), $pid);
    						
    						if ($index == -1)
    						{
    							echo "<div id='".$nextlocation."-values'><b>Mapped to Product Value:</b><br /><span class='mappeddisplay'>".strip_tags($mval)."</span></div>";	
    						}
    						else
    						{
    							echo "<div id='".$nextlocation."-".$index."-values'><b>Mapped to Product Value:</b><br /><span class='mappeddisplay'>".strip_tags($mval)."</span></div>";
    						}
    						$valuetosend = strip_tags(rawurlencode($mval));
    						$overridden = false;
  						}else{
  							
  							if ($index == -1)
  							{
								echo "<div id='".$nextlocation."-values'>Not set</div>";
  							}
  							else
  							{
  								echo "<div id='".$nextlocation."-".$index."-values'>Not set</div>";
  							}
    						$valuetosend = "";	
    						$overridden = false;	
  						}
    				}
    				
    			
  				}
  				else
  				{
  					if ($index == -1) {
						echo "<div id='".$nextlocation."-values'>Click 'Bulk Edit' to change.</div>";
  					}
  					else {
						echo "<div id='".$nextlocation."-".$index."-values'>Click 'Bulk Edit' to change.</div>";
  					}
  				}
    			
    			?></td>
 				<td class="scope-label"></td>
       			<td class="value"><?php
				if(!isset($_POST['product']))
				{
					if((is_object($model))
						&&($overridden == false)
						&&($model->getMappingvalue() != ""))
					{
						if ($index == -1) {
       						echo "<div id='".$nextlocation."-buttons'>";
						}
						else {
							echo "<div id='".$nextlocation."-".$index."-buttons'>";
						}
       					
       					?>
       					<button class="scalable" type="button" 
       						onclick="displayEntryFormWithIndex('<?php echo $nextlocation; ?>','<?php echo $valuetosend; ?>', <?php echo $index; ?>);" 
       						name="submit" value="Override Mapping"><span>Override Mapping</span>
       						</button></div>
		       		<?php
       				}
       				else
       				{
       					if ($index == -1) {
       						echo "<div id='".$nextlocation."-buttons'>";
						}
						else {
							echo "<div id='".$nextlocation."-".$index."-buttons'>";
						}
						
		       			?>
		       			<button class="scalable" type="button" 
		       			onclick="displayEntryFormWithIndex('<?php echo $nextlocation; ?>','<?php echo $valuetosend; ?>', <?php echo $index; ?>);" 
		       			name="submit" value="Edit"><span>Edit</span>
		       			</button></div>
		      <?php }
		       		
				}
				else
				{
					if ($index == -1) {
       					echo "<div id='".$nextlocation."-buttons'>";
					}
					else {
						echo "<div id='".$nextlocation."-".$index."-buttons'>";
					}
					
					?><button class="scalable" type="button"
					 onclick="displayEntryFormWithIndex('<?php echo $nextlocation; ?>','', <?php echo $index; ?>);" name="submit"
					  value="Bulk Edit"><span>Bulk Edit</span></button></div>
		       		<?php 
				}
		       		?>
		       						
		       						</td>
			</tr>
            </tbody>
        </table>
				        		<?php 
	  				}
	  			}
	        
			}
  	
  	
  }
  
  
}

?>