<?php

class Camiloo_Amazonimport_Block_Manualsetup_Bulk_Edit_Tab_Help extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
	parent::__construct();  
	$this->setTemplate('amazonimport/manualsetup/bulk/help.phtml');
  }
  
   public function from_camel_case($str) {
    $str[1] = strtolower($str[1]);
    $func = create_function('$c', 'return " " . strtolower($c[1]);');
    return preg_replace_callback('/([A-Z])/', $func, $str);
  }

  public function loadvtsforproduct($pdt){
  	  	
	$example = Mage::getModel('amazonimportcategorise'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace').'/amazonimportcategorise'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace'))->getCollection()->addFieldToFilter('productdatatype',array('eq'=>$pdt));
	foreach($example as $mdl){
		break;
	}
	
	$data = $mdl->getData();
	
  	
  	$xml = Mage::getModel('amazonimport/amazonimport')->getTemplateXml();
  		
 	$node = $xml->xpath('//ProductData/'.$data['producttype'].'/ProductType/'.$data['productdatatype'].'/VariationData');
	if(sizeof($node) == 0){
		$node = $xml->xpath('//ProductData/'.$data['producttype'].'/VariationData');		
	}
	
	foreach($node[0]->VariationTheme[0]->CamOption as $vt){
		echo "To set variation theme to ".$this->from_camel_case($vt->Value).", enter the following code into Column D without quotes:<h3>\"".$vt->Value."\"</h3>";
	}
	
  }
  
  
}

?>