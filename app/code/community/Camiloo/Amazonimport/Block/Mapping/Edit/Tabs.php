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

class Camiloo_Amazonimport_Block_Mapping_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function from_camel_case($str) {
    $str[1] = strtolower($str[1]);
    $func = create_function('$c', 'return " " . strtolower($c[1]);');
    return preg_replace_callback('/([A-Z])/', $func, $str);
  }
  
  public function __construct()
  {
      parent::__construct();
      $this->setId('mapping_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('amazonimport')->__('Mapping Setup'));
  }

  protected function _beforeToHtml()
  {
	$xml = Mage::getModel('amazonimport/amazonimport')->getTemplateXml();
	$xml2 = clone $xml;
	
	unset($xml2->ProductData);
	unset($xml2->DescriptionData);
	unset($xml2->DiscoveryData);  
	unset($xml2->RegisteredParameter);  
	unset($xml2->StandardProductID);  

	 
  	  $this->addTab("MainAttributes", array(
          'label'     => Mage::helper('amazonimport')->__('Main Attributes'),
          'title'     => Mage::helper('amazonimport')->__('Main Attributes'),
          'content'   => $this->getLayout()->createBlock('amazonimport/mapping_edit_tab_defaults')->setData('producttype','Product')->setData('nodedata',$xml2)->toHtml(),
      ));
      
  	  $this->addTab("DescriptionAttributes", array(
          'label'     => Mage::helper('amazonimport')->__('Description Attributes'),
          'title'     => Mage::helper('amazonimport')->__('Description Attributes'),
          'content'   => $this->getLayout()->createBlock('amazonimport/mapping_edit_tab_defaults')->setData('producttype','Product/DescriptionData')->setData('nodedata',$xml->DescriptionData[0])->toHtml(),
      ));
  	$count = 0;
	
		$marketplace = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
				
		$db = Mage::getSingleton('core/resource')->getConnection('core_read');
		$table_prefix = Mage::getConfig()->getTablePrefix();
				
		$producttypes = array();
		$productdatatypes = array();
		
		$result = $db->query("SELECT producttype, productdatatype FROM {$table_prefix}amazonimport_categorise_".$marketplace." GROUP BY productdatatype");
		if(!$result) {
			return FALSE;
		}
		$rows = $result->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($rows as $row){
			$producttypes["".$row['producttype'].""] = $row['producttype'];	
			$productdatatypes["".$row['producttype'].""]["".$row['productdatatype'].""] = $row['productdatatype'];	
		}
		
		$result = $db->query("SELECT itemtype FROM {$table_prefix}amazonimport_categorymapping WHERE country_id='".$marketplace."' GROUP BY itemtype");
		if(!$result) {
			return FALSE;
		}
		$rows = $result->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($rows as $row){
			$temp = explode("/",$row['itemtype']);
			$producttype = $temp[0];
			$productdatatype = $temp[sizeof($temp)-1];
			$producttypes["".$producttype.""] = $producttype;	
			$productdatatypes["".$producttype.""]["".$productdatatype.""] = $productdatatype;
		}

  		foreach($xml->ProductData[0] as $producttype){
   
   			$name = (string) $producttype->getName();
			
			if(array_search($name,$producttypes)){
				
			$matchingkey = array_search($name,$producttypes);
			$pdtsearcher = $productdatatypes[$matchingkey];
			
  			$productdatatype = clone $producttype->ProductType;
  	
  			if(sizeof($productdatatype) < 1 && is_object($producttype->ClassificationData->ClothingType)){
  				$productdatatype = clone $producttype->ClassificationData->ClothingType;
  				unset($producttype->ClassificationData->ClothingType);
  				$path = 'ClassificationDataClothingType';
  			}else{
  				$path = 'ProductType';  				
  			}
				
			  unset($producttype->ProductType);
			  unset($producttype->Parentage);
                
                if (isset($producttype->VariationData->VariationTheme)) {
                    unset($producttype->VariationData->VariationTheme);
                }
                if (isset($producttype->VariationData->Parentage)) {
                    unset($producttype->VariationData->Parentage);
                }
                
			  if(sizeof($producttype) != 0){
				  
			  $this->addTab("".strtolower($producttype->getName())."", array(
				  'label'     => Mage::helper('amazonimport')->__('General Attributes for '.ucwords($this->from_camel_case("".$producttype->getName()."")).' Products'),
				  'title'     => Mage::helper('amazonimport')->__('General Attributes for '.ucwords($this->from_camel_case("".$producttype->getName()."")).' Products'),
				  'content'   => $this->getLayout()->createBlock('amazonimport/mapping_edit_tab_defaults')->setData('producttype','Product/ProductData/'.$producttype->getName())->setData('nodedata',$producttype)->toHtml(),
			  ));
		
			  }
			   if ($productdatatype[0]) {
		      
			      foreach ($productdatatype[0] as $theproductdatatype) {
			      
				  		$name = $theproductdatatype->getName();
			      		if (array_search($name, $pdtsearcher)) {
			    	  	
                            if (isset($theproductdatatype->VariationData->VariationTheme)) {
                                unset($theproductdatatype->VariationData->VariationTheme);
                            }
                            
			      			if ("CamOption" != $theproductdatatype->getName()) {
			      			
		  					      $this->addTab("".strtolower($theproductdatatype->getName())."", array(
						    	      'label'     => Mage::helper('amazonimport')->__(ucwords($this->from_camel_case("".$theproductdatatype->getName()."")).' Attributes'),
						 	         'title'     => Mage::helper('amazonimport')->__(ucwords($this->from_camel_case("".$theproductdatatype->getName()."")).' Attributes'),
						 	         'content'   => $this->getLayout()->createBlock('amazonimport/mapping_edit_tab_defaults')->setData('producttype','Product/ProductData/'.$producttype->getName().'/'.$path.'/'.$theproductdatatype->getName())->setData('nodedata',$theproductdatatype)->setData('addon',$producttype->getName().$path)->toHtml(),
						 	     ));
			      			}
			      		}
				      
			      }      
			      
		      }
				  
				  
				  
			}
		}
	
      return parent::_beforeToHtml();
  }
  
  public function attribout(){
  	
		$attribout = "";  							
  		$collection = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter( Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId() );	
		
			$attribs = array();
			$count = 0;
			foreach($collection as $item){
				if(($item->getFrontendInput() != "media_image")&&($item->getFrontendInput() != "gallery")){
					$attribout .= "<option value='x' name='".$item->getAttributeCode()."'>".$item->getFrontendLabel()." (".$item->getAttributeCode()." ".$item->getFrontendInput().")"."</option>";
				}
			}

		return $attribout;

  	
  }
}