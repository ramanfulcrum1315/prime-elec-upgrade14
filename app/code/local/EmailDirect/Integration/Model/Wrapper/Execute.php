<?php
/*
 * Created on Oct 31, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class EmailDirect_Integration_Model_Wrapper_Execute
{
	private $apiKey = null;


	public function sendCommand($command,$subcommand = null, $id= null,$xmldata=null,$put = false)
	{
		if(!$this->apiKey) {
			$this->_getApiKey();
		}
		//Mage::log("Send data: ".$xmldata);
		$URL = Mage::getStoreConfig('emaildirect/general/urls/accesspoint');
		$urlsuffix = Mage::getStoreConfig('emaildirect/general/urls/'.$command);
		$URL .= $urlsuffix;
		if($id) {
			$URL .= "/$id";
		}
		if($subcommand) {
			$URL .= "/$subcommand";
		}
		$header = array('Content-Type: text/xml','ApiKey: '.$this->apiKey,'Accept: application/xml');
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		if($put) {
			$putString = stripslashes($xmldata);
			$putData = tmpfile();
			fwrite($putData, $putString);
			fseek($putData, 0);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_PUT, true);
			curl_setopt($ch, CURLOPT_INFILE, $putData);
			curl_setopt($ch, CURLOPT_INFILESIZE, strlen($putString));
		}
		else {
			curl_setopt($ch, CURLOPT_POST, 0);
			if($xmldata) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $xmldata);
			}
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		//Mage::log("Received data: ".$output);
		curl_close($ch);
		$start = strpos($output,'<?xml',true);
		if(!$start) {
			$start = strpos($output,'<Response>',true);
		}
		$strxml = substr($output,$start);
		try {
			$xml = simplexml_load_string($strxml);
		}
		catch(Exception $e) {
			Mage::throwException($e->getMessage());
		}
		return $xml;
	}
	private function _getApiKey()
	{
		$path = 'emaildirect/general/apikey';
		$this->apiKey = Mage::getStoreConfig($path);
	}
}
