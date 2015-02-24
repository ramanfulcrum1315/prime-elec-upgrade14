<?php

class Camiloo_Amazonimport_Block_Variations_Edit_Tab_Defaults extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
  	parent::__construct();  
	$this->setTemplate('amazonimport/variations/defaults.phtml');
  }
  
  public function from_camel_case($str) {
    $str[1] = strtolower($str[1]);
    $func = create_function('$c', 'return " " . strtolower($c[1]);');
    return preg_replace_callback('/([A-Z])/', $func, $str);
  }

  public function loadvtsforproduct($productid){
  	
  	$col = Mage::getModel('amazonimportcategorise'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace').'/amazonimportcategorise'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace'))->getCollection()->addFieldToFilter('productid',array('eq'=>$productid));
  	foreach($col as $model){
  		break;  		
  	}
  	$row = $model->getData();
  	
  	$col = Mage::getModel('amazonimportvariations'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace').'/amazonimportvariations'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace'))->getCollection()->addFieldToFilter('configurable_product_id',array('eq'=>$productid));
  	foreach($col as $model){
  		break;  		
  	}
  	$row2 = $model->getData();
  	
  	if(isset($row2['variation_theme'])){
  		$currentvt = $row2['variation_theme'];  		
  	}else{
  		$currentvt = "";  		
  	}
  	
  	$xml = Mage::getModel('amazonimport/amazonimport')->getTemplateXml();
		
 	$node = $xml->xpath('//ProductData/'.$row['producttype'].'/ProductType/'.$row['productdatatype'].'/VariationData');
	if(sizeof($node) == 0){
		$node = $xml->xpath('//ProductData/'.$row['producttype'].'/VariationData');		
	}
	
	foreach($node[0]->VariationTheme[0]->CamOption as $vt){
		echo '<option value="'.$vt->Value.'"';
		if("".$currentvt."" == "".$vt->Value.""){
			echo ' selected="selected"';	
		}		
		echo '>'.$this->from_camel_case($vt->Value).'</option>';		
	}
	
  }
  
}

?>