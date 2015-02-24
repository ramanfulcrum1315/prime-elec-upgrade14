<?php
/**
* Rule for Moneris payment gateway process
*/

Mage::app();
$payment_country_file = Mage::getStoreConfig('payment/moneris/payment_country');
if($payment_country_file == 'CA') {
	 require('Slmp/Moneris/lib/mpgClasses.php');
	}else{
     require('Slmp/Moneris/lib/us_mpgClasses.php');
	}


class Slmp_Moneris_Model_PaymentMethod extends Mage_Payment_Model_Method_Cc
{

	function __construct() {
	/** 
	* Constants 
	**/
	if($this->getConfigData('payment_country') == 'CA') {
		if($this->getConfigData('secure_3d')){
			  $this->TRANSACTION_PREAUTH    = 'cavv_preauth';
			} else{
			   $this->TRANSACTION_PREAUTH    = 'preauth';
			}
		 $this->TRANSACTION_COMPLETION = 'completion';
	} else {
			if($this->getConfigData('secure_3d')){
			   $this->TRANSACTION_PREAUTH  = 'us_cavv_preauth';
			  } else{
			   $this->TRANSACTION_PREAUTH  = 'us_preauth';
		   	}
	   $this->TRANSACTION_COMPLETION = 'us_completion';
	}
		 
	}
	const ERROR_CODE_LIMIT 	     = 50;
	const ERROR_CODE_UPPER_LIMIT = 1000;
	const CRYPT_TYPE			 = 7; // SSL enabled merchant	
	
	/**
	* Human readable errors from Monoris' CSV file
	* 
	*
	* @var array
	*/
	protected $_errors = array(
		'50' => 'Decline',
		'51' => 'Expired Card',
		'52' => 'PIN retries exceeded',
		'53' => 'No sharing',
		'54' => 'No security module',
		'55' => 'Invalid transaction',
		'56' => 'No Support',
		'57' => 'Lost or stolen card',
		'58' => 'Invalid status',
		'59' => 'Restricted Card',
		'60' => 'No Chequing account',
		'60' => 'No Savings account',
		'61' => 'No PBF',
		'62' => 'PBF update error',
		'63' => 'Invalid authorization type',
		'64' => 'Bad Track 2',
		'65' => 'Adjustment not allowed',
		'66' => 'Invalid credit card advance increment',
		'67' => 'Invalid transaction date',
		'68' => 'PTLF error',
		'69' => 'Bad message error',
		'70' => 'No IDF',
		'71' => 'Invalid route authorization',
		'72' => 'Card on National NEG file ',
		'73' => 'Invalid route service (destination)',
		'74' => 'Unable to authorize',
		'75' => 'Invalid PAN length',
		'76' => 'Low funds',
		'77' => 'Pre-auth full',
		'78' => 'Duplicate transaction',
		'79' => 'Maximum online refund reached',
		'80' => 'Maximum offline refund reached',
		'81' => 'Maximum credit per refund reached',
		'82' => 'Number of times used exceeded',
		'83' => 'Maximum refund credit reached',
		'84' => 'Duplicate transaction - authorization number has already been corrected by host. ',
		'85' => 'Inquiry not allowed',
		'86' => 'Over floor limit ',
		'87' => 'Maximum number of refund credit by retailer',
		'88' => 'Place call',
		'89' => 'CAF status inactive or closed',
		'90' => 'Referral file full',
		'91' => 'NEG file problem',
		'92' => 'Advance less than minimum',
		'93' => 'Delinquent',
		'94' => 'Over table limit',
		'95' => 'Amount over maximum',
		'96' => 'PIN required',
		'97' => 'Mod 10 check failure',
		'98' => 'Force Post',
		'99' => 'Bad PBF',
		'100' => 'Unable to process transaction',
		'101' => 'Place call',
		'102' => 'Place call',
		'103' => 'NEG file problem',
		'104' => 'CAF problem',
		'105' => 'Card not supported',
		'106' => 'Amount over maximum',
		'107' => 'Over daily limit',
		'108' => 'CAF Problem',
		'109' => 'Advance less than minimum',
		'110' => 'Number of times used exceeded',
		'111' => 'Delinquent',
		'112' => 'Over table limit',
		'113' => 'Timeout',
		'115' => 'PTLF error',
		'121' => 'Administration file problem',
		'122' => 'Unable to validate PIN: security module down',
		'150' => 'Merchant not on file',
		'200' => 'Invalid account',
		'201' => 'Incorrect PIN',
		'202' => 'Advance less than minimum',
		'203' => 'Administrative card needed',
		'204' => 'Amount over maximum ',
		'205' => 'Invalid Advance amount',
		'206' => 'CAF not found',
		'207' => 'Invalid transaction date',
		'208' => 'Invalid expiration date',
		'209' => 'Invalid transaction code',
		'210' => 'PIN key sync error',
		'212' => 'Destination not available',
		'251' => 'Error on cash amount',
		'252' => 'Debit not supported',
		'426' => 'AMEX - Denial 12',
		'427' => 'AMEX - Invalid merchant',
		'429' => 'AMEX - Account error',
		'430' => 'AMEX - Expired card',
		'431' => 'AMEX - Call Amex',
		'434' => 'AMEX - Call 03',
		'435' => 'AMEX - System down',
		'436' => 'AMEX - Call 05',
		'437' => 'AMEX - Declined',
		'438' => 'AMEX - Declined',
		'439' => 'AMEX - Service error',
		'440' => 'AMEX - Call Amex',
		'441' => 'AMEX - Amount error',
		'475' => 'CREDIT CARD - Invalid expiration date',
		'476' => 'CREDIT CARD - Invalid transaction, rejected',
		'477' => 'CREDIT CARD - Refer Call',
		'478' => 'CREDIT CARD - Decline, Pick up card, Call',
		'479' => 'CREDIT CARD - Decline, Pick up card',
		'480' => 'CREDIT CARD - Decline, Pick up card',
		'481' => 'CREDIT CARD - Decline',
		'482' => 'CREDIT CARD - Expired Card',
		'483' => 'CREDIT CARD - Refer',
		'484' => 'CREDIT CARD - Expired card - refer',
		'485' => 'CREDIT CARD - Not authorized',
		'486' => 'CREDIT CARD - CVV Cryptographic error',
		'487' => 'CREDIT CARD - Invalid CVV',
		'489' => 'CREDIT CARD - Invalid CVV',
		'490' => 'CREDIT CARD - Invalid CVV',
		'800' => 'Bad format',
		'801' => 'Bad data',
		'802' => 'Invalid Clerk ID',
		'809' => 'Bad close ',
		'810' => 'System timeout',
		'811' => 'System error',
		'821' => 'Bad response length',
		'877' => 'Invalid PIN block',
		'878' => 'PIN length error',
		'880' => 'Final packet of a multi-packet transaction',
		'881' => 'Intermediate packet of a multi-packet transaction',
		'889' => 'MAC key sync error',
		'898' => 'Bad MAC value',
		'899' => 'Bad sequence number - resend transaction',
		'900' => 'Capture - PIN Tries Exceeded',
		'901' => 'Capture - Expired Card',
		'902' => 'Capture - NEG Capture',
		'903' => 'Capture - CAF Status 3',
		'904' => 'Capture - Advance < Minimum',
		'905' => 'Capture - Num Times Used',
		'906' => 'Capture - Delinquent',
		'907' => 'Capture - Over Limit Table',
		'908' => 'Capture - Amount Over Maximum',
		'909' => 'Capture - Capture',
		'960' => 'Initialization failure - merchant number mismatch',
		'961' => 'Initialization failure - pinpad  mismatch',
		'963' => 'No match on Poll code',
		'964' => 'No match on Concentrator ID' ,
		'965' => 'Invalid software version' ,
		'966' => 'Duplicate terminal name',
		'1M' => 'Match' ,
		'1Y' => 'Match for AmEx' ,
		'1N' => 'No Match',
		'1P' => 'Not Processed' ,
		'1S' => 'CVD should be on the card, but Merchant has indicated that CVD is not present' ,
		'1R' => 'Retry for AmEx' ,
		'1U' => 'Issuer is not a CVD participant',
		'0' => 'CAVV authentication results invalid',
		'1' => 'CAVV failed validation;authentication' ,
		'2' => 'CAVV passed validation;authentication',
		'3' => 'CAVV passed validation;attempt' ,
		'4' => 'CAVV failed validation; attempt' ,
		'7' => 'CAVV failed validation; attempt (US issued cards only)' ,
		'8' => 'CAVV passed validation; attempt(US issued cards only' ,
		'9' => 'CAVV failed validation; attempt(US issued cards only)' ,
		'A' => 'CAVV passed validation; attempt(US issued cards only)' ,
		'B' => 'CAVV passed validation;information only, no liability shift' 
	);


    /**
    * unique internal payment method identifier
    *
    * @var string [a-z0-9_]
    */
    protected $_code = 'moneris';

	/**
	* Magento defined flags
	*/

    /**
     * Is this payment method a gateway (online auth/charge) ?
     */
    protected $_isGateway               = true;

    /**
     * Can authorize online?
     */
    protected $_canAuthorize            = true;

    /**
     * Can capture funds online?
     */
    protected $_canCapture              = true;

    /**
     * Can capture partial amounts online?
     */
    protected $_canCapturePartial       = false;

    /**
     * Can refund online?
     */
    protected $_canRefund               = true;

    /**
     * Can void transactions online?
     */
    protected $_canVoid                 = false;

    /**
     * Can use this payment method in administration panel?
     */
    protected $_canUseInternal          = true;

    /**
     * Can show this payment method as an option on checkout payment page?
     */
    protected $_canUseCheckout          = true;

    /**
     * Is this payment method suitable for multi-shipping checkout?
     */
    protected $_canUseForMultishipping  = true;

    /**
     * Can save credit card information for future processing?
     */
    protected $_canSaveCc = false;

   /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return boolean
     */
   
	protected $_allowCurrencyCode = array('USD');
	public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->getAcceptedCurrencyCodes())) {
            return false;
        }
        return true;
    }

    /**
     * Return array of currency codes supplied by Payment Gateway
     *
     * @return array
     */
    public function getAcceptedCurrencyCodes()
    {
        if (!$this->hasData('_accepted_currency')) {
            $acceptedCurrencyCodes = $this->_allowCurrencyCode;
            $acceptedCurrencyCodes[] = $this->getConfigData('currency');
            $this->setData('_accepted_currency', $acceptedCurrencyCodes);
        }
        return $this->_getData('_accepted_currency');
    }

    /**
     * Authorize a payment for future capture
	 * 
     * @var Variant_Object $payment 
	 * @var Float $amount
     */
	public function authorize(Varien_Object $payment, $amount)
	{	
	
		$error = false;
		$sessThreeDSecure = Mage::getSingleton('customer/session')->getThreeDSecure();
		// check for payment
		if($amount > 0){
			
		
			$payment->setAmount($amount);
			$order         = $payment->getOrder();
			$billing       = $order->getBillingAddress();
			$shipping      = $order->getShippingAddress();
			$streetName    = $billing->getStreet(2);		
			
			//check 19 characters
			if(strlen($streetName) > 19){
				$streetName = substr($billing->getStreet(2) , 0 , 19);
			}

			if($payment->getCcType() == 'AE') {
				$avsTemplate = array('avs_zipcode' => $billing->getPostcode(), 'avs_street_number' => $billing->getStreet(1),'avs_street_name' => $streetName,'avs_email' => $billing->getEmail(),'avs_hostname' => $_SERVER['SERVER_NAME'], 'avs_browser' => $_SERVER['HTTP_USER_AGENT'], 'avs_shiptocountry' =>$shipping->getCountry(), 'avs_custip' => $_SERVER["REMOTE_ADDR"],'avs_custphone' => $shipping->getTelephone());

			} else {
				$avsTemplate = array('avs_zipcode' => $billing->getPostcode(), 'avs_street_number' => $streetNos,'avs_street_name' => $streetName);
			}

		   //Getting and transfering AVS information begin
			$mpgAvsInfo = new mpgAvsInfo($avsTemplate);
			

			/* CVD Checked Condition and create Array */
			if($this->getConfigData('useccv')) {
				$cvd_array= explode(",",$this->getConfigData('cvd_response'));
				$cvdTemplate = array('cvd_value' => $payment->getCcCid(),'cvd_indicator' => 1);
				$mpgCvdInfo = new mpgCvdInfo($cvdTemplate);
			}

			$transaction = $this->_build($payment, $this->TRANSACTION_PREAUTH);
			$transaction->setAvsInfo($mpgAvsInfo);

			/* CVD Checked Condition and create XML */
			if($this->getConfigData('useccv')) {
			  $transaction->setCvdInfo($mpgCvdInfo);
			}
			
			$response = $this->_send($transaction);
			 $payment->setCcApproval($response->getReceiptId())
					 ->setLastTransId($response->getReceiptId())
					 ->setCcTransId($response->getTxnNumber())
					 ->setCcAvsStatus($response->getAuthCode())
					 ->setApproval($response->getAuthCode())
					 ->setCcCidStatus($response->getResponseCode());
			/*
			 COMMENT BY RAMENDRA
			
			$payment->setCcApproval($response->getReceiptId())
             ->setLastTransId($response->getReceiptId())
             ->setCcTransId($response->getTxnNumber())
             ->setCcAvsStatus($response->getAuthCode())
		    ->setCcCidStatus($response->getResponseCode());      */
		
					/* Checked CVD Response */
					/*$getCvdResultCode = 'M';
					if((count($cvd_array)>0) && ($this->getConfigData('useccv') == 1)) {
						if(!in_array($getCvdResultCode, $cvd_array)) {
						  $error = Mage::helper('paygate')->__('CVD Code Not Match.');
						} else{
						   $message = 'CVD Response: ' . Mage::helper('paygate')->__($this->_errors[$getCvdResultCode]);
						   Mage::getSingleton('core/session')->addSuccess($message);
						}
					} */
					 if($this->getConfigData('payment_country') == 'CA') {
							if($response->getResponseCode() > 0 && $response->getResponseCode() <= self::ERROR_CODE_LIMIT) {
									
						// Add message to success page begin
								$message = '';

								//Gettind cvd array
								$cvdArr = explode(",", $this->getConfigData('cvd_response'));
								
								//Getting cavv array
								$cavvArr = explode(",",$this->getConfigData('cavv_response_code'));

								//Extracting cvd value from response
								$cvdValue = substr($response->getCvdResultCode(), 1,1);

								//Getting avs array for Visa/Mastercard
								$vmArr = explode(",", $this->getConfigData('avs_vm_response_code'));

								//Getting avs array for Discover
								$dArr = explode(",", $this->getConfigData('avs_d_response_code'));

								//Getting avs array for American Express/JCB
								$ajArr = explode(",", $this->getConfigData('avs_aj_response_code'));
								
								//Avs checking begin

								if( !(in_array($response->getAvsResultCode(), $vmArr)) && ($payment->getCcType() == 'VI' || $payment->getCcType() == 'MC') && $this->getConfigData('avs_status') == 1) {
								
									$message.= 'AVS Response: AVS for Visa/Master Card does not match.';

								}

								if( !(in_array($response->getAvsResultCode(), $dArr)) && $payment->getCcType() == 'DI' && $this->getConfigData('avs_status') == 1) {
								
									$message.= 'AVS Response: AVS for Discovery does not match.';

								}

								if( !(in_array($response->getAvsResultCode(), $ajArr)) && $payment->getCcType() == 'AE' && $this->getConfigData('avs_status') == 1) {
								
									$message.= "AVS Response: AVS for American Express does not match.";

								}

								//Avs checking end

								//Cavv check begin
								if( !(in_array($response->getCavvResultCode(), $cavvArr)) && $this->getConfigData('secure_3d') == 1 && $sessThreeDSecure == '1') {
								
									$message.= "\n CAVV Response: CAVV does not match.";

								}
								//Cavv check end

								//Cvd check begin
								if( !(in_array($cvdValue, $cvdArr)) && $this->getConfigData('useccv') == 1) {

									$message.= "\n CVD Response: CVD does not match.";

								}
								//Cvd check end

								//Checking error string
								if($message != '') {
									
									$message.= "\n PLEASE CHECK YOUR PAYMENT INFORMATION.";	
									$this->capture($payment, 0);
									$error = Mage::helper('paygate')->__($message);
																	
								} else {
									
									//checking for Auth and Capture
									if($this->getConfigData('payments_type') == 'authorize_capture'){
											$this->capture($payment, $amount);
									 } else {
									     $payment->setStatus(self::STATUS_APPROVED);
									 }
								}
								
						// Add message to success page end

							  } else if($response->getResponseCode() > self::ERROR_CODE_LIMIT && $response->getResponseCode() < self::ERROR_CODE_UPPER_LIMIT) {
								$error = Mage::helper('paygate')->__($this->_errors[$response->getResponseCode()]);
							  } else {
								$error = Mage::helper('paygate')->__('Incomplete transaction.');
							 }
					} else {
							if($response->getResponseCode() <= self::ERROR_CODE_LIMIT) {
														   
							   // Add message to success page begin
								$message = '';

								//Gettind cvd array
								$cvdArr = explode(",", $this->getConfigData('cvd_response'));
								
								//Getting cavv array
								$cavvArr = explode(",",$this->getConfigData('cavv_response_code'));

								//Extracting cvd value from response
								$cvdValue = substr($response->getCvdResultCode(), 1,1);

								//Getting avs array for Visa/Dicover/JCB
								$vdjArr = explode(",", $this->getConfigData('us_avs_vdj_response_code'));

								//Getting avs array for American Express
								$aArr = explode(",", $this->getConfigData('us_avs_a_response_code'));

								//Getting avs array for Mastercard
								$mArr = explode(",", $this->getConfigData('avs_m_response_code'));
								
								//Avs checking begin

								if( !(in_array($response->getAvsResultCode(), $vdjArr)) && ($payment->getCcType() == 'VI' || $payment->getCcType() == 'DI') && $this->getConfigData('avs_status') == 1) {
								
									$message.= "AVS Response: AVS for Visa/Discovery does not match.";

								}

								if( !(in_array($response->getAvsResultCode(), $aArr)) && $payment->getCcType() == 'AE' && $this->getConfigData('avs_status') == 1) {
								
									$message.= "AVS Response: AVS for American Express does not match.";

								}

								if( !(in_array($response->getAvsResultCode(), $mArr)) && $payment->getCcType() == 'MC' && $this->getConfigData('avs_status') == 1) {
								
									$message.= "AVS Response: AVS for Master Card does not match.";

								}

								//Avs checking end
					
								//Cavv check begin
								if( !(in_array($response->getCavvResultCode(), $cavvArr)) && $this->getConfigData('secure_3d') == 1) {
								
									$message.= "\n CAVV Response: CAVV does not match.";

								}
								//Cavv check end

								//Cvd check begin
								if( !(in_array($cvdValue, $cvdArr)) && $this->getConfigData('useccv') == 1) {

									$message.= "\n CVD Response: CVD does not match.";

								}
								//Cvd check end


								//Checking error string
								if($message != '') {
									
									$message.= "\n PLEASE CHECK YOUR PAYMENT INFORMATION.";	
									$this->capture($payment, 0);
									$error = Mage::helper('paygate')->__($message);
																	
								} else {
									
									//checking for Auth and Capture
									if($this->getConfigData('payments_type') == 'authorize_capture'){
											$this->capture($payment, $amount);
									 } else {
									     $payment->setStatus(self::STATUS_APPROVED);
									 }
								}
								// Add message to success page end
							} else if($response->getResponseCode() > self::ERROR_CODE_LIMIT && $response->getResponseCode() < self::ERROR_CODE_UPPER_LIMIT) {
							$error = Mage::helper('paygate')->__($this->_errors[$response->getResponseCode()]);
							} else {
							$error = Mage::helper('paygate')->__('Incomplete transaction.');
							}
				   }

		} else{
		    $error = Mage::helper('paygate')->__('Invalid amount for authorization.');
		}
	
		// we've got something bad here.
		if ($error !== false) 
		    Mage::throwException($error);
	
		return $this;
	}
	
	/**
	* Capture the authorized transaction for a specific order
	*
    * @var Variant_Object $payment 
	* @var Float $amount
    */
	public function capture(Varien_Object $payment, $amount) {		
		$error = false;
		
		// check for payment
		if($amount > 0){
			$payment->setAmount($amount);

		
			// Map magento keys to moneris way
			$transaction = $this->_build($payment, $this->TRANSACTION_COMPLETION);
			
			$response = $this->_send($transaction);
			if($this->getConfigData('payment_country') == 'CA') {
				if($response->getResponseCode() > 0 && $response->getResponseCode() <= self::ERROR_CODE_LIMIT) {

					 $payment->setStatus(self::STATUS_SUCCESS);

				} else if($response->getResponseCode() > self::ERROR_CODE_LIMIT && $response->getResponseCode() < self::ERROR_CODE_UPPER_LIMIT) {

					$error = Mage::helper('paygate')->__($this->_errors[$response->getResponseCode()]);

				} else {

					$error = Mage::helper('paygate')->__('Incomplete transaction.');

				}
			} else {

				if($response->getResponseCode() <= self::ERROR_CODE_LIMIT) {

				$payment->setStatus(self::STATUS_SUCCESS);

			} else if($response->getResponseCode() > self::ERROR_CODE_LIMIT && $response->getResponseCode() < self::ERROR_CODE_UPPER_LIMIT) {

				$error = Mage::helper('paygate')->__($this->_errors[$response->getResponseCode()]);

			} else {

				$error = Mage::helper('paygate')->__('Incomplete transaction.');

			}
			}
		} else{

		  
			$payment->setAmount(0.00);
			
			// Zero dollar completion call
			$transaction = $this->_build($payment, $this->TRANSACTION_COMPLETION);	
			$this->_send($transaction);
			return;

		}


		// we've got something bad here.
		if ($error !== false) 
		    Mage::throwException($error);

		return $this;
		
	}
	
	/**
	* Void a transaction, 
	* This is not yet implemented in this module so you need to do them in the moneris control
	* panel.
	*/
	public function void(Varien_Object $payment) {
		Mage::throwException("Not yet implemented");
		return $this;			
	}
	
	/******************************************************************************/
	/** Custom methods	*/
	
	/**
	* Receive a moneris transaction object and send it to the moneris webservice
	*
	* @var mpgTransaction $transaction
	*/
	public function _send(mpgTransaction $transaction) {
		
		$store_id  = $this->getConfigData('store_id');
		$api_token = $this->getConfigData('api_token');
		$test_mode   = $this->getConfigData('tests');
		$request   = new mpgRequest($transaction);

		$mpgHttpsPost  = new mpgHttpsPost($test_mode,$store_id,$api_token,$request);
		return $mpgHttpsPost->getMpgResponse();
	}
	
	/**
	* Build a moneris transaction object the data of moneris
	* Make sure the transaction object is the appropriate type for the current
	* step.
	*
	* @var Varien_Object $payment
	* @var string $type
	*/
	public function _build(Varien_Object $payment, $type) {
		$order    = $payment->getOrder();	
		$billing  = $order->getBillingAddress();
		$shipping = $order->getShippingAddress();
		$mpgCustInfo = new mpgCustInfo(); //Customer Information Object

		//Shipping array
		$shippingInf = array(
				 'first_name' => $shipping->getFirstname(),
                 'last_name' => $shipping->getLastname(),
                 'company_name' => $shipping->getCompany(),
                 'address' => implode(",", $shipping->getStreet()),
                 'city' => $shipping->getCity(),
                 'province' => $shipping->getRegion(),
                 'postal_code' => $shipping->getPostcode(),
                 'country' => $shipping->getCountry(),
                 'phone_number' => $shipping->getTelephone(),
                 'fax' => $shipping->getFax(),
				 'shipping_cost' => $order->getShippingAmount()
                 );
		
		$mpgCustInfo->setShipping($shippingInf);
		$mpgCustInfo->setEmail($shipping->getEmail());
		
		//Billing array
		$billingInf = array(
				 'first_name' => $billing->getFirstname(),
                 'last_name' => $billing->getLastname(),
                 'company_name' => $billing->getCompany(),
                 'address' => implode(",", $shipping->getStreet()),
                 'city' => $billing->getCity(),
                 'province' => $billing->getRegion(),
                 'postal_code' => $billing->getPostcode(),
                 'country' => $billing->getCountry(),
                 'phone_number' => $billing->getTelephone(),
                 'fax' => $billing->getFax(),
                 'shipping_cost' => $order->getShippingAmount()
                 );
		$mpgCustInfo->setBilling($billingInf);

		//get all items array
		$i = 0;
		$items_in_cart =  Mage::getSingleton('checkout/session')->getQuote()->getAllItems();			
		foreach ($items_in_cart as $item) { 

			$items[] = array(
			'name'=> $item->getName(),
			'quantity'=> $item->getQty(),
			'product_code'=> $item->getProductId(),
			'extended_amount'=> ($item->getQty() * $item->getPrice())

		   );
		   $mpgCustInfo->setItems($items[$i]);
		   $i++;
		}
		/*3D Secure create cavv transaction array */
		$secure_3d = $this->getConfigData('secure_3d');
		if($secure_3d){
		   $cavv_code = Mage::getSingleton('customer/session', array('name' => 'frontend'));
		   $transaction_cavv =   array('cavv' =>	$cavv_code->getMyCustomData(),);
        }else{
			$transaction_cavv = array();
			}
		# Should be only used in the developement environment
		# without it we get duplicate order id.
		$token = '11';
		//$token = (empty($token)) ? "" : "-" . $token;
		$token = (empty($token)) ? "" : "-" .time().rand(5, 15);

		if(($type == 'completion' || $type == 'us_completion') && ($token != ''))
		{
			$transaction =   array('type'		 =>	$type,
				'order_id'	 =>	$payment->getCcApproval(),
				'crypt_type' =>	self::CRYPT_TYPE,
			);
		}
		else
		{
		
			 $transaction = array(
				'type'		 =>	$type,
				'order_id'	 =>	$order->getIncrementId() . $token,
				'crypt_type' =>	self::CRYPT_TYPE,
			);
		 
		}
							
		switch($type) {
			case $this->TRANSACTION_PREAUTH :
				$transaction = $transaction + $transaction_cavv + array(
							'cust_id'	 =>	$billing->getCustomerId(),
							'amount'	 =>	sprintf("%01.2f", $payment->getAmount()),
							'pan'		 =>	$this->_cleanCC($payment->getCcNumber()),
							'expdate'	 =>	$this->_formatExpirationDate($payment->getCcExpYear(), $payment->getCcExpMonth()),
				);
				
				break;
			case $this->TRANSACTION_COMPLETION :
				$transaction = $transaction + array(
					'comp_amount' => sprintf("%01.2f", $payment->getAmount()),
					'txn_number'  => $payment->getCcTransId(),						
				);
				
				break;
			case self::TRANSACTION_VOID :
				$transaction = $transaction + array(
					'comp_amount' => sprintf("%01.2f", $payment->getAmount()),
					'txn_number'  => $payment->getCcTransId(),
				);
				break;

		}

	    $mpgTxn = new mpgTransaction($transaction); //Transaction Object
		$mpgTxn->setCustInfo($mpgCustInfo);
		return $mpgTxn;
	}
	
	/**
	* Clean the CC number, make sure its only digit.
	*
	* @var string $cc
	*/
	public function _cleanCC($cc) {
		return preg_replace('/[^\d]/', '', $cc);
	}
	
	/**
	* Format the expiration date for the moneris webservice
	*
	* The year is in two digit format
	* 2013-14 become 13-14
	*
	* @var string $year
	* @var string $month
	*/
	public function _formatExpirationDate($year, $month) {
		$year = substr($year, 2, 2); // use two year digits.
			
		return sprintf("%s%02d", $year, $month);
	}
}