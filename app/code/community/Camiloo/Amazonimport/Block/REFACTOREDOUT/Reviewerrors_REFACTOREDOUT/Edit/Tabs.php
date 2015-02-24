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

class Camiloo_Amazonimport_Block_Reviewerrors_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function from_camel_case($str) {
    $str[1] = strtolower($str[1]);
    $func = create_function('$c', 'return " " . strtolower($c[1]);');
    return preg_replace_callback('/([A-Z])/', $func, $str);
  }
  
  public function __construct()
  {
      parent::__construct();
      $this->setId('manualsetup_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('amazonimport')->__('Reviewerrors Setup'));
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
	
	
	ini_set("display_errors","on");
	
	  Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_reviewerrors_marketplace'),"camiloo_amazon_reviewerrors_marketplace");
	  Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_reviewerrors_marketplace'),"camiloo_amazon_mapping_marketplace");
	  Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_reviewerrors_marketplace'),"camiloo_amazon_variations_marketplace");
	  Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_reviewerrors_marketplace'),"camiloo_amazon_manualsetup_marketplace");

		$itemtype = Mage::registry('amazonimport_reviewerror_itemtype');
		$data = Mage::registry('amazonimport_reviewerror_data');
	
	  $this->addTab("ReviewError", array(
	  	'label'     => Mage::helper('amazonimport')->__('Review Error(s)'),
	  	'title'     => Mage::helper('amazonimport')->__('Review Error(s)'),
	  	'content'   => $this->getLayout()->createBlock('amazonimport/manualsetup_edit_tab_review')->setData('errordetails',$data['result_description'])->setData('productid',$data['productid'])->toHtml(),
      ));
	 
  	  $this->addTab("MainAttributes", array(
          'label'     => Mage::helper('amazonimport')->__('Edit Manual Setup of Main Attributes'),
          'title'     => Mage::helper('amazonimport')->__('Edit Manual Setup: <br /> Main Attributes'),
          'content'   => $this->getLayout()->createBlock('amazonimport/manualsetup_edit_tab_defaults')->setData('producttype','Product')->setData('nodedata',$xml2)->setData('productid',$data['productid'])->toHtml(),
      ));
      
  	  $this->addTab("DescriptionAttributes", array(
          'label'     => Mage::helper('amazonimport')->__('Edit Manual Setup of Description Attributes'),
          'title'     => Mage::helper('amazonimport')->__('Edit Manual Setup: <br /> Description Attributes'),
          'content'   => $this->getLayout()->createBlock('amazonimport/manualsetup_edit_tab_defaults')->setData('producttype','Product/DescriptionData')->setData('nodedata',$xml->DescriptionData[0])->setData('productid',$data['productid'])->toHtml(),
      ));
  	
		$productid = $data['productid'];
		
		// new- bulk handling logic.
		if(strpos($productid,",") > 0){
			$idsample = explode(",",$productid);
			$idsample = $idsample[0];
		}else{
			$idsample = $productid;
		}
      
	  $country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_reviewerrors_marketplace');


      $cats = Mage::getModel('amazonimportcategorise'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_reviewerrors_marketplace').'/amazonimportcategorise'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_reviewerrors_marketplace'))->getCollection()->addFieldToFilter('productid',array('eq'=>$data['productid']));
		foreach($cats as $categorised){
			break;	
		}
		if(isset($categorised)){
			$pd = $categorised->getData('producttype');
			$pdt = $categorised->getData('productdatatype');
		}else{
			$pd = "";
			$pdt = "";
		}
      
  	foreach($xml->ProductData[0] as $producttype){
  		
  		if($producttype->getName() ==  $pd){
  		
  			$productdatatype = clone $producttype->ProductType;
  	
  			if(sizeof($productdatatype) < 1){
  				$productdatatype = clone $producttype->ClassificationData->ClothingType;
  				unset($producttype->ClassificationData->ClothingType);
  				$path = 'ClassificationDataClothingType';
  			}else{
  				$path = 'ProductType';  				
  			}
  			
  			unset($producttype->ProductType);
  			unset($producttype->VariationData);
  			
		      $this->addTab("".strtolower($producttype->getName())."", array(
		          'label'     => Mage::helper('amazonimport')->__('Edit Manual Setup: <br /> General Attributes for '.ucwords($this->from_camel_case("".$producttype->getName()."")).' Products'),
		          'title'     => Mage::helper('amazonimport')->__('Edit Manual Setup: <br /> Attributes for '.ucwords($this->from_camel_case("".$producttype->getName()."")).' Products'),
		          'content'   => $this->getLayout()->createBlock('amazonimport/manualsetup_edit_tab_defaults')->setData('producttype','Product/ProductData/'.$producttype->getName())->setData('nodedata',$producttype)->setData('productid',$data['productid'])->toHtml(),
		      ));
		      
		      foreach($productdatatype[0] as $theproductdatatype){
		      
		      		if($theproductdatatype->getName() == $pdt){
		    	  	
		      			unset($theproductdatatype->VariationData);
  					      $this->addTab("".strtolower($theproductdatatype->getName())."", array(
				    	      'label'     => Mage::helper('amazonimport')->__("Edit Manual Setup: <br /> ".ucwords($this->from_camel_case("".$theproductdatatype->getName()."")).' Attributes'),
				 	         'title'     => Mage::helper('amazonimport')->__("Edit Manual Setup for <br /> ".ucwords($this->from_camel_case("".$theproductdatatype->getName()."")).' Attributes'),
				 	         'content'   => $this->getLayout()->createBlock('amazonimport/manualsetup_edit_tab_defaults')->setData('producttype','Product/ProductData/'.$producttype->getName().'/'.$path.'/'.$theproductdatatype->getName())->setData('nodedata',$theproductdatatype)->setData('addon',$producttype->getName().$path)->setData('productid',$data['productid'])->toHtml(),
				 	     ));
		      
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