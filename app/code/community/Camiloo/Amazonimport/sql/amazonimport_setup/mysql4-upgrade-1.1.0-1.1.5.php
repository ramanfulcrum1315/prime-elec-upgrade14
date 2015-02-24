<?php

$installer = $this;

$installer->startSetup();


	$tpl = 	array(	'type'              => 'text',
							'backend'           => '',
							'frontend'          => '',
							'label'             => 'Search terms separated by commas',
							'input'             => 'textarea',
							'class'             => '',
							'source'            => '',
							'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
							'visible'           => true,
							'required'          => false,
							'user_defined'      => false,
							'default'           => '',
							'searchable'        => false,
							'filterable'        => false,
							'comparable'        => false,
							'visible_on_front'  => false,
							'unique'            => false,
							'apply_to'          => '',
							'is_configurable'   => false
						);
	


	$countrycode = "_us";	
	$installer->addAttribute('catalog_product', 'amazonsearchterms'.$countrycode, $tpl);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amazonsearchterms'.$countrycode, $tpl);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amazonsearchterms'.$countrycode, $tpl);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amazonsearchterms'.$countrycode, $tpl);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amazonsearchterms'.$countrycode, $tpl);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amazonsearchterms'.$countrycode, $tpl);
	
	
	$tpl['label'] = 'Platinum keywords separated by commas';
		
	$countrycode = "_us";	
	$installer->addAttribute('catalog_product', 'amazonplatinumkeys'.$countrycode, $tpl);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amazonplatinumkeys'.$countrycode, $tpl);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amazonplatinumkeys'.$countrycode, $tpl);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amazonplatinumkeys'.$countrycode, $tpl);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amazonplatinumkeys'.$countrycode, $tpl);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amazonplatinumkeys'.$countrycode, $tpl);
	
	
	$tpl['input'] = 'select';
	$tpl['label'] = 'Select an attribute to map to Manufacturer';
	$tpl['source'] = 'amazonimport/source_attribute';
	
	
	$tpl['label'] = 'Select an attribute to map to Brand Name';
	$countrycode = "_us";
	$installer->addAttribute('catalog_product', 'amz_brand_name'.$countrycode, $tpl);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amz_brand_name'.$countrycode, $tpl);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amz_brand_name'.$countrycode, $tpl);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amz_brand_name'.$countrycode, $tpl);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amz_brand_name'.$countrycode, $tpl);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amz_brand_name'.$countrycode, $tpl);
	
	
	
	$tpl['label'] = 'Select an attribute to map to Model Number';
	
	$countrycode = "_us";
	$installer->addAttribute('catalog_product', 'amz_model_number'.$countrycode, $tpl);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amz_model_number'.$countrycode, $tpl);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amz_model_number'.$countrycode, $tpl);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amz_model_number'.$countrycode, $tpl);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amz_model_number'.$countrycode, $tpl);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amz_model_number'.$countrycode, $tpl);
	
		
	$tpl['label'] = 'Select an attribute to map to Shipping Weight';
	
	$countrycode = "_us";
	$installer->addAttribute('catalog_product', 'amz_shippingweight'.$countrycode, $tpl);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amz_shippingweight'.$countrycode, $tpl);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amz_shippingweight'.$countrycode, $tpl);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amz_shippingweight'.$countrycode, $tpl);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amz_shippingweight'.$countrycode, $tpl);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amz_shippingweight'.$countrycode, $tpl);
	
	
	$tpl['label'] = 'Weight Unit';
	$tpl['source'] = 'amazonimport/source_shippingweightaddition';
		
	$countrycode = "_us";
	$installer->addAttribute('catalog_product', 'amz_weightaddon'.$countrycode, $tpl);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amz_weightaddon'.$countrycode, $tpl);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amz_weightaddon'.$countrycode, $tpl);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amz_weightaddon'.$countrycode, $tpl);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amz_weightaddon'.$countrycode, $tpl);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amz_weightaddon'.$countrycode, $tpl);
	
	
	$tpl['source'] = 'amazonimport/source_attribute';
	$tpl['label'] = 'Select an attribute to map to Size';
	
	$countrycode = "_us";
	$installer->addAttribute('catalog_product', 'amz_size'.$countrycode, $tpl);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amz_size'.$countrycode, $tpl);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amz_size'.$countrycode, $tpl);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amz_size'.$countrycode, $tpl);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amz_size'.$countrycode, $tpl);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amz_size'.$countrycode, $tpl);
	
	
	$tpl['label'] = 'Select an attribute to map to Colour';
	
	$countrycode = "_us";
	$installer->addAttribute('catalog_product', 'amz_color'.$countrycode, $tpl);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amz_color'.$countrycode, $tpl);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amz_color'.$countrycode, $tpl);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amz_color'.$countrycode, $tpl);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amz_color'.$countrycode, $tpl);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amz_color'.$countrycode, $tpl);
	
// okay, now we need to add each attribute to the attribute sets in the correct groups for that attribute.
foreach ($installer->getAllAttributeSetIds('catalog_product') as $attributeSetId) {
	
	$countrycode = "_us";
	$asn = 'Amazon USA Listing';
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonsearchterms'.$countrycode),10);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonplatinumkeys'.$countrycode),20);	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_brand_name'.$countrycode),40);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_model_number'.$countrycode),50);	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_shippingweight'.$countrycode),60);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_weightaddon'.$countrycode),70);		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_size'.$countrycode),80);		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_color'.$countrycode),90);
	
	$countrycode = "_ca";
	$asn = 'Amazon Canada Listing';
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonsearchterms'.$countrycode),10);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonplatinumkeys'.$countrycode),20);	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_brand_name'.$countrycode),40);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_model_number'.$countrycode),50);	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_shippingweight'.$countrycode),60);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_weightaddon'.$countrycode),70);		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_size'.$countrycode),80);		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_color'.$countrycode),90);
	
	$countrycode = "_uk";
	$asn = 'Amazon UK Listing';
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonsearchterms'.$countrycode),10);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonplatinumkeys'.$countrycode),20);	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_brand_name'.$countrycode),40);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_model_number'.$countrycode),50);	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_shippingweight'.$countrycode),60);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_weightaddon'.$countrycode),70);		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_size'.$countrycode),80);		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_color'.$countrycode),90);
	
	$countrycode = "_fr";	
	$asn = 'Amazon France Listing';
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonsearchterms'.$countrycode),10);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonplatinumkeys'.$countrycode),20);	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_brand_name'.$countrycode),40);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_model_number'.$countrycode),50);	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_shippingweight'.$countrycode),60);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_weightaddon'.$countrycode),70);		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_size'.$countrycode),80);		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_color'.$countrycode),90);
	
	
	$countrycode = "_de";
	$asn = 'Amazon Germany Listing';
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonsearchterms'.$countrycode),10);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonplatinumkeys'.$countrycode),20);	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_brand_name'.$countrycode),40);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_model_number'.$countrycode),50);	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_shippingweight'.$countrycode),60);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_weightaddon'.$countrycode),70);		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_size'.$countrycode),80);		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_color'.$countrycode),90);
	
	$countrycode = "_jp";
	$asn = 'Amazon Japan Listing';
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonsearchterms'.$countrycode),10);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonplatinumkeys'.$countrycode),20);	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_brand_name'.$countrycode),40);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_model_number'.$countrycode),50);	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_shippingweight'.$countrycode),60);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_weightaddon'.$countrycode),70);		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_size'.$countrycode),80);		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amz_color'.$countrycode),90);
	
	
}


$installer->installEntities();

$installer->endSetup(); 

?>