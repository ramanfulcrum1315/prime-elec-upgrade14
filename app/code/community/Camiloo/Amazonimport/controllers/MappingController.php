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

class Camiloo_Amazonimport_MappingController extends Mage_Adminhtml_Controller_Action
{
	private $attributeName;
	private $attributeValue;

	protected function _initAction() {
		$this->loadLayout();	
		return $this;
	}   
	
	public function indexAction() {
		$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);
		
		$this->loadLayout();
		if($iview == true) {
			$this->_addContent($this->getLayout()->createBlock('amazonimport/mapping_edit'));
			$this->_addLeft($this->getLayout()->createBlock('amazonimport/mapping_edit_tabs'));
		}
		$this->renderLayout();
	}
			
	public function comAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("com",'camiloo_amazon_mapping_marketplace');
		$this->indexAction();		
	}
	
	public function ukAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("uk",'camiloo_amazon_mapping_marketplace');
		$this->indexAction();		
	}
	
	public function frAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("fr",'camiloo_amazon_mapping_marketplace');
		$this->indexAction();		
	}
	
	public function deAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("de",'camiloo_amazon_mapping_marketplace');
		$this->indexAction();		
	}
	
	
	private function findNode($xml, $pathToFind, $currentPath) {
		
		if ($currentPath == $pathToFind)
		{
			// Get attributes if exist
			
			$this->attributeName = '';
			$this->attributeValue = '';
			foreach($xml->children() as $childnode) {
				if ($childnode->getName() == "Attributes") {
					
					foreach($childnode->children() as $grandchildnode) {
					
						$this->attributeName = $grandchildnode->getName();
						
						foreach($grandchildnode->children() as $ggc)
						{
							if($ggc->getName() == "CamOption") {
								
								$sepChar = "|";
								$this->attributeValue .= ((string)$ggc->Value).$sepChar;
							
							}
						}
						break;
					}
				}
				break;
			}
			
			
			return true; // Exit recursion
		}
		
		foreach($xml->children() as $childnode) {

			$nextlocation = $currentPath."/".$childnode->getName();
			
			$result = $this->findNode($childnode, $pathToFind, $nextlocation);
			
			if ($result == true) {
				return true;
			}
		}
		return false;
	}
	
    private function FormatCamelCase( $string ) {
                $output = "";
                foreach( str_split( $string ) as $char ) {
                        strtoupper( $char ) == $char and $output and $output .= " ";
                        $output .= $char;
                }
                return $output;
    }
    
    private function getAttributeHtml(&$attribout, $element, $country, $repIndex = -1) {
    	// =======
		
		// If there is an attribute, allow selection and write to the MANUAL database table
		// as attributes are always manually setup, never mapped - BUT applies to ALL product IDs
		
		$xml = Mage::getModel('amazonimport/amazonimport')->getTemplateXml(
		    $country);
		$this->attributeName = '';
		$this->attributeValue = '';
		
		
		// find the node e.g. 'Product/DescriptionData/PackageWeight' (element)
		if($this->findNode($xml, $element, "Product")) {
			// There is an attribute, output this
			
			if(strlen($this->attributeName) > 0 && strlen($this->attributeValue) > 0) {
				
				if ($repIndex == -1) {
					$model = Mage::getModel('amazonimportmanualsetup'.$country.'/amazonimportmanualsetup'.$country)->getCollection()
			  				->addFieldToFilter('productid',array('eq' => -1))
			  				->addFieldToFilter('xmlkey', $element.'/Attributes/'.$this->attributeName);
		  				
				}
				else
				{
					$model = Mage::getModel('amazonimportmanualsetup'.$country.'/amazonimportmanualsetup'.$country)->getCollection()
		  				->addFieldToFilter('productid',array('eq' => -1))
		  				->addFieldToFilter('xmlkey', $element.'['.$repIndex.']/Attributes/'.$this->attributeName);
				}
		  		
  				$existAttrVal = '';
				foreach($model as $mdl)
				{
					$existAttrVal = $mdl->getData('manualsetupvalue');
				}
				
				// ============
				if ($repIndex == -1)
				{
					$attribout .= ucwords($this->FormatCamelCase($this->attributeName))." <select name='myattributeid' id='myattributeid'>";
				}
				else
				{
					$attribout .= ucwords($this->FormatCamelCase($this->attributeName))." <select name='myattributeid-".$repIndex
						."' id='myattributeid-".$repIndex."'>";
				}
				
				$attribValuesArr = explode("|", $this->attributeValue);
				
				foreach($attribValuesArr as $av) {
					if (strlen(trim($av)) > 0) {
						
						if($existAttrVal == $av) {
					
							$attribout .= "<option selected='selected' value='$av'>$av</option>";
						
						}
						else
						{
							$attribout .= "<option value='$av'>$av</option>";
						}
					}
				}
				
				$attribout .= '</select>';
				
				$_nameTemp = $repIndex == -1 ? "myattributename" : ("myattributename-".$repIndex);
				
				$attribout .= "<input type='hidden' name='$_nameTemp' id='$_nameTemp' value='".($this->attributeName)."'/>";
			}
		}
    }
	
	public function getattributesAction()
	{
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(
			Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_mapping_marketplace'),
			'camiloo_amazon_mapping_marketplace');
			
		$data = $this->getRequest()->getPost();
		$element = $data['getattributes-xmlkey'];
		$curval = $data['getattributes-keyvalue'];
		$repIndex = $data['getattributes-repelemindex'];
		
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_mapping_marketplace');
		
		if ($repIndex == -1)
		{
			$attribout = "<select name='".$element."-newvalue' id='".$element."-newvalue'>";
		}
		else
		{
			$attribout = "<select name='".$element."-".$repIndex."-newvalue' id='".$element."-".$repIndex."-newvalue'>";
		}
		
		$attribout .= "<option value=''>--- Not mapped ---</option>";
		
  		$collection = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter(Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId());	
		
		$attribs = array();
		$count = 0;
		foreach ($collection as $item)
		{
			if(($item->getFrontendInput() != "media_image")&&($item->getFrontendInput() != "gallery")){
				$attribout .= "<option value='".$item->getAttributeCode()."' ";
				
				if($item->getAttributeCode() == $curval){
					$attribout .= 'selected="selected"';
				}
				
				
				$attribout .= ">".$item->getFrontendLabel()." (".$item->getAttributeCode()." ".$item->getFrontendInput().")"."</option>";
			}
		}
		
		$attribout .= '</select>';
		
		// =======
		
		$this->getAttributeHtml($attribout, $element, $country, $repIndex);
		
		// =======
		
		$_valuesElemTemp = $repIndex == -1 ? ($element."-values") : ($element."-".$repIndex."-values");
		$_buttonsElemTemp = $repIndex == -1 ? ($element."-buttons") : ($element."-".$repIndex."-buttons");

		echo "<script type='text/javascript'>parent.document.getElementById('$_valuesElemTemp').innerHTML = unescape('".rawurlencode($attribout)."');</script>"; 
        echo "<script type='text/javascript'>parent.document.getElementById('$_buttonsElemTemp').innerHTML = unescape('";
        ob_start();
        ?>
        <button class="scalable" type="button" onclick="saveEnterCodeWithIndex('<?php echo $element; ?>','<?php echo $curval; ?>', <?php echo $repIndex; ?>);" name="submit" value="Save"><span>Save</span></button>
		<button class="scalable back" type="button" onclick="cancelUpdateWithIndex('<?php echo $element; ?>','<?php echo $curval; ?>', <?php echo $repIndex; ?>);" name="submit" value="Cancel"><span>Cancel</span></button>
        <?php
        $pagecontent = ob_get_contents(); 
        ob_end_clean(); 
        echo rawurlencode($pagecontent);        
        echo "');</script>";
	}
	
	public function texteditAction(){		
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_mapping_marketplace'),'camiloo_amazon_mapping_marketplace');
		$data = $this->getRequest()->getPost();
		$element = $data['textedit-xmlkey'];
		$curval = $data['textedit-keyvalue'];
		$index = $data['textedit-repelemindex'];
		
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_mapping_marketplace');
		$attribout = '';
		$this->getAttributeHtml($attribout, $element, $country, $index);
		
		if ($index == -1)
		{
			echo "<script type='text/javascript'>parent.document.getElementById('".$element."-values').innerHTML = unescape('";
			$elemNameTemp = $element."-newvalue";
		}
		else
		{
			echo "<script type='text/javascript'>parent.document.getElementById('".$element."-".$index."-values').innerHTML = unescape('";
			$elemNameTemp = $element."-".$index."-newvalue";
		}
		
        ob_start();
       	?>
			<input type="text" name="<?php echo $elemNameTemp; ?>" id="<?php echo $elemNameTemp; ?>" value="<?php echo $curval; ?>" />
			<?php echo $attribout; ?>
		<?php 
        $pagecontent = ob_get_contents();
        ob_end_clean();
        echo rawurlencode($pagecontent);
        echo "');</script>";
        
        if ($index == -1)
        {
        	echo "<script type='text/javascript'>parent.document.getElementById('".$element."-buttons').innerHTML = unescape('";
        }
        else
        {
        	echo "<script type='text/javascript'>parent.document.getElementById('".$element."-".$index."-buttons').innerHTML = unescape('";
        }
        
        ob_start();
        ?>
		<button class="scalable" type="button" onclick="saveEnterCodeWithIndex('<?php echo $element; ?>','<?php echo $curval; ?>', <?php echo $index; ?>);" name="submit" value="Save"><span>Save</span></button>
		<button class="scalable back" type="button" onclick="cancelUpdateWithIndex('<?php echo $element; ?>','<?php echo $curval; ?>', <?php echo $index; ?>);" name="submit" value="Cancel"><span>Cancel</span></button>
        <?php
        $pagecontent= ob_get_contents(); 
        ob_end_clean(); 
        echo rawurlencode($pagecontent);        
        echo "');</script>"; 
	}
	
	public function canceleditAction(){		
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_mapping_marketplace'),'camiloo_amazon_mapping_marketplace');
		$data = $this->getRequest()->getPost();
		$element = $data['canceledit-xmlkey'];
		$curval = $data['canceledit-keyvalue'];
		$index = $data['canceledit-repelemindex'];
		
		if ($index == -1)
		{
			echo "<script type='text/javascript'>parent.document.getElementById('".$element."-values').innerHTML = unescape('";
		}
		else {
			echo "<script type='text/javascript'>parent.document.getElementById('".$element."-".$index."-values').innerHTML = unescape('";
		}
		
        ob_start();
       	
        if($curval == ""){
        	?>
       		Not mapped	
        	<?php 	
        }else{
        	?>
        	Mapped to attribute with code <?php echo $curval; ?>
        	<?php 
        }
        	
        $pagecontent = ob_get_contents(); 
        ob_end_clean(); 
        echo rawurlencode($pagecontent);        
        echo "');</script>";
        
        if ($index == -1)
        {
        	echo "<script type='text/javascript'>parent.document.getElementById('".$element."-buttons').innerHTML = unescape('";
        }
        else
        {
        	echo "<script type='text/javascript'>parent.document.getElementById('".$element."-".$index."-buttons').innerHTML = unescape('";
        }
        
        ob_start();
        ?>
		<button class="scalable" type="button" onclick="displaySelectFormWithIndex('<?php echo $element; ?>','<?php echo $curval; ?>', <?php echo $index; ?>);" name="submit" value="Select Attribute">
		<span>Select Attribute</span></button>&nbsp;
		<button class="scalable" type="button" onclick="displayEnterCodeFormWithIndex('<?php echo $element; ?>','<?php echo $curval; ?>', <?php echo $index; ?>);" name="submit" value="Enter Attribute Code">
		<span>Enter Attribute Code</span></button>
		<?php
        $pagecontent = ob_get_contents();
        ob_end_clean();
        echo rawurlencode($pagecontent);
        echo "');</script>";
	}
	
	public function savemappingAction(){
		
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_mapping_marketplace'),'camiloo_amazon_mapping_marketplace');
		$data = $this->getRequest()->getPost();
		$element = $data['saveform-xmlkey'];
		$curval = $data['saveform-keyvalue'];
		$attrval = isset($data['saveform-attrvalue']) ? $data['saveform-attrvalue'] : null;
		$attrname = isset($data['saveform-attrkey']) ? $data['saveform-attrkey'] : null;
		$elementIndex = $data['saveform-repelemindex'];
		
		if($elementIndex == -1)
		{
			$indexedPath = $element.'/Attributes/'.$attrname;
			$element2 = $element;
		}
		else
		{
			$indexedPath = $element.'['.$elementIndex.']/Attributes/'.$attrname;
			$element2 = $element.'['.$elementIndex.']';
		}
		
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_mapping_marketplace');
		
		$ismapped = false;
		
		

		// data save handler.
		$collection = Mage::getModel('amazonimportmapping'.$country.'/amazonimportmapping'.$country)->getCollection()
			->addFieldToFilter('xmlkey',array('eq'=> $element2));
  		foreach($collection as $model){
			$ismapped = true;
  			break;	
  		}
  		
  		$mustDeleteAttributeValue = false;
  					
  		if ($ismapped)
  		{
			if($curval != "")
			{
  				$model->setData('mappingvalue',$curval);
  				$model->save();	
			}else{
				$model->delete();
				$mustDeleteAttributeValue = true;	
			}
  		} else {  		
  			if($curval != "")
  			{
  				$model = Mage::getModel('amazonimportmapping'.$country.'/amazonimportmapping'.$country);
  				$model->setData('xmlkey',$element2);
  				$model->setData('mappingvalue',$curval);
  				$model->save();			
  			}
  			else
  			{
  				$mustDeleteAttributeValue = true;
  			}
  		}
  		
  		if ($mustDeleteAttributeValue && $attrname != null && $attrname != "0")
  		{
  			$model = Mage::getModel('amazonimportmanualsetup'.$country.'/amazonimportmanualsetup'.$country)->getCollection()
  				->addFieldToFilter('productid',array('eq' => -1))
  				->addFieldToFilter('xmlkey', $indexedPath);
			foreach($model as $mdl)
			{
				$mdl->delete();
				break;
			}
			
  		}
  		else if ($attrname != null && $attrname != "0" && $attrval != null && $attrval != "0")
  		{
  			$model = Mage::getModel('amazonimportmanualsetup'.$country.'/amazonimportmanualsetup'.$country)->getCollection()
  				->addFieldToFilter('productid',array('eq' => -1))
  				->addFieldToFilter('xmlkey', $indexedPath);
  				
  			$_bHasUpdated = false;
			foreach($model as $mdl)
			{
				// Update existing value
				$mdl->setData('manualsetupvalue', $attrval);
  				$mdl->save();	
				
				$_bHasUpdated = true;
				break;
			}
			
			if(!$_bHasUpdated) {
				// Create a new entry
				$model = Mage::getModel('amazonimportmanualsetup'.$country.'/amazonimportmanualsetup'.$country);
				
				$model->setData('xmlkey', $indexedPath);
				$model->setData('manualsetupvalue', $attrval);
				$model->setData('mapping_override', 0);
				$model->setData('productid', -1);
  				$model->save();
			}
  		}

		// force mass update of the product data as mapping has been changed.
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		
		$result = $db->query("select * from {$table_prefix}amazonimport_setup_".$country." WHERE initial_setup_complete = 1 AND 
			productid not in (select productid from {$table_prefix}amazonimport_errorlog_".$country." where submission_type='Product')");
		while($row = $result->fetch(PDO::FETCH_ASSOC)){
			$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$row['productid'].",'Product')");
		}
		
		if ($elementIndex == -1) {
			echo "<script type='text/javascript'>parent.document.getElementById('".$element."-values').innerHTML = unescape('";
		}
		else {
			echo "<script type='text/javascript'>parent.document.getElementById('".$element."-".$elementIndex."-values').innerHTML = unescape('";
		}
		
        ob_start();

        if($curval == ""){
        	?>
       		Not mapped	
        	<?php 	
        }else{
        	?>
        	Mapped to attribute with code <?php echo $curval; ?>
        	<?php 
        }
        
        $pagecontent = ob_get_contents(); 
        ob_end_clean(); 
        echo rawurlencode($pagecontent);        
        echo "');</script>"; 
        
        if ($elementIndex == -1) {
       		echo "<script type='text/javascript'>parent.document.getElementById('".$element."-buttons').innerHTML = unescape('";
        }
        else {
        	echo "<script type='text/javascript'>parent.document.getElementById('".$element."-".$elementIndex."-buttons').innerHTML = unescape('";
        }
        
        ob_start();
        ?>
		<button class="scalable" type="button" onclick="displaySelectFormWithIndex('<?php echo $element; ?>','<?php echo $curval; ?>', <?php echo $elementIndex; ?>);" name="submit" value="Select Attribute">
		<span>Select Attribute</span></button>&nbsp;
		<button class="scalable" type="button" onclick="displayEnterCodeFormWithIndex('<?php echo $element; ?>','<?php echo $curval; ?>', <?php echo $elementIndex; ?>);" name="submit" value="Enter Attribute Code">
		<span>Enter Attribute Code</span></button>
		<?php
        $pagecontent= ob_get_contents(); 
        ob_end_clean(); 
        echo rawurlencode($pagecontent);        
        echo "');</script>";
	}
	
	
}

?>