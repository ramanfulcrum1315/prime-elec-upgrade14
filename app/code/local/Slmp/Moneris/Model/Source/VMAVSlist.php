<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 */

class Slmp_Moneris_Model_Source_VMAVSlist
{
    /**
     * Prepare CVD list
     *
     * @return array
     */
    public function toOptionArray()
    {
		$options = array();
		$options[] = array('value'=>'A', 'label'=>'A - Street addresses match. The street addresses match but the postal/ZIP codes do not, or the request does not include the postal/ZIP code.');
		$options[] = array('value'=>'B', 'label'=>'B - Street addresses match. Postal code not verified due to incompatible	formats. (Acquirer sent both street address and postal code.)');
		$options[] = array('value'=>'C', 'label'=>'C - Street address and postal code not verified due to incompatible formats.	(Acquirer sent both street address and postal code.)');
		$options[] = array('value'=>'D', 'label'=>'D - Street addresses and postal	codes match.');
		$options[] = array('value'=>'G', 'label'=>'G - Address information not	verified for international transaction.');
		$options[] = array('value'=>'I', 'label'=> 'I - Address information not	verified.');
		$options[] = array('value'=>'M', 'label'=> 'M - Street address and postal code match.');
		$options[] = array('value'=>'N', 'label'=> 'N - No match. Acquirer sent	postal/ZIP code only, or street address only, or both postal code and street address.');
		$options[] = array('value'=>'P', 'label'=>'P - Postal code match. Acquirer sent both postal code and street address,but street address not verified due to incompatible formats.');
		$options[] = array('value'=>'R', 'label'=>'R - Retry: System unavailable or	timed out. Issuer ordinarily performs its	own AVS but was unavailable. Available for U.S. issuers only.');
		$options[] = array('value'=>'S', 'label'=>'S - Not applicable. If present,replaced with G (for international) or U (for domestic) by V.I.P. Available for U.S. Issuers only.');
		$options[] = array('value'=>'U', 'label'=>'U - Address not verified for	domestic transaction. Visa tried to perform	check on issue behalf but no AVS information was available on record,issuer is not an AVS participant, or AVS data was present in the request but issuer did not return an AVS result.');
		$options[] = array('value'=>'W', 'label'=>'W - Not applicable. If present,replaced with Z by V.I.P. Available for U.S.issuers only.');
		$options[] = array('value'=>'X', 'label'=> 'Y - Not applicable. If present,replaced with Y by V.I.P. Available for U.S.	issuers only.');
		$options[] = array('value'=>'Y', 'label'=>'Y - Street address and postal code	match.');
		$options[] = array('value'=>'Z', 'label'=>'Z - Postal/ZIP matches; street	address does not match or street address not included in request.');
		return $options;
        
    }
}
