<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 */

class Slmp_Moneris_Model_Source_USVDJAVSlist
{
    /**
     * Prepare AVS list
     *
     * @return array
     */
    public function toOptionArray()
    {
		$options = array();
		$options[] = array('value'=>'A', 'label'=>'A - Address matches, ZIP does not. Acquirer rights not implied.');
		$options[] = array('value'=>'B', 'label'=>'B - Street addresses match. Zip code not verified due to incompatible formats. (Acquirer sent both street address and zip code.)');
		$options[] = array('value'=>'C', 'label'=>'C - Street addresses not verified due to incompatible formats. (Acquirer	 sent both street address and zip code.)');
		$options[] = array('value'=>'D', 'label'=>'D - Street addresses and zip codes	match.');
		$options[] = array('value'=>'F', 'label'=>'F - Street address and zip code	 match. Applies to U.K. only');
		$options[] = array('value'=>'G', 'label'=>'G - Address information not	 verified for international transaction. Issuer is not an AVS participant, or AVS data was present in the request but issuer did not return an AVS result, or Visa performs AVS on behalf of the issuer and there was no address record on file for this account.');
		$options[] = array('value'=>'I', 'label'=> 'I - Address information not	 verified.');
		$options[] = array('value'=>'M', 'label'=> 'M - Street address and zip code	 match.');
		$options[] = array('value'=>'N', 'label'=> 'N - No match. Acquirer sent	 postal/ZIP code only, or street address only, or both zip code and street address. Also used when acquirer	 requests AVS but sends no AVS data.');
		$options[] = array('value'=>'P', 'label'=>'P - Zip code match. Acquirer sent both zip code and street address but street address not verified due to incompatible formats.');
		$options[] = array('value'=>'R', 'label'=>'R - Retry: system unavailable or	timed out. Issuer ordinarily performs AVS but was unavailable. The code R is used by Visa when issuers are unavailable. Issuers should refrain from using this code.');
		$options[] = array('value'=>'U', 'label'=>'U - Address not verified for	 domestic transaction. Issuer is not an AVS	 participant, or AVS data was present in the request but issuer did not	 return an AVS result, or Visa performs AVS on behalf of the issuer	 and there was no address record on file for this account.');
		$options[] = array('value'=>'W', 'label'=>'W - Not applicable. If present, replaced with Z by Visa. Available for U.S. issuers only.');
		$options[] = array('value'=>'Y', 'label'=>'Y - Street address and zip code	 match.');
		$options[] = array('value'=>'Z', 'label'=>'Z - Postal/Zip matches; street address does not match or street address not included in request.');
		return $options;
        
    }
}
