<?php

class Camiloo_Amazonimport_Block_Mapping_Edit_Tab_Defaults extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('amazonimport/mappings/defaults.phtml');
	}

	public function from_camel_case($string) {
    	$output = "";
        foreach( str_split( $string ) as $char ) {
        	strtoupper( $char ) == $char and $output and $output .= " ";
 			$output .= $char;
		}
		return $output;
	}


	public function convert_xls_format($string) {
    	$output = "";
        foreach( str_split( $string ) as $char ) {
        	strtoupper( $char ) == $char and $output and $output .= "-";
 			$output .= $char;
		}
		return strtolower($output);
	}

	public function getMappedValue($xmlpath, $index = -1)
	{
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

	public function outputChildren($currentlocation, $node, $index = -1)
	{
		$elements = $node;
		
		$debugString = '';
		$amzcore = Mage::getModel('amazonimport/amazonimport');
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace');
  			 
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
				
			$debugString .= "\n".$nextlocation;
			 
			// check level two children, then check if its a CamOption element.
			if (count($producttype->children()) > 0
				&& "".$producttype->children()->getName() != "CamOption"
				&& "".$producttype->children()->getName() != "Attributes"
				&& $producttype->getName() != "Condition")
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
				{ ?>
<div class="entry-edit">
<div class="entry-edit-head collapseable">
<a id="<?php echo $nextlocation ?>-head" href="#" onclick="Fieldset.toggleCollapse('<?php echo $nextlocation ?>'); return false;" class="closed"> 
<?php echo ucwords($this->from_camel_case("".$producttype->getName())) ?>
</a>
</div>

<input id="<?php echo $nextlocation ?>-state" name="config_state[<?php echo $nextlocation ?>]" type="hidden" value="0">
<fieldset class="config collapseable" id="<?php echo $nextlocation ?>">
				<?php $this->outputChildren($nextlocation, $producttype->children()); ?>
</fieldset>
<script type="text/javascript">Fieldset.applyCollapse('<?php echo $nextlocation ?>')</script>
</div>
				<?php
				
				}
			}
			else{
				if((strpos((string) $producttype['CamType'],"tring") > 0
					||strpos((string) $producttype['CamType'],"KUType") > 0
					||strpos((string) $producttype['CamType'],"nteger") > 0
					||strpos((string) $producttype['CamType'],"ecimal") > 0)
					&&($producttype->getName() != "Condition"))
				{
						$model = $this->getMappedValue($nextlocation, $index);
						?>
						<?php if(($producttype->getName() != "RecommendedBrowseNode")&&($producttype->getName() != "ItemType")){ ?>
<table cellspacing="0" class="form-list">
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
					
					if("".$producttype->children()->getName() != "CamOption"){
					$fielddesc = "<b>Definition And Use:</b><br/>".$row['value']."<br/><br/><b>Accepted Values:</b><br/>".$row['accepted_values']."<br/><br/><b>Example Value:</b>".$row['example'];
					}else{
						
						$fielddesc = "<b>Definition And Use:</b><br/>".$row['value']."<br/><br/><b>Accepted Values:</b><br/>";
						$fielddesc .= "Mapped value must be one of values shown<br/><br/><b>Example Value:</b>".$row['example'];
						
					}
					
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
        
            <td class="label amzhelp" <?php echo $fielddesc; ?>><?php 
			
			
			echo ucwords($this->from_camel_case("".$producttype->getName().""));

			?><br /><span style="font-size:10px;"><?php echo $required; ?></span>
            </td>
			<td class="value"><?php 
			if(is_object($model))
			{
				if ($index == -1)
				{
					echo "<div id='".$nextlocation."-values'>Mapped to attribute with code ".$model->getMappingvalue().'</div>';
				}
				else
				{
					echo "<div id='".$nextlocation."-".$index."-values'>Mapped to attribute with code ".$model->getMappingvalue().'</div>';
				}
				
				$valuetosend = $model->getMappingvalue();
			}
			else
			{
				if ($index == -1)
				{
					echo "<div id='".$nextlocation."-values'>Not mapped</div>";
				}
				else
				{
					echo "<div id='".$nextlocation."-".$index."-values'>Not mapped</div>";
				}
				$valuetosend = "";
			}

			if(isset($producttype['CamElementRepeatLimit'])){
				echo "<br /><small>Can contain up to ".$producttype['CamElementRepeatLimit']." values separated by | marks.</small><br />";
			}
			
	
			if("".$producttype->children()->getName() == "CamOption"){
				echo "<small><b>The attribute you map this field onto must contain one of a strict set of values. <a href='#' onclick=\"$('showhideoptions".$nextlocation."-".$index."').toggle();\">Show / Hide Values.</a></b><br/>
				<div id='showhideoptions".$nextlocation."-".$index."' style='display:none;'>";
				foreach($producttype->children() as $child){
					echo $child->children().", ";	
				}
				echo "</div></small>";
			}
			
			
			unset($ismapped);
			?></td>
			<td class="scope-label"></td>
			<td class="value">
			<?php 
			
				if ($index == -1)
				{
					echo "<div id='".$nextlocation."-buttons'>";
				}
				else
				{
					echo "<div id='".$nextlocation."-".$index."-buttons'>";
				}
			
			?>
			<button class="scalable" type="button"
				onclick="displaySelectFormWithIndex('<?php echo $nextlocation; ?>','<?php echo $valuetosend; ?>', <?php echo $index; ?>);"
				name="submit" value="Select Attribute"><span>Select Attribute</span></button>
			<button class="scalable" type="button"
				onclick="displayEnterCodeFormWithIndex('<?php echo $nextlocation; ?>','<?php echo $valuetosend; ?>', <?php echo $index; ?>);"
				name="submit" value="Enter Attribute Code"><span>Enter Attribute Code</span></button>
			</div>
			</td>
		</tr>
	</tbody>
</table>
			<?php
						}
					}
				
			}
			 
		} // End of foreach
		 
		 
	}


}

?>