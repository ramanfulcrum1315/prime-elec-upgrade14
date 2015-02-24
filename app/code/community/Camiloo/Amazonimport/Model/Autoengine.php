<?php

	class Camiloo_Amazonimport_Model_Autoengine extends Varien_Object
	{
		
		
		public function attribout($element=""){
  		
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
			
		// check the session doesn't perhaps have a value for us here?
		if($element != ""){
			if(!isset($_SESSION['qsw_'.$country.'_attributes'])){
				$_SESSION['qsw_'.$country.'_attributes'] = "";	
			}
			$testvalues = $_SESSION['qsw_'.$country.'_attributes'];	
		}else{
			$testvalues = "";	
		}
		
		$testvalues = unserialize($testvalues);
		if(!is_array($testvalues)){
			$testvalues = array($testvalues);
		}
	
		$attribout = "";  							
  		$collection = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter( Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId() );	
		
			$attribs = array();
			$count = 0;
			foreach($collection as $item){
				if(($item->getFrontendInput() != "media_image")&&($item->getFrontendInput() != "gallery")){
					
					$attribout .= "<option value='".$item->getAttributeCode()."' name='".$item->getAttributeCode()."'";
						if(in_array($item->getAttributeCode(),$testvalues)){
							$attribout .= " selected='selected'";	
						}
					$attribout .= ">".$item->getFrontendLabel()." (".$item->getAttributeCode()." ".$item->getFrontendInput().")"."</option>";
								
				}
			}

		return $attribout;

  	
  }
		
	}


?>