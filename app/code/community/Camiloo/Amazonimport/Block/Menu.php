<?php
class Camiloo_Amazonimport_Block_Menu  extends Mage_Adminhtml_Block_Page_Menu
{
	
    public function getMenuArray()
    {
        //Load standard menu
        $parentArr = parent::getMenuArray();
	
		// BUGFIX FNZ-263-60001-2
		// Major issue reported in that admins without access 
		// to the module cannot access admin area
		
		if(isset($parentArr['amazon'])){
		
		// load Marketplace and Merchant ID values.
		$USAmid = Mage::getStoreConfig('amazonint/amazoncom/mid');
		$UKmid = Mage::getStoreConfig('amazonint/amazonuk/mid');
		$FRmid = Mage::getStoreConfig('amazonint/amazonfr/mid');
		$DEmid = Mage::getStoreConfig('amazonint/amazonde/mid');
		
		// remove any marketplaces which are not set up yet.
		$marketplacecount = 4;
		$potentialonlyone = array();
				
		if($USAmid == ""){
			unset($parentArr['amazon']['children']['amazoncom']);
			$marketplacecount = $marketplacecount - 1;
		}else{
			$potentialonlyone[] = $parentArr['amazon']['children']['amazoncom'];
		}
		
		if($UKmid == ""){
			unset($parentArr['amazon']['children']['amazonuk']);
			$marketplacecount = $marketplacecount - 1;
		}else{
			$potentialonlyone[] = $parentArr['amazon']['children']['amazonuk'];
		}
		
		if($FRmid == ""){
			unset($parentArr['amazon']['children']['amazonfr']);
			$marketplacecount = $marketplacecount - 1;
		}else{
			$potentialonlyone[] = $parentArr['amazon']['children']['amazonfr'];
		}
		if($DEmid == ""){
			unset($parentArr['amazon']['children']['amazonde']);
			$marketplacecount = $marketplacecount - 1;
		}else{
			$potentialonlyone[] = $parentArr['amazon']['children']['amazonde'];
		}
		
		$parentArr['amazon']['children']['licensing']['last'] = false;
		
		$licenseXml = $this->getRemoteXMLFileData("http://service.camiloo.co.uk/inc/modules/licensing.php?sku=CAM-AMZ20&domain=".Mage::getStoreConfig('web/secure/base_url'));
		
		
		if(is_object($licenseXml) && ((string) $licenseXml->licensetype == "lifetime")){
			unset($parentArr['amazon']['children']['licensing']);	
			if($marketplacecount == 0){
			$parentArr['amazon']['children']['amzintro'] = array(
				'label' => 'View Welcome Guide',
				'active' => true ,
				'sort_order' => 0,
				'click' => "window.open(this.href, 'View Welcome Guide at ' + this.href); return false;",
				'url' => 'http://help.camiloo.co.uk/index.php?/article/AA-00256/26/Main-Knowledgebase/Global-Amazon-Integration-Version-2.20.html',
				'level' => 2,
				'last' => true,
				'children' => array()
			);
		}else{
			$parentArr['amazon']['children']['amzintro'] = array(
				'label' => 'Help & Support',
				'active' => true ,
				'sort_order' => 0,
				'click' => "window.open(this.href, 'View Welcome Guide at ' + this.href); return false;",
				'url' => 'http://support.camiloo.co.uk/',
				'level' => 2,
				'last' => true,
				'children' => array()
			);
		}
			
			
		}else if(is_object($licenseXml) && ((string) $licenseXml->licensetype == "monthly")){
			$parentArr['amazon']['children']['licensing']['label'] = "Purchase Lifetime License";
			if($marketplacecount == 0){
			$parentArr['amazon']['children']['amzintro'] = array(
				'label' => 'View Welcome Guide',
				'active'=>true ,
				'sort_order'=> 0,
				'click' => "window.open(this.href, 'View Welcome Guide at ' + this.href); return false;",
				'url'=>'http://support.camiloo.co.uk/',
				'level'=>2,
				'last'=> true,
				'children' => array()
			);
		}else{
			$parentArr['amazon']['children']['amzintro'] = array(
				'label' => 'Help & Support',
				'active'=>true ,
				'sort_order'=> 0,
				'click' => "window.open(this.href, 'View Welcome Guide at ' + this.href); return false;",
				'url'=>'http://support.camiloo.co.uk/',
				'level'=>2,
				'last'=> true,
				'children' => array()
			);
		}
			
		}else if(is_object($licenseXml) && ((string) $licenseXml->licensetype == "trial")){
			
			$diff = $licenseXml->ddiff;
			$parentArr['amazon']['children']['licensing']['label'] = $diff." trial days remaining. Click here to purchase license";
			if($marketplacecount == 0){
			$parentArr['amazon']['children']['amzintro'] = array(
				'label' => 'View Welcome Guide',
				'active'=>true ,
				'sort_order'=> 0,
				'click' => "window.open(this.href, 'View Welcome Guide at ' + this.href); return false;",
				'url'=>'http://help.camiloo.co.uk/index.php?/article/AA-00256/26/Main-Knowledgebase/Global-Amazon-Integration-Version-2.20.html',
				'level'=>2,
				'last'=> true,
				'children' => array()
			);
		}else{
			$parentArr['amazon']['children']['amzintro'] = array(
				'label' => 'Help & Support',
				'active'=>true ,
				'sort_order'=> 0,
				'click' => "window.open(this.href, 'View Welcome Guide at ' + this.href); return false;",
				'url'=>'http://support.camiloo.co.uk/',
				'level'=>2,
				'last'=> true,
				'children' => array()
			);
		}
			
			
		}else if(is_object($licenseXml) && ((string) $licenseXml->licensetype == "expired")){
			$parentArr['amazon']['children']['licensing']['label'] = "Your trial has expired.<br />Click to purchase.";
			$parentArr['amazon']['children']['licensing']['last'] = true;
			// unset all other menu items.
			unset($parentArr['amazon']['children']['logging']);
			unset($parentArr['amazon']['children']['status']);
			unset($parentArr['amazon']['children']['amazonuk']);
			unset($parentArr['amazon']['children']['amazonde']);
			unset($parentArr['amazon']['children']['amazonfr']);
			unset($parentArr['amazon']['children']['amazoncom']);
		}else{
			$parentArr['amazon']['children']['licensing']['label'] = "To begin, click here to register for your free trial license.";
			$parentArr['amazon']['children']['licensingactive'] = $parentArr['amazon']['children']['licensing'];
			$parentArr['amazon']['children']['licensingactive']['label'] = "Then click here to activate your trial";
			$parentArr['amazon']['children']['licensingactive']['last'] = true;
			$parentArr['amazon']['children']['licensingactive']['url'] = $parentArr['amazon']['children']['licensingactive']['url']."?activatetrial=true";
			// unset all other menu items.
			unset($parentArr['amazon']['children']['logging']);
			unset($parentArr['amazon']['children']['status']);
			unset($parentArr['amazon']['children']['amazonuk']);
			unset($parentArr['amazon']['children']['amazonde']);
			unset($parentArr['amazon']['children']['amazonfr']);
			unset($parentArr['amazon']['children']['amazoncom']);
		}
		
		}
	 
        return $parentArr;
    }
	
	public function getRemoteXMLFileData($urltograb){
		// this function gets the requested data
		$session = curl_init("$urltograb");
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($session, CURLOPT_TIMEOUT, 60);
		$result = curl_exec($session);
		curl_close($session);
		return simplexml_load_string($result,'SimpleXMLElement', LIBXML_NOCDATA);
	}
}
