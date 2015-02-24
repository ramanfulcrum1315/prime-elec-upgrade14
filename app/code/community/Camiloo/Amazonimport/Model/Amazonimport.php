<?php

class Camiloo_Amazonimport_Model_Amazonimport extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('amazonimport/amazonimport');
    }
    
    function curl_get($url, array $get = NULL, array $options = array()) 
	{    
	    $defaults = array( 
	        CURLOPT_URL => $url, 
	        CURLOPT_HEADER => 0, 
	        CURLOPT_RETURNTRANSFER => TRUE, 
	        CURLOPT_TIMEOUT => 30, 
	    ); 
	    
	    $ch = curl_init(); 
	    curl_setopt_array($ch, ($options + $defaults)); 
	    if( ! $result = curl_exec($ch)) 
	    { 
	       return false;
	    } 
	    curl_close($ch); 
	    return $result; 
	}
    
	public function convertCategoryNameToProductDataType($categoryName, $country, $reverse=false){
	
        /* Bugfix 24/06/2011 - MN: Issue RGX-218-27228
           Accented characters causing UTF issue. Resolved by removing these characters.
        */

        $categoryName = preg_replace("/[^a-zA-Z0-9\s]/", "", $categoryName);

	 if($country == "uk"){
		 $categoryToPdtMappings["Apparel"] = array('ClothingAccessories|Accessories','Clothing|Clothing Items');
		 $categoryToPdtMappings["Automotive"] = array('AutoAccessory|Automotive Accessories','TiresAndWheels|Tires and Wheels','Tools|Tools');
		 $categoryToPdtMappings["Beauty"] = array('Beauty|Beauty Products','Health|Health Products');
		 $categoryToPdtMappings["Baby"] = array('ToysBaby| Toys &amp; Baby Products');
		 $categoryToPdtMappings["Computers  Accessories"] = array('CE|Consumer Electronics','Wireless|Mobile / Cell Phone Related','CameraPhoto|Cameras &amp; Photography Equipment');
		 $categoryToPdtMappings["Consumer Electronics"] = array('CE|Consumer Electronics','Wireless|Mobile / Cell Phone Related','CameraPhoto|Cameras &amp; Photography Equipment');
		 $categoryToPdtMappings["Grocery  Beverages"] = array('Gourmet|Gourmet','PetSupplies|Pet Supplies','FoodAndBeverages|Food and Drink');
		 $categoryToPdtMappings["Health  Personal Care"] =  array('Beauty|Beauty Products','Health|Health Products');
		 $categoryToPdtMappings["Home and Garden"] = array('Home|Home and Garden Items','PetSupplies|Pet Supplies','Lighting|Lighting');
		 $categoryToPdtMappings["Home Improvement"] = array('HomeImprovement|Home Improvement / DIY','Tools|Tools','Lighting|Lighting');
		 $categoryToPdtMappings["Jewellery"] = array('Jewelry|Jewelry');
		 $categoryToPdtMappings["Lighting"] = array('Lighting|Lighting');
		 $categoryToPdtMappings["Musical Instruments"] = array('MusicalInstruments|Musical Instruments','ToysBaby|Musical Toys');
		 $categoryToPdtMappings["Office"] = array('Office|Office Equipment');
		 $categoryToPdtMappings["Shoes  Accessories"] = array('ClothingAccessories|Accessories','Clothing|Clothing Items');
		 $categoryToPdtMappings["Software"] = array('SWVG|Software');
		 $categoryToPdtMappings["Sports"] = array('Sports|Sports Equipment','TiresAndWheels|Tires and Wheels','Clothing|Sports Clothing','ClothingAccessories|Sports Clothing Accessories');
		 $categoryToPdtMappings["Toys"] = array('ToysBaby|Toys');
		 $categoryToPdtMappings["Video Games"] = array('SWVG|Video Games');
		 $categoryToPdtMappings["Watches"] = array('Jewelry|Jewelry');
		 }else if($country == "com"){
		 $categoryToPdtMappings["Automotive"] = array('AutoAccessory|Automotive Accessories','TiresAndWheels|Tires and Wheels','Tools|Tools');
		 $categoryToPdtMappings["Baby Products"] = array('ToysBaby| Toys &amp; Baby Products');
		 $categoryToPdtMappings["Beauty"] = array('Beauty|Beauty Products','Health|Health Products');
		 $categoryToPdtMappings["Clothing and Accessories"] = array('ClothingAccessories|Accessories','Clothing|Clothing Items');
		 $categoryToPdtMappings["Consumer Electronics"] = array('CE|Consumer Electronics','Wireless|Mobile / Cell Phone Related','CameraPhoto|Cameras &amp; Photography Equipment');
		 $categoryToPdtMappings["Grocery and Gourmet Food"] = array('Gourmet|Gourmet','PetSupplies|Pet Supplies','FoodAndBeverages|Food and Drink');
		 $categoryToPdtMappings["Health and Personal Care"] = array('Beauty|Beauty Products','Health|Health Products');
		 $categoryToPdtMappings["Home and Garden"] = array('Home|Home and Garden Items','PetSupplies|Pet Supplies','Lighting|Lighting');
		 $categoryToPdtMappings["Jewelry"] = array('Jewelry|Jewelry');
		 $categoryToPdtMappings["Musical Instruments"] = array('MusicalInstruments|Musical Instruments','ToysBaby|Musical Toys');
		 $categoryToPdtMappings["Office Products"] = array('Office|Office Equipment');
		 $categoryToPdtMappings["Shoes"] = array('ClothingAccessories|Accessories','Clothing|Clothing Items');
		 $categoryToPdtMappings["Software"] = array('SWVG|Software');
		 $categoryToPdtMappings["Sports and Outdoors"] = array('Sports|Sports Equipment','TiresAndWheels|Tires and Wheels','Clothing|Sports and Outdoor Clothing','ClothingAccessories|Sports and Outdoor Clothing Accessories');
		 $categoryToPdtMappings["Tools and Home Improvement"] = array('HomeImprovement|Home Improvement / DIY','Tools|Tools','Lighting|Lighting');
		 $categoryToPdtMappings["Toys and Games"] = array('ToysBaby|Toys and Games');
		 $categoryToPdtMappings["Video Games"] = array('SWVG|Video Games');
		 $categoryToPdtMappings["Watches"] = array('Jewelry|Jewelry');
		 $categoryToPdtMappings["Wireless"] = array('Wireless|Mobile / Cell Phone Related');
		 }else if($country == "de"){
		 $categoryToPdtMappings["Auto  Motorrad"] = array('AutoAccessory|Automotive Accessories','TiresAndWheels|Tires and Wheels','Tools|Tools');
		 $categoryToPdtMappings["Baby"] = array('ToysBaby| Toys &amp; Baby Products');
		 $categoryToPdtMappings["Baumarkt"] = array('HomeImprovement|Home Improvement / DIY','Tools|Tools','Lighting|Lighting');
		 $categoryToPdtMappings["Bekleidung"] = array('ClothingAccessories|Accessories','Clothing|Clothing Items');
		 $categoryToPdtMappings["Beleuchtung"] = array('Lighting|Lighting');
		 $categoryToPdtMappings["Brobedarf  Schreibwaren"] = array('Office|Office Equipment');
		 $categoryToPdtMappings["Computer  Zubehr"] = array('CE|Consumer Electronics','Wireless|Mobile / Cell Phone Related','CameraPhoto|Cameras &amp; Photography Equipment');
		 $categoryToPdtMappings["Drogerie  Bad"] = array('Beauty|Beauty Products','Health|Health Products');
		 $categoryToPdtMappings["Elektronik  Foto"] = array('CE|Consumer Electronics','Wireless|Mobile / Cell Phone Related','CameraPhoto|Cameras &amp; Photography Equipment');
		 $categoryToPdtMappings["Garten  Freizeit"] = array('Home|Garden Items','PetSupplies|Pet Supplies','Lighting|Lighting');
		 $categoryToPdtMappings["Games"] = array('SWVG|Software');
		 $categoryToPdtMappings["Kche  Haushalt"] = array('Home|Home & Kitchen Items','PetSupplies|Pet Supplies','Lighting|Lighting');
		 $categoryToPdtMappings["Lebensmittel"] = array('Gourmet|Gourmet','FoodAndBeverages|Food and Drink');
		 $categoryToPdtMappings["Musikinstrumente  DJ-Equipment"] = array('MusicalInstruments|Musical Instruments / DJ Equipment','ToysBaby|Musical Toys');
		 $categoryToPdtMappings["Parfmerie  Kosmetik"] = array('Beauty|Beauty Products','Health|Health Products');
		 $categoryToPdtMappings["Schmuck"] = array('Jewelry|Jewelry');
		 $categoryToPdtMappings["Schuhe  Handtaschen"] = array('ClothingAccessories|Accessories','Clothing|Clothing Items');
		 $categoryToPdtMappings["Software"] = array('SWVG|Software');
		 $categoryToPdtMappings["Spielwaren  Kinderwelt"] = array('ToysBaby|Toys and Games');
		 $categoryToPdtMappings["Sport  Freizeit"] = array('Sports|Sports Equipment','TiresAndWheels|Tires and Wheels','Clothing|Sports and Outdoor Clothing','ClothingAccessories|Sports and Outdoor Clothing Accessories');
		 $categoryToPdtMappings["Uhren"] = array('Jewelry|Jewelry');
	}else if($country == "fr"){ 
		 $categoryToPdtMappings["Bijoux"] = array('Jewelry|Jewelry');
		 $categoryToPdtMappings["Chaussures"] = array('ClothingAccessories|Accessories','Clothing|Clothing Items');
		 $categoryToPdtMappings["Cuisine  Maison"] = array('Home|Home & Kitchen Items','PetSupplies|Pet Supplies','Lighting|Lighting');
		 $categoryToPdtMappings["Fournitures de bureau"] = array('Office|Office Equipment');
		 $categoryToPdtMappings["Image  Son  Photo"] = array('CE|Consumer Electronics','Wireless|Mobile / Cell Phone Related','CameraPhoto|Cameras &amp; Photography Equipment');
		 $categoryToPdtMappings["Informatique"] = array('CE|Consumer Electronics','Wireless|Mobile / Cell Phone Related','CameraPhoto|Cameras &amp; Photography Equipment');
		 $categoryToPdtMappings["Instruments de musique et Sono"] = array('MusicalInstruments|Musical Instruments','ToysBaby|Musical Toys');
		 $categoryToPdtMappings["Jeux et Jouets"] = array('ToysBaby|Toys and Games');
		 $categoryToPdtMappings["Jeux Vidos"] = array('SWVG|Software');
		 $categoryToPdtMappings["Logiciels"] = array('SWVG|Software');
		 $categoryToPdtMappings["Luminaires et Eclairage"] = array('Lighting|Lighting');
		 $categoryToPdtMappings["Montres"] = array('Jewelry|Jewelry');
		 $categoryToPdtMappings["Parfum et Beaut"] = array('Beauty|Beauty Products','Health|Health Products');
		 $categoryToPdtMappings["Puriculture"] =  array('ToysBaby| Toys &amp; Baby Products');
		 $categoryToPdtMappings["Sant et Soins du corps"] = array('Beauty|Beauty Products','Health|Health Products');
		 $categoryToPdtMappings["Sports et Loisirs"] = array('Sports|Sports Equipment','TiresAndWheels|Tires and Wheels','Clothing|Sports and Outdoor Clothing','ClothingAccessories|Sports and Outdoor Clothing Accessories');
		 $categoryToPdtMappings["Vtements"] = array('ClothingAccessories|Accessories','Clothing|Clothing Items');
		 }

		 if($reverse == false){
			 if(isset($categoryToPdtMappings[$categoryName])){

				 return $categoryToPdtMappings[$categoryName];
			 }else{
				 return false;
			 }
		 }else{
			 foreach($categoryToPdtMappings as $key=>$catgroup){
					foreach($catgroup as $catelement){
						$temp = explode("|",$catelement);
						$testKitten = $temp[0];
						if($categoryName == $testKitten){
							return $key;
						}
					}
			 }
				 return false;
		 }
	}
	
	public function computeHumanReadibleItemtype($cm, $country){
		
		$itemtype = $cm->getData('itemtype');
		$itemtype = explode("/",$itemtype);
		$output = "";
		$producttype = $itemtype[0];
		$productdatatype = $itemtype[sizeof($itemtype)-1];
	
		$bn = Mage::getModel('amazonimport/amazonimportbrowsenodes')->load($cm->getData('browsenode1'));
		
		if (isset($bn)) {
		
			$category_name = $bn->getData('category_name');
			$array = $this->convertCategoryNameToProductDataType($category_name, $country);
			
			if (is_array($array)) {

				foreach($array as $possibleProductDataType){
					$temp = explode("|", $possibleProductDataType);
					$name = $temp[1];
					$xsd = $temp[0];
				
					if($xsd == "ProductClothing"){
						$xsd = "Clothing";	
					}
				
					if($xsd == $producttype){
						$output = $name." &gt; ".$this->FormatCamelCase($productdatatype);
					}
				
				}
		
				return $output;

			}
			else {
				return "Unknown Product Type";
			}

		}
		else
			return $itemtype;
		
	}
	
	public function getPossibleVariationThemesSelect($itemtype){
		//itemtype values are in format producttype/x.[multiple / elements possible]../itemtype
		$output = '<option value="#">Select a value to update this setting</option>';	
		$xml = $this->getTemplateXml();
			
		$itemtype = explode("/",$itemtype);
		$producttype = $itemtype[0];
		$productdatatype = $itemtype[sizeof($itemtype)-1];
			
		$node = $xml->xpath('//ProductData/'.$producttype.'/ProductType/'.$productdatatype.'/VariationData');
		if(sizeof($node) == 0){
			$node = $xml->xpath('//ProductData/'.$producttype.'/VariationData');		
		}
		
		if(isset($node[0])){
			if(isset($node[0]->VariationTheme[0])){
				foreach($node[0]->VariationTheme[0]->CamOption as $vt){
					$output .= '<option value="'.$vt->Value.'"';
					$output .= '>'.$this->FormatCamelCase($vt->Value).'</option>';		
				}
			}else{
				$output = false;
			}
		}else{
			$output = false;	
		}
		return $output;
	}
	
	
	public function getPossibleItemtypesSelect($categoryName, $country, $currentitemtype=""){
	
		$output = "";
		$output = '<option value="#">Select a value to update this setting</option>';	
		$possibleProductDataTypes = $this->convertCategoryNameToProductDataType($categoryName, $country);

		if (!is_array($possibleProductDataTypes)) {
			return $output;
		}

		foreach($possibleProductDataTypes as $possibleProductDataType){
				$temp = explode("|", $possibleProductDataType);
				$name = $temp[1];
				$xsd = $temp[0];
				if($xsd == "ProductClothing"){
					$xsd = "Clothing";	
				}
				$xml = $this->getTemplateXml();
		
				foreach($xml->ProductData[0] as $producttype){

					if($producttype->getName() == $xsd
						|| ($producttype->getName() == "SoftwareVideoGames" && $xsd == "SWVG")){
					
						if($xsd == "Clothing"){
							
							
							$typestemp = $producttype->ClassificationData->ClothingType->children();
							foreach($typestemp as $type){
								if(!isset($typesarray["$xsd/ClassificationData/ClothingType/".(string) $type[0]->Value.""])){
								$typesarray["$xsd/ClassificationData/ClothingType/".(string) $type[0]->Value.""] = $name." &gt; ".ucwords($this->FormatCamelCase((string) $type[0]->Value));
								}
							}
							
						}else{
							
							// bugfix 07072010 - check for productdatatype as a CamOption; display as drop down.
							try{
								
								if($producttype->ProductType->children()->getName() == "CamOption"){
									
									foreach($producttype->ProductType->children() as $type){
										if(!isset($typesarray["$xsd/ProductType/".(string) $type[0]->Value.""])){
											$typesarray["$xsd/ProductType/".(string) $type[0]->Value.""] = $name." &gt; ".ucwords($this->FormatCamelCase((string) $type[0]->Value));
										}
									}
								
								
								}else{
								
									if(isset($producttype->ProductType)){
										
										foreach($producttype->ProductType->children() as $type){
											if(!isset($typesarray["$xsd/ProductType/".(string) $type->getName().""])){
											$typesarray["$xsd/ProductType/".(string) $type->getName().""] = $name." &gt; ".ucwords($this->FormatCamelCase((string) $type->getName()));	
											}
										}
										
									}
								}
								
							}catch (Exception $e){
													
									if(isset($producttype->ProductType)){
										
										
										foreach($producttype->ProductType->children() as $type){
											if(!isset($typesarray["$xsd/ProductType/".(string) $type->getName().""])){
											$typesarray["$xsd/ProductType/".(string) $type->getName().""] = $name." &gt; ".ucwords($this->FormatCamelCase((string) $type->getName()));												
											}
										}
										
									}
								
							}
						}
						
					}
					
					$producttype = "";			
				}
				
		}
		if(isset($typesarray)){
						
						$matched = false;
						foreach($typesarray as $key=>$value){
							$output .= '<option value="'.$key.'"';
							$output .= '>'.utf8_decode($value).'</option>';
						}
								
						
						
				}
		
		return $output;
	
	}
	
	
	public function getTemplateXML($country = ''){

		if ($country == '') {

			$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_automatch_marketplace');
	
		}

		$xmlPath = $this->getLocalRoot()
			.'app/design/adminhtml/default/default/template/amazonimport/mappings/Template_'.$country.'.xml';
		
		try {
			
			$xml = simplexml_load_file($xmlPath, 'SimpleXMLElement', LIBXML_NOCDATA);
			
			if (FALSE === $xml) {
				echo "Error loading Template - ".$xmlPath."\n";
			}
		}
		catch (Exception $e) {
			echo "Exception caught ".$e."\n";
			
			die;
		}
		
		return $xml;
	
	}
	
	public function getRootUrl(){
	
		if((!empty($_SERVER['HTTPS']))&&($_SERVER['HTTPS'] == "on")){
			return Mage::getUrl('',array('_secure'=>true));
		}else{
			return Mage::getUrl('');
		}

	}

	public function flatordersDuplicationLock($amazonOrderId, $amazonMarketplace){
	
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		// BUGFIX 24/05/2011 insert a placeholder, then remove it when the standard insert happens.
		$_sql = "INSERT INTO {$table_prefix}amazonimport_flatorders (`entity_id`,`amazon_order_id`,`amazon_marketplace`)
				VALUES (0,'".$amazonOrderId."','".$amazonMarketplace."')";
		
		$result = $db->query($_sql);
	}	

	public function flatordersInsertHelper($orderEntityId, $amazonOrderId, $amazonMarketplace){
	
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		
		// BUGFIX 24/05/2011 insert a placeholder, then remove it when the standard insert happens.
		$_sql = "DELETE FROM {$table_prefix}amazonimport_flatorders WHERE entity_id = 0 AND `amazon_order_id`='".$amazonOrderId."' AND `amazon_marketplace`='".$amazonMarketplace."'";
		$result = $db->query($_sql);
		

		$_sql = "INSERT INTO {$table_prefix}amazonimport_flatorders (`entity_id`,`amazon_order_id`,`amazon_marketplace`)
				VALUES ('".$orderEntityId."','".$amazonOrderId."','".$amazonMarketplace."')";
		
		$result = $db->query($_sql);
	}
	
	public function flatordersUniqueHelper($amazonOrderId){
	
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		
		$_sql = "SELECT * FROM {$table_prefix}amazonimport_flatorders WHERE amazon_order_id='$amazonOrderId'";
		
		$result = $db->query($_sql);
		
		if($result->rowCount() > 0){
			return array("1","2","3");	
		}else{
			return array();
		}
	}
	
	public function flatordersGetOrderEntityId($amazonOrderId){
	
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		
		$_sql = "SELECT * FROM {$table_prefix}amazonimport_flatorders WHERE amazon_order_id='$amazonOrderId'";
		
		$result = $db->query($_sql);
		
		if($result->rowCount() > 0){
			$row = $result->fetch();
			return $row['entity_id'];	
		}else{
			return 0;
		}
	}

	public function limitLoggingHelper(){
		
		if(Mage::getModel('amazonimport/amazonimportlog')->getCollection()->getSize() > 200){
			$db = Mage::getSingleton("core/resource")->getConnection("core_write");
			$table_prefix = Mage::getConfig()->getTablePrefix();		
			$_sql = "SELECT * FROM {$table_prefix}amazonimport_log ORDER BY log_id ASC LIMIT 0,1";
			$result = $db->query($_sql);
			$row = $result->fetch();
			$id = $row['log_id'];
			$goodbye = Mage::getModel('amazonimport/amazonimportlog')->load($id);
			$goodbye->delete();
		}
		
	}	
	
	public function flatordersLookupHelper($orderEntityId){
	
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		
		$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_flatorders WHERE entity_id='$orderEntityId'");
		if($result->rowCount() > 0){
			$row = $result->fetch();
			return array($row['amazon_order_id'],$row['amazon_marketplace'],$row['entity_id']);	
		}else{
			return array();
		}
		
	}
	
	public function getSkinUrl(){
	
		if((!empty($_SERVER['HTTPS']))&&($_SERVER['HTTPS'] == "on")){
			return str_replace("index.php/","",Mage::getUrl('',array('_secure'=>true)));
		}else{
			return str_replace("index.php/","",Mage::getUrl(''));
		}

	}
	/**

	 */
	public function getLocalRoot()
	{
		return Mage::getBaseDir() . '/';
	}

        public function marketplaceIdToName($mkt) {
            $mpid = $mkt;
	    
	    switch ($mkt) {
	    case "ATVPDKIKX0DER":
	        $mpid = "Amazon.com";
	        break;
	        
	    case "A1PA6795UKMFR9":
	        $mpid = "Amazon.de";
	        break;
	        
	    case "A1F83G8C2ARO7P":
	        $mpid = "Amazon.co.uk";
	        break;
	        
	    case "A13V1IB3VIYZZH":
	        $mpid = "Amazon.fr";
	        break;
	        
	    case "APJ6JRA9NG5V4":
	        $mpid = "Amazon.it";
	        break;
	    }
	    return $mpid;
        }
	
	public function getMarketplaceId($country) {
	    $mpid = "";
	    
	    switch ($country) {
	    case "com":
	        $mpid = "ATVPDKIKX0DER";
	        break;
	        
	    case "de":
	        $mpid = "A1PA6795UKMFR9";
	        break;
	        
	    case "uk":
	        $mpid = "A1F83G8C2ARO7P";
	        break;
	        
	    case "fr":
	        $mpid = "A13V1IB3VIYZZH";
	        break;
	        
	    case "it":
	        $mpid = "APJ6JRA9NG5V4";
	        break;
                
        default:
            $mpid = "MARKETPLACEXXXXX";
            break;
	    }
	    return $mpid;
	}
	
	public function saveSessionValue($value,$key){
		if(strpos($key, "_marketplace") > 0){
			$key = "amazonimport_marketplace";	
		}
		Mage::getSingleton('adminhtml/session')->setData($key,$value);	
	}
	
	/**
	 * Loads a cached value for use by the Amazon module.
	 */
	public function loadSessionValue($key){
		if(strpos($key, "_marketplace") > 0){
			$key = "amazonimport_marketplace";	
		}
		$value = Mage::getSingleton('adminhtml/session')->getData($key);	
		if(($value == "")&&($key == "amazonimport_marketplace")){
			header("Location: ".Mage::getUrl('adminhtml'));
			die();
		}
		return $value;
	}
	
	public function browserCheckForReloader($form){
	
			// firefox form reload compatability fix
		 if(strpos($_SERVER['HTTP_USER_AGENT'],"Firefox") > 0){
			return "setTimeout(\"$('".$form."').submit()\",250);";
		 }else{
			return "$('".$form."').submit();";
		 } 
		
	}
	
	public function FormatCamelCase( $string ) {
                $output = "";
                foreach( str_split( $string ) as $char ) {
                        strtoupper( $char ) == $char and $output and $output .= " ";
                        $output .= $char;
                }
                return $output;
    }
	
	
	/*
		carefulProductLoad
		This function takes a search term, search attribute (optional) and array of attributes to select (optional)
		and loads the product model in a memory friendly manner. The method returns false on failure to load.	
		Addendum: option for store (MN 02/04)
	*/
	public function carefulProductLoad($searchValue, $attributeToLookup="entity_id", $attributeToSelect=array(), $store=""){
	
	    if ($store != "") {
           $currentstore = Mage::app()->getStore()->getId();
           Mage::app()->setCurrentStore($store);
        }
	    
			try {
						
				if($store != ""){
					$collectionOfProduct = Mage::getModel('catalog/product')->setStoreId($store)->getCollection();	
				}else{
					$collectionOfProduct = Mage::getModel('catalog/product')->getCollection();
				}
					
				$collectionOfProduct->addAttributeToFilter($attributeToLookup,array('eq'=>$searchValue));
						
				foreach($attributeToSelect as $attribute){
					$collectionOfProduct->addAttributeToSelect($attribute);
				}
						
				if($collectionOfProduct->getSize() < 1){
				    if($store != ""){
                           Mage::app()->setCurrentStore($currentstore);
                   }
					return false;	
				}else{
					foreach($collectionOfProduct as $product){
						break;	
					}
					if($store != ""){
                           Mage::app()->setCurrentStore($currentstore);
                   }
					return $product;
				}
				
			}catch (Exception $e){
			
				// AN ERROR OCCURRED. Load product data in the old way to avoid any issues.
				$collectionOfProduct = Mage::getModel('catalog/product')->setStoreId($store)->getCollection();
				if($store != ""){
					$collectionOfProduct = Mage::getModel('catalog/product')->setStoreId($store)->getCollection();	
				}else{
					$collectionOfProduct = Mage::getModel('catalog/product')->getCollection();
				}
				$collectionOfProduct->addAttributeToFilter($attributeToLookup,array('eq'=>$searchValue));
				$collectionOfProduct->addAttributeToSelect("*");
				
				if($collectionOfProduct->getSize() < 1){
				    if ($store != "") {
                        Mage::app()->setCurrentStore($currentstore);
                    }
					return false;	
				}else{
					foreach($collectionOfProduct as $product){
						break;	
					}
					if ($store != "") {
                        Mage::app()->setCurrentStore($currentstore);
                    }
					return $product;
				}
			}
			
	}
	
	public function getSkuAccordingToMapping($productid, $db, $table_prefix, $cmc) {
		
		$_item = $this->carefulProductLoad($productid);
		
		if ($_item)
		{
			$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_manualsetup_".$cmc." WHERE productid=".$productid
				    ." AND xmlkey = 'Product/SKU'");
			$manuals = $result->fetchAll(PDO::FETCH_ASSOC);
			
			foreach($manuals as $manual){
				
				return $manual['manualsetupvalue'];
			}
			
			// == No manual override for SKU - see if it's mapped
			
			$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_mapping_".$cmc." WHERE xmlkey = 'Product/SKU'");
		    $mappings = $result->fetchAll(PDO::FETCH_ASSOC);
	
		
			
		    foreach($mappings as $mapping) {
				$_item = $this->carefulProductLoad($productid,"entity_id",array($mapping['mappingvalue']));
		    	return $_item->getData($mapping['mappingvalue']);
		    }
		    
		    // == else just use normal SKU
		    
			$_item = $this->carefulProductLoad($productid,"entity_id",array("sku"));
		    return $_item->getData('sku');
		}
		else {
			return false;
		}
	}
	
	
	public function getSkuAccordingToOrderImport($sku, $db, $table_prefix, $cmc) {
		
			if ($cmc == "uk") { $store = Mage::getStoreConfig('amazonint/amazonuk/store'); }
			if ($cmc == "fr") { $store = Mage::getStoreConfig('amazonint/amazonfr/store'); }
			if ($cmc == "de") { $store = Mage::getStoreConfig('amazonint/amazonde/store'); }
			if ($cmc == "com"){ $store = Mage::getStoreConfig('amazonint/amazoncom/store');}
		
			$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_manualsetup_".$cmc."
								   WHERE manualsetupvalue='".mysql_escape_string($sku)."' AND xmlkey = 'Product/SKU'");
			$manuals = $result->fetchAll(PDO::FETCH_ASSOC);
			
			foreach($manuals as $manual){			
				return Mage::getModel('catalog/product')->load($manual['productid']);
			}
			
			// == No manual override for SKU - see if it's mapped			
			$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_mapping_".$cmc." WHERE xmlkey = 'Product/SKU'");
		    $mappings = $result->fetchAll(PDO::FETCH_ASSOC);
	
		    foreach($mappings as $mapping) {
				$collection = Mage::getModel('catalog/product')->getCollection()->addFieldToFilter($mapping['mappingvalue'],array('eq'=>$sku));
				if($collection->getSize() < 1){
					return false;	// cron.php will create a stub.					
				}else{
		    		foreach($collection as $product){
						break;	
					}
				}
				return $product;
		    }
		    
		    // == else just use normal SKU
		 		$collection = Mage::getModel('catalog/product')->getCollection()->addFieldToFilter('sku',array('eq'=>$sku));
				if($collection->getSize() < 1){
					return false;	// cron.php will create a stub.
				}else{
		    		foreach($collection as $product){
						break;	
					}
				}
				return $product;
		
	}
	
	
	public function updateCache(){
			
	 # BUGFIX GAX-602-21855. Using PHPExcel locally was causing no end of problems, therefore we will now move to a CDN based system.
	 #		 Updated files will be generated once daily by the Camiloo Server Farm and submitted to a CDN for hosting.
	 
	 $db = Mage::getSingleton("core/resource")->getConnection("core_write");
	 $table_prefix = Mage::getConfig()->getTablePrefix();
	 
	 $db->query("TRUNCATE TABLE {$table_prefix}amazonimport_browsenodes");
	 $db->query("TRUNCATE TABLE {$table_prefix}amazonimport_fielddescriptions");
	 $db->query("ALTER TABLE {$table_prefix}amazonimport_browsenodes MODIFY browsenode_id bigint(11)");
	 $db->query("ALTER TABLE {$table_prefix}amazonimport_categorymapping MODIFY browsenode1 bigint(11), MODIFY browsenode2 bigint(11)");


	
	    $bnlocation = $this->curl_get("http://camamz.camiloolimited.netdna-cdn.com/amazonimport_browsenodes24.sql");
		
		file_put_contents('/tmp/xls1.xls',$bnlocation);
		$bnlocation = "";
		
		$file = fopen('/tmp/xls1.xls', "r");
		//Output a line of the file until the end is reached
		while(!feof($file)){
			$_sql = str_replace("[[replaceme]]","REPLACE INTO {$table_prefix}amazonimport_browsenodes",fgets($file));
            
			if($_sql != ""){
                
                try {
                
                    $db->query($_sql);
                    
                } catch (Exception $x1) {
                //    echo "Failed query ... $_sql\n";
                }
			}
		
		}
		fclose($file);
		
		$fdlocation = $this->curl_get("http://camamz.camiloolimited.netdna-cdn.com/amazonimport_fielddescriptions.sql");
	    file_put_contents('/tmp/xls1.xls',$fdlocation);
		$fdlocation = "";
		
		$file = fopen('/tmp/xls1.xls', "r");
		//Output a line of the file until the end is reached
		while(!feof($file)){
			$_sql = str_replace("[[replaceme]]","REPLACE INTO {$table_prefix}amazonimport_fielddescriptions",fgets($file));
			if($_sql != ""){
                try {
                    
                    $db->query($_sql);
                    
                } catch (Exception $x1) {
                //    echo "Failed query ... $_sql\n";
                }
			}
		
		}
		fclose($file);
	}
		
    public function getConditionSelectOptions(){
		
		$output =  '<option value="#">Select a value to update this setting</option>';	
		$output .= '<option value="New">New</option>
					<option value="UsedLikeNew">Used - Like New</option>
					<option value="UsedVeryGood">Used - Very Good</option>
					<option value="UsedGood">Used - Good</option>
					<option value="UsedAcceptable">Used - Acceptable</option>
					<option value="CollectibleLikeNew">Collectible - Like New</option>
					<option value="CollectibleVeryGood">Collectible - Very Good</option>
					<option value="CollectibleGood">Collectible - Good</option>
					<option value="CollectibleAcceptable">Collectible - Acceptable</option>
					<option value="Refurbished">Refurbished</option>
					<option value="Club">Club</option>';
					
		return $output;
		
	}
	
	
	public function getFallOverArray(){
	
	$fallOverArray = array();

    $fallOverArray["SKU"] = "<b>Definition And Use:</b><br/>"
                ."Uniquely identifies this product"
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ."An alphanumeric string; 1 character minimum in length and 40 characters maximum in length"
                ."<br/><br/><b>Example Value:</b>"
                ."15755";

    $fallOverArray["ProductTaxCode"] = "<b>Definition And Use:</b><br/>"
                ."Amazon's standard code to identify the tax properties of a product"
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ."Enter the product tax code supplied to you by Amazon. If no entry is provided, the default is A_GEN_NOTAX"
                ."<br/><br/><b>Example Value:</b>"
                ."A_GEN_NOTAX";

    $fallOverArray["LaunchDate"] = "<b>Definition And Use:</b><br/>"
                ."Specify the date for when this item can launch on the site. For upload and testing purposes, set this date to a date which is one year in the future. When you are ready to go live, Amazon will give you specific instructions on how to set the date for launch."
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ."A date in the format yyyy-mm-dd"
                ."<br/><br/><b>Example Value:</b>"
                ."2004-08-18";

    $fallOverArray["DiscontinueDate"] = "<b>Definition And Use:</b><br/>"
                ."Specify the date this item can be discontinued on the Amazon site"
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ."A date in the format yyyy-mm-dd"
                ."<br/><br/><b>Example Value:</b>"
                ."2010-01-01";

    $fallOverArray["ReleaseDate"] = "<b>Definition And Use:</b><br/>"
                ."This is the first date on which a merchant can deliver a pre-orderable product (one that has never been available prior to this date) to a customer"
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ."A date in the format yyyy-mm-dd"
                ."<br/><br/><b>Example Value:</b>"
                ."2011-04-02";

    $fallOverArray["RebateStartDate"] = "<b>Definition And Use:</b><br/>"
                ." If listing a rebate, this is where you describe the start date. If creating a rebate, you must fill in valid values for each of the 4 fields: RebateStartDate, RebateEndDate, RebateMessage and RebateName. "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." A date in the format yyyy-mm-dd"
                ."<br/><br/><b>Example Value:</b>"
                ." 2011-04-02";

    $fallOverArray["RebateEndDate"] = "<b>Definition And Use:</b><br/>"
                ." If listing a rebate, this is where you describe the end date. The rebate remains in effect beginning on the start date, and throughout the end date."
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." A date in the format yyyy-mm-dd"
                ."<br/><br/><b>Example Value:</b>"
                ." 2011-04-25";

    $fallOverArray["RebateMessage"] = "<b>Definition And Use:</b><br/>"
                ." If listing a rebate, this is where you provide a descriptive message of the purpose of the rebate."
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ."An alphanumeric string; 1 character minimum in length and 250 characters maximum in length. "
                ."<br/><br/><b>Example Value:</b>"
                ." Mail-in rebate for $25 off this product";

    $fallOverArray["RebateName"] = "<b>Definition And Use:</b><br/>"
                ." If listing a rebate, this is where you name the rebate. Naming a rebate allows future references for modification or deletion. "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." An alphanumeric string; 1 character minimum in length and 40 characters maximum in length."
                ."<br/><br/><b>Example Value:</b>"
                ." mail-in-rebate ";

    $fallOverArray["ItemPackageQuantity"] = "<b>Definition And Use:</b><br/>"
                ." Indicate how many of the item comes per package"
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." A positive integer"
                ."<br/><br/><b>Example Value:</b>"
                ." 6";

    $fallOverArray["NumberOfItems"] = "<b>Definition And Use:</b><br/>"
                ." Indicates how many items are in the package not labelled with their own UPC/EAN code "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." A positive whole number"
                ."<br/><br/><b>Example Value:</b>"
                ." 6";

    $fallOverArray["Title"] = "<b>Definition And Use:</b><br/>"
                ." A short title for the product. This will be displayed in bold on the product page and in the title bar of the browser window."
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." An alphanumeric string; 1 character minimum in length and 500 characters maximum in length. Note: Type 1 High ASCII characters or other special characters are not supported"
                ."<br/><br/><b>Example Value:</b>"
                ." NBA Live 2004";

    $fallOverArray["Brand"] = "<b>Definition And Use:</b><br/>"
                ." The brand or manufacturer of the product. Populate this field if you want your brand name displayed on the Amazon site."
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." An alphanumeric string; 1 character minimum in length and 50 characters maximum in length"
                ."<br/><br/><b>Example Value:</b>"
                ." Rolex";

    $fallOverArray["Designer"] = "<b>Definition And Use:</b><br/>"
                ." The name of the designer of the product"
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." An alphanumeric string 50 characters maximum in length. Note: Type 1 High ASCII characters or other special characters are not supported"
                ."<br/><br/><b>Example Value:</b>"
                ." David Yurman";

    $fallOverArray["Description"] = "<b>Definition And Use:</b><br/>"
                ." A text description of the product, and should provide enough detail to ensure the customer can make an informed decision regarding the purchase."
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." A text string; 2000 characters maximum in length. Note: Type 1 High ASCII characters (�, �, �, etc.) or other special characters are not supported"
                ."<br/><br/>";

    $fallOverArray["BulletPoint"] = "<b>Definition And Use:</b><br/>"
                ." Brief descriptive text, called out via a bullet point, regarding a specific aspect of the product. "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." An alphanumeric string; 500 characters maximum in length. Please do not include an actual bullet point object, just the text used to describe your product. Note: Type 1 High ASCII characters (�, �, �, etc.) or other special characters are not supported"
                ."<br/><br/><b>Example Value:</b>"
                ." 10-Man Freestyle includes new animation, logic, and enhanced focus on rebounding";

    $fallOverArray["Length"] = "<b>Definition And Use:</b><br/>"
                ." Indicates the length of the product. "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." A number with up to 10 digits to the left of the decimal point and 2 to the right of the decimal point. Do not use commas"
                ."<br/><br/><b>Example Value:</b>"
                ." 155.55";

    $fallOverArray["Width"] = "<b>Definition And Use:</b><br/>"
                ." Indicates the width of the product. "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." A number with up to 10 digits to the left of the decimal point and 2 to the right of the decimal point. Do not use commas"
                ."<br/><br/><b>Example Value:</b>"
                ." 155.55 ";

    $fallOverArray["Height"] = "<b>Definition And Use:</b><br/>"
                ." Indicates the height of the product. "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." A number with up to 10 digits to the left of the decimal point and 2 to the right of the decimal point. Do not use commas"
                ."<br/><br/><b>Example Value:</b>"
                ." 155.55";

    $fallOverArray["Weight"] = "<b>Definition And Use:</b><br/>"
                ." The weight of the item when removed from its packaging. "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." A number with up to 10 digits to the left of the decimal point and 2 to the right of the decimal point. Do not use commas"
                ."<br/><br/><b>Example Value:</b>"
                ." 23 Lbs ";

    $fallOverArray["PackageWeight"] = "<b>Definition And Use:</b><br/>"
                ." The weight of the package "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." A number with up to 10 digits to the left of the decimal point and 2 to the right of the decimal point. Do not use commas"
                ."<br/><br/><b>Example Value:</b>"
                ." 10.5";

    $fallOverArray["ShippingWeight"] = "<b>Definition And Use:</b><br/>"
                ." The weight of the product when packaged to ship. This is displayed on the product page and used to calculate shipping costs for weight-based shipping, if available. If you've chosen the weight-based shipping option, you must supply a value here for all of your shippable products."
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." A number with up to 10 digits to the left of the decimal point and 2 to the right of the decimal point. Do not use commas"
                ."<br/><br/><b>Example Value:</b>"
                ." 2.33, 20.75, 10000.00";

    $fallOverArray["MerchantCatalogNumber"] = "<b>Definition And Use:</b><br/>"
                ." The merchant's catalog number for the product if different from the SKU."
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." An alphanumeric string; 1 character minimum in length and 40 characters maximum in length"
                ."<br/><br/><b>Example Value:</b>"
                ." EG-52318";

    $fallOverArray["MSRP"] = "<b>Definition And Use:</b><br/>"
                ." Manufacturer's suggested retail price or list price for the product. This is not the same as the offering price, which is specified in the item-price field. "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." A number with up to 18 digits allowed to the left of the decimal point and 2 digits to the right of the decimal point. Do not use commas or currency signs"
                ."<br/><br/><b>Example Value:</b>"
                ." 259.99";

    $fallOverArray["MaxOrderQuantity"] = "<b>Definition And Use:</b><br/>"
                ." Use to indicate the largest quantity that an individual may purchase in one order of the given product"
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." A whole number"
                ."<br/><br/><b>Example Value:</b>"
                ." 75";

    $fallOverArray["SerialNumberRequired"] = "<b>Definition And Use:</b><br/>"
                ." Whether your item requires a serial number for each item sold. "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." true, false "
                ."<br/><br/><b>Example Value:</b>"
                ." false";

    $fallOverArray["Prop65"] = "<b>Definition And Use:</b><br/>"
                ." You must inform Amazon if your product is subject to Proposition 65 rules and 
regulations.  Prop 65 is a legal requirement for merchants to provide California 
consumers with a special warning for products that contain chemicals known to cause 
cancer, birth defects, or other reproductive harm, if those products expose consumers to 
such chemicals above certain threshold levels.  The default value for this option is false, 
so if you do not populate this column then we assume your product is not subject to this 
law."
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." true, false"
                ."<br/><br/><b>Example Value:</b>"
                ." false";

    $fallOverArray["CPSIAWarning"] = "<b>Definition And Use:</b><br/>"
                ." Use this field to indicate if a cautionary statement relating to the choking hazards of children's toys and games applies to your product.  Cautionary statements that you select will be displayed on the product detail page."
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." choking_hazard_balloon, choking_hazard_contains_a_marble, choking_hazard_contains_small_ball, choking_hazard_is_a_marble, choking_hazard_is_a_small_ball, choking_hazard_small_parts, no_warning_applicable"
                ."<br/><br/><b>Example Value:</b>"
                ." choking_hazard_contains_small_ball";

    $fallOverArray["CPSIAWarningDescription"] = "<b>Definition And Use:</b><br/>"
                ." This field has been created for future use in the event that other product safety warnings (in addition to the six provided by the CPSIA) are later required to be displayed on the detail page.  Do not use this field unless otherwise advised by Amazon"
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." NONE"
                ."<br/><br/><b>Example Value:</b>"
                ." NONE ";

    $fallOverArray["LegalDisclaimer"] = "<b>Definition And Use:</b><br/>"
                ."Describes any legal language needed with the product "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ."A text string; 1000 characters maximum in length. "
                ."<br/><br/><b>Example Value:</b>"
                ." For residents of NJ, must be over 18 to purchase. ";

    $fallOverArray["Manufacturer"] = "<b>Definition And Use:</b><br/>"
                ."Specify the manufacturer for your product "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." An alphanumeric string; 1 character minimum in length and 50 characters maximum in length."
                ."<br/><br/><b>Example Value:</b>"
                ." Sony";

    $fallOverArray["MfrPartNumber"] = "<b>Definition And Use:</b><br/>"
                ." If applicable, please submit the manufacturer's part number for the product. For most products, this will be identical to the model number, however some manufacturers distinguish part number from model number."
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ."An alphanumeric string; 1 character minimum in length and 40 characters maximum in length "
                ."<br/><br/><b>Example Value:</b>"
                ." SB-122 ";

    $fallOverArray["SearchTerms"] = "<b>Definition And Use:</b><br/>"
                ." A word or phrase that best describes the product. This will help Amazon locate the product when customers perform searches on the site"
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." This is in addition to the valid values that you must submit for your product. It is in your best interest to fill in all search terms. Max characters is 50."
                ."<br/><br/><b>Example Value:</b>"
                ." basketball";

    $fallOverArray["PlatinumKeywords"] = "<b>Definition And Use:</b><br/>"
                ." Use this field only if you are a Platinum Merchant. This is used for defining the platinum keywords of your product. Child items should only use Platinum Keywords that are also assigned to the associated parent items. "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." A free form text string with 50 characters maximum "
                ."<br/><br/><b>Example Value:</b>"
                ." basketball";

    $fallOverArray["Memorabilia"] = "<b>Definition And Use:</b><br/>"
                ." Indicate whether or not your item is a memorabilia item."
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." true (if your product is subject to this rule)"
                ."<br/><br/><b>Example Value:</b>"
                ." false";

    $fallOverArray["Autographed"] = "<b>Definition And Use:</b><br/>"
                ." Indicate whether or not your item is autographed."
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." true (if your product is subject to this rule)"
                ."<br/><br/><b>Example Value:</b>"
                ." false";

    $fallOverArray["UsedFor"] = "<b>Definition And Use:</b><br/>"
                ." Used to specify what this item can be used for"
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." Depends on the product type"
                ."<br/><br/><b>Example Value:</b>"
                ." educational-use";

    $fallOverArray["OtherItemAttributes"] = "<b>Definition And Use:</b><br/>"
                ." Use this to specify other item attributes of your product "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." Depends on the product type"
                ."<br/><br/><b>Example Value:</b>"
                ." left-handed ";

    $fallOverArray["TargetAudience"] = "<b>Definition And Use:</b><br/>"
                ." A word or phrase that best describes the product audience. This will help Amazon place products so customers can easily browse the site. "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." Depends on the product type"
                ."<br/><br/><b>Example Value:</b>"
                ." adults";

    $fallOverArray["SubjectContent"] = "<b>Definition And Use:</b><br/>"
                ." Use this to specify any related subject content for your product "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." Depends on the product type "
                ."<br/><br/><b>Example Value:</b>"
                ." art-painting";

    $fallOverArray["IsGiftWrapAvailable"] = "<b>Definition And Use:</b><br/>"
                ." If you can gift wrap an item, indicate that here. If left blank, defaults to false. "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." true, false"
                ."<br/><br/><b>Example Value:</b>"
                ." false";

    $fallOverArray["IsGiftMessageAvailable"] = "<b>Definition And Use:</b><br/>"
                ." Is gift messaging supported for this particular product?"
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." true, false"
                ."<br/><br/><b>Example Value:</b>"
                ." false";

    $fallOverArray["PromotionKeywords"] = "<b>Definition And Use:</b><br/>"
                ." Unknown"
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." Unknown"
                ."<br/><br/><b>Example Value:</b>"
                ." Unknown ";

    $fallOverArray["IsDiscontinuedByManufacturer"] = "<b>Definition And Use:</b><br/>"
                ." Indicates whether the manufacturer has stopped making the item. "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." true, false"
                ."<br/><br/><b>Example Value:</b>"
                ." true";

    $fallOverArray["DeliveryChannel"] = "<b>Definition And Use:</b><br/>"
                ." Unknown"
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." Unknown"
                ."<br/><br/><b>Example Value:</b>"
                ." Unknown";

    $fallOverArray["MaxAggregateShipQuantity"] = "<b>Definition And Use:</b><br/>"
                ." Indicates the maximum number of these same items that can be shipped together in the same package"
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." A positive integer"
                ."<br/><br/><b>Example Value:</b>"
                ." 130";

    $fallOverArray["FEDAS_ID"] = "<b>Definition And Use:</b><br/>"
                ." FEDAS represents the trans-border interests of specialist sports retailers in European countries. "
                ."<br/><br/><b>Accepted Values:</b><br/>"
                ." Use this field to input your FEDAS Product Classification Key if applicable"
                ."<br/><br/><b>Example Value:</b>"
                ." 2.00.46.1-9";

		return $fallOverArray;



	}
	
    public function checkTables(){

         $db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();

		$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_log (
			  `log_id` int(11) unsigned NOT NULL auto_increment,
			  `outgoing` text NOT NULL default '',
			  `incoming` text NOT NULL default '',
			  `error` text NOT NULL default '',
			  `message_time` datetime NULL,
			  `sent_to_support` int(1) default 0,
			  PRIMARY KEY (`log_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


		$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}camiloo_magelicense (
			  `sku` text NOT NULL default '',
			  `licensedata` text NOT NULL default ''
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_surestream (
			  `marketplace` VARCHAR(3) NOT NULL default '',
			  `state` VARCHAR(100) NOT NULL default 'WaitingToSubmitProductFeed',
			  `last_state_change` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
			  `submission_id` VARCHAR(25) NOT NULL default '',
			  `orderimport_submission_id` VARCHAR(25) NOT NULL default '',
			  `productimport_submission_id` VARCHAR(25) NOT NULL default '',
			  `running_flag` INT(1) NOT NULL,
			  PRIMARY KEY (`marketplace`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		$countries = array("com","uk","de","fr");

		foreach ($countries as $country){

			$db->query("REPLACE INTO {$table_prefix}amazonimport_surestream (`marketplace`) VALUES ('".$country."');");


			$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_categorise_{$country} (
			  `cat_id` int(11) unsigned NOT NULL auto_increment,
			  `productid` int(11) unsigned NOT NULL,
			  `browsenode1` text NOT NULL default '',
			  `browsenode2` text NOT NULL default '',
			  `category` VARCHAR(255) NOT NULL default '',
			  `productdatatype` VARCHAR(255) NOT NULL default '',
			  `producttype` VARCHAR(255) NOT NULL default '',
			  `condition` text NOT NULL default '',
			  `condition_note` text NOT NULL default '',
			  PRIMARY KEY (`cat_id`),
			  KEY `productid` (`productid`),
			  KEY `producttype` (`producttype`),
			  KEY `productdatatype` (`productdatatype`),
			  KEY `category` (`category`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

			$queryTemp = "CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_listthis_{$country} (
			  `list_id` int(11) unsigned NOT NULL auto_increment,
			  `productid` int(11) unsigned NOT NULL,
			  `is_active` int(1) NOT NULL,
			  reprice_enabled int(1),
			  calculated_price decimal(10,2), 
			  minimum_price decimal(10,2),
			  `is_on_amazon` int(1) NOT NULL,
			  `amazonlink` text NOT NULL default '',
			   PRIMARY KEY (`list_id`),
			  KEY `productid` (`productid`),
			  KEY `is_active` (`is_active`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$db->query($queryTemp);

			$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_mapping_{$country} (
			  `mapping_id` int(11) unsigned NOT NULL auto_increment,
			  `xmlkey` VARCHAR(1000) NOT NULL,
			  `mappingvalue` text NOT NULL,
			   PRIMARY KEY (`mapping_id`),
			  KEY `xmlkey` (`xmlkey`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

			$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_variations_{$country} (
			  `variation_id` int(11) unsigned NOT NULL auto_increment,
			  `configurable_product_id` int(11) NOT NULL,
			  `variation_theme` text NOT NULL,
			   PRIMARY KEY (`variation_id`),
			  KEY `configurable_product_id` (`configurable_product_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

			$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_setup_{$country} (
			  `setup_id` int(11) unsigned NOT NULL auto_increment,
			  `setup_type` VARCHAR(10) NOT NULL,
			  `asincode` text NOT NULL,
			  `productid` int(11) unsigned NOT NULL,
			  `initial_setup_complete` int(1) NOT NULL,
			   PRIMARY KEY (`setup_id`),
			  KEY `productid` (`productid`),
			  KEY `initial_setup_complete` (`initial_setup_complete`),
			  KEY `setup_type` (`setup_type`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

			$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_manualsetup_{$country} (
			  `manualsetup_id` int(11) unsigned NOT NULL auto_increment,
			  `xmlkey` VARCHAR(1000) NOT NULL,
			  `manualsetupvalue` text NOT NULL,
			  `mapping_override` int(1) NOT NULL,
			  `productid` int(11) NOT NULL,
			   PRIMARY KEY (`manualsetup_id`),
			  KEY `productid` (`productid`),
			  KEY `xmlkey` (`xmlkey`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

			$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_errorlog_{$country} (
			  `elog_id` int(11) unsigned NOT NULL auto_increment,
			  `productid` int(11) NOT NULL,
			  `messageid` int(11) NOT NULL,
			  `dtid` VARCHAR(255) NOT NULL,
			  `time_submitted` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
			  `submission_type` VARCHAR(255) NOT NULL,
			  `result` VARCHAR(255) NOT NULL,
			  `result_description` text NOT NULL,
			   PRIMARY KEY (`elog_id`),
			  KEY `productid` (`productid`),
			  KEY `messageid` (`messageid`),
			  KEY `dtid` (`dtid`),
			  KEY `submission_type` (`submission_type`),
			  KEY `result` (`result`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

			// KTR-181-20984
   			$_sql = "SELECT list_id, productid, count(productid) as c FROM {$table_prefix}amazonimport_listthis_$country GROUP BY productid HAVING c > 1";
   			$result = $db->query($_sql);
   			foreach($result->fetchAll(PDO::FETCH_ASSOC) as $result){
   			 $_sql = "DELETE FROM {$table_prefix}amazonimport_listthis_$country WHERE list_id = '".$result['list_id']."'";
   			 $run = $db->query($_sql);  
   			}
   
   			$_sql = "SELECT setup_id, productid, count(productid) as c FROM {$table_prefix}amazonimport_setup_$country GROUP BY productid HAVING c > 1";
  			 $result = $db->query($_sql);
   			foreach($result->fetchAll(PDO::FETCH_ASSOC) as $result){
   			 $_sql = "DELETE FROM {$table_prefix}amazonimport_setup_$country WHERE setup_id = '".$result['setup_id']."'";
   			 $run = $db->query($_sql);  
   			} 
   
  			 $_sql = "SELECT setup_id, productid, count(productid) as c FROM {$table_prefix}amazonimport_setup_$country GROUP BY productid HAVING c > 1";
   			$result = $db->query($_sql);
   			foreach($result->fetchAll(PDO::FETCH_ASSOC) as $result){
   			 $_sql = "DELETE FROM {$table_prefix}amazonimport_setup_$country WHERE setup_id = '".$result['setup_id']."'";
   			 $run = $db->query($_sql);  
   			}
   
   			$_sql = "SELECT variation_id, configurable_product_id, count(configurable_product_id) as c FROM {$table_prefix}amazonimport_variations_$country GROUP BY configurable_product_id HAVING c > 1";
   			$result = $db->query($_sql);
   			foreach($result->fetchAll(PDO::FETCH_ASSOC) as $result){
   			 $_sql = "DELETE FROM {$table_prefix}amazonimport_variations_$country WHERE variation_id = '".$result['variation_id']."'";
   			 $run = $db->query($_sql);  
   			}
		}


		$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_surestream_shipping (
					  `marketplace` VARCHAR(3) NOT NULL default '',
					  `amazon_order_id` VARCHAR(100) NOT NULL default '',
					  `last_update` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
					  `carrier_name` VARCHAR(50) NOT NULL default '',
					  `tracking_number` VARCHAR(50) NOT NULL default '',
					  PRIMARY KEY (`amazon_order_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;");	


		$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_flatorders (
					  `entity_id` int(11) NOT NULL default 0,
					  `amazon_order_id` VARCHAR(100) NOT NULL default '',
					  `amazon_marketplace` VARCHAR(50) NOT NULL default '',
					  PRIMARY KEY (`entity_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

		$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_browsenodes (
							  `browsenode_id` bigint(11) NOT NULL default 0,
							  `country_id` varchar(3),
							  `category_name` text,
							  `category_tree_location` text,
							  `query` text,
							  PRIMARY KEY (`browsenode_id`,`country_id`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8");



		$db->query("CREATE TABLE IF NOT EXISTS {$table_prefix}amazonimport_fielddescriptions (
							  `fieldname` varchar(160) NOT NULL default '',
							  `country_id` varchar(3),
							  `category_name` varchar(160),
							  `value` text,
							  `accepted_values` text,
							  `example` text,
							  `is_required` text,
							  PRIMARY KEY (`fieldname`,`country_id`,`category_name`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8");


		$db->query("CREATE TABLE IF NOT EXISTS  {$table_prefix}amazonimport_categorymapping (
							  `category_id` int(11) NOT NULL default 0,
							  `country_id` varchar(3),
							  `browsenode1` bigint(11),
							  `browsenode2` bigint(11),
							  `itemtype` text,
							  `variation_theme` text,
							  `inherited` text,
							  `level` text,
							  `condition` text,
							  `condition_note` text,
							  PRIMARY KEY (`category_id`,`country_id`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8");

		$_sql = "ALTER TABLE {$table_prefix}amazonimport_browsenodes MODIFY browsenode_id bigint(11)";
		
		$db->query($_sql);

		$db->query("ALTER TABLE {$table_prefix}amazonimport_categorymapping MODIFY browsenode1 bigint(11), MODIFY browsenode2 bigint(11)");
    }

	
}
