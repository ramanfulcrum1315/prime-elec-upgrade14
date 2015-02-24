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

class Camiloo_Amazonimport_Block_Manualsetup_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
      $this->setTitle(Mage::helper('amazonimport')->__('Manualsetup Setup'));
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
	$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
	$db = Mage::getSingleton('core/resource')->getConnection('core_read');
	$table_prefix = Mage::getConfig()->getTablePrefix();
		
		if(isset($_POST['product'])){
			$productid = implode(",",$_POST['product']);
		}else{
			$productid = $this->getRequest()->getParam('id');
		}
		
		// new- bulk handling logic.
		if(strpos($productid,",") > 0){
			$idsample = explode(",",$productid);
			$idsample = $idsample[0];
		}else{
			$idsample = $productid;
		}	
		
		
		 if($productid == $idsample){
		  
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
		$store = Mage::getStoreConfig('amazonint/amazon'.$country.'/store');
		
		
  		$collection = Mage::getModel('catalog/product')->setStoreId($store)->getCollection()
        ->joinTable('amazonimporterrorlog'.$country.'/amazonimporterrorlog'.$country,
         			 'productid=entity_id',
					 array('elog_id'=>'elog_id','productid'=>'productid','result'=>'result',
					 'result_description'=>'result_description','submission_type'=>'submission_type'),
					 null,
					 'inner')
		 ->addFieldToFilter('result',array('neq'=>''))
		 ->joinTable('amazonimportlistthis'.$country.'/amazonimportlistthis'.$country, 
		 			 'productid=entity_id', array('is_active' => 'is_active'), null, 'inner')		 
		 ->addFieldToFilter('is_active',array('eq' => 1))
		 ->addFieldToFilter('productid',array('eq' => $productid));
		 

		 if($collection->getSize() == 1){
		  
			$resArr = $collection->getData();

			if (is_array($resArr) && count($resArr) > 0) {
				$rd = "";

				for ($i = 0; $i < count($resArr); $i++) {
					$rd .= $resArr[$i]['result_description'] . "<br/><br/>";
				}

			  $this->addTab("ReviewError", array(
				'label'     => Mage::helper('amazonimport')->__('Review Error(s)'),
				'title'     => Mage::helper('amazonimport')->__('Review Error(s)'),
				'content'   => $this->getLayout()->createBlock('amazonimport/manualsetup_edit_tab_review')->setData('errordetails', $rd)->setData('productid', $idsample)->toHtml(),
			  ));

			}
		 }
	  
	  }
		
		
	 
  	  $this->addTab("MainAttributes", array(
          'label'     => Mage::helper('amazonimport')->__('Main Attributes'),
          'title'     => Mage::helper('amazonimport')->__('Main Attributes'),
          'content'   => $this->getLayout()->createBlock('amazonimport/manualsetup_edit_tab_defaults')->setData('producttype','Product')->setData('nodedata',$xml2)->toHtml(),
      ));
      
  	  $this->addTab("DescriptionAttributes", array(
          'label'     => Mage::helper('amazonimport')->__('Description Attributes'),
          'title'     => Mage::helper('amazonimport')->__('Description Attributes'),
          'content'   => $this->getLayout()->createBlock('amazonimport/manualsetup_edit_tab_defaults')->setData('producttype','Product/DescriptionData')->setData('nodedata',$xml->DescriptionData[0])->toHtml(),
      ));
  	
	  
      $categorised = "";
      $cats = Mage::getModel('amazonimportcategorise'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace').'/amazonimportcategorise'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_manualsetup_marketplace'))->getCollection()->addFieldToFilter('productid',array('eq'=>$idsample));
		foreach($cats as $categorised){
			break;	
		}
		if(!is_object($categorised)){
			
				$db->query(" DROP FUNCTION IF EXISTS GetAmzCatmapId; CREATE FUNCTION GetAmzCatmapId(paramProductid INTEGER) RETURNS INTEGER DETERMINISTIC
					 BEGIN
					 
					 DECLARE  current_cat_id INT;
					 DECLARE  catmapid INT;
					 DECLARE  current_count_two INT;
					 DECLARE  current_count_three INT;
					 DECLARE  no_more_products INT;
					 DECLARE  match_found INT;
					 
					 DECLARE  cur_product CURSOR FOR 
					 SELECT category_id FROM {$table_prefix}catalog_category_product
											INNER JOIN {$table_prefix}catalog_category_entity ON entity_id=category_id
											WHERE product_id = paramProductid ORDER BY {$table_prefix}catalog_category_entity.level DESC;
					
					 DECLARE  CONTINUE HANDLER FOR NOT FOUND 
				     	SET no_more_products = 1;
				
					 SET match_found = 0;
					 OPEN  cur_product;
					 
					 FETCH  cur_product INTO current_cat_id;
					 
					 mainloop: REPEAT 
					 	SELECT category_id INTO catmapid FROM {$table_prefix}amazonimport_categorymapping
						WHERE country_id='$country' AND category_id = current_cat_id; 

						SET no_more_products = 0;
					 	
						IF  catmapid > 0 AND match_found < 1 THEN
							SET match_found = catmapid;
							SELECT count(*) INTO current_count_two FROM {$table_prefix}amazonimport_categorise_$country WHERE productid = paramProductid;
							IF  current_count_two > 0 THEN
								SET match_found = 0;
								LEAVE mainloop;
							END IF;
						END IF;
											
						FETCH  cur_product INTO current_cat_id;
					  	UNTIL  no_more_products = 1
					 END REPEAT;
					 
					 CLOSE  cur_product;
					 RETURN match_found;
					 
END;");
			
			
			// we need to find out the itemtype for the product or sample product. to do this, we need to look it up in the category system
			$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_categorymapping WHERE category_id = GetAmzCatmapId($idsample);");
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$temp = explode("/",$row['itemtype']);
			$pd = $temp[0];
			$pdt = $temp[sizeof($temp)-1];
		
		}else{
			
			$pd = $categorised->getData('producttype');
			$pdt = $categorised->getData('productdatatype');
		
		}
		
		/*
			if(isset($_POST['product']))
		{
			$productid = implode(",",$_POST['product']);
		}
		else
		{
			$productid = $this->getRequest()->getParam('id');
		}
		
		
		*/
      
  	foreach($xml->ProductData[0] as $producttype){
  		
  		if($producttype->getName() ==  $pd){
  		
  			$productdatatype = clone $producttype->ProductType;
  	
  			if(sizeof($productdatatype) < 1 && is_object($producttype->ClassificationData->ClothingType)){
  				$productdatatype = clone $producttype->ClassificationData->ClothingType;
  				unset($producttype->ClassificationData->ClothingType);
  				$path = 'ClassificationDataClothingType';
  			}else{
  				$path = 'ProductType';  				
  			}
  			
  			unset($producttype->ProductType);
  			if (isset($producttype->VariationData->VariationTheme)) {
                unset($producttype->VariationData->VariationTheme);
            }
            if (isset($producttype->VariationData->Parentage)) {
                unset($producttype->VariationData->Parentage);
            }
			unset($producttype->Parentage);
  			if(sizeof($producttype) != 0){
			  
		      $this->addTab("".strtolower($producttype->getName())."", array(
		          'label'     => Mage::helper('amazonimport')->__('General Attributes for '.ucwords($this->from_camel_case("".$producttype->getName()."")).' Products'),
		          'title'     => Mage::helper('amazonimport')->__('Attributes for '.ucwords($this->from_camel_case("".$producttype->getName()."")).' Products'),
		          'content'   => $this->getLayout()->createBlock('amazonimport/manualsetup_edit_tab_defaults')->setData('producttype','Product/ProductData/'.$producttype->getName())->setData('nodedata',$producttype)->toHtml(),
		      ));
			}
		      if($productdatatype[0]) {
		      
			      foreach($productdatatype[0] as $theproductdatatype){
			      
			      		if($theproductdatatype->getName() == $pdt){
                            
                            if (isset($theproductdatatype->VariationData->VariationTheme)) {
                                unset($theproductdatatype->VariationData->VariationTheme);
                            }
			      			
			      			if("CamOption" != $theproductdatatype->getName()) {
			      			
		  					      $this->addTab("".strtolower($theproductdatatype->getName())."", array(
						    	      'label'     => Mage::helper('amazonimport')->__(ucwords($this->from_camel_case("".$theproductdatatype->getName()."")).' Attributes'),
						 	         'title'     => Mage::helper('amazonimport')->__(ucwords($this->from_camel_case("".$theproductdatatype->getName()."")).' Attributes'),
						 	         'content'   => $this->getLayout()->createBlock('amazonimport/manualsetup_edit_tab_defaults')->setData('producttype','Product/ProductData/'.$producttype->getName().'/'.$path.'/'.$theproductdatatype->getName())->setData('nodedata',$theproductdatatype)->setData('addon',$producttype->getName().$path)->toHtml(),
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
