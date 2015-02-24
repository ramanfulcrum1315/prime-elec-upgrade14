<?php

$installer = $this;

$installer->startSetup();


/// Availability on Amazon XX			[availableonamazon_xx]
	
	// to save on code, lets set up an array we can then tweak to meet each countries needs.
	$available = 	array(	'type'              => 'int',
							'backend'           => '',
							'frontend'          => '',
							'label'             => 'List this product?',
							'input'             => 'select',
							'class'             => '',
							'source'            => 'eav/entity_attribute_source_boolean',
							'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
							'visible'           => true,
							'required'          => false,
							'user_defined'      => false,
							'default'           => '1',
							'searchable'        => false,
							'filterable'        => false,
							'comparable'        => false,
							'visible_on_front'  => false,
							'unique'            => false,
							'apply_to'          => '',
							'is_configurable'   => false
						);
	
	// and a two letter country code to use throughout.
	$countrycode = "_us";
	
	$installer->addAttribute('catalog_product', 'availableonamazon'.$countrycode, $available);

	$available['label'] = 'List this product?';
	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'availableonamazon'.$countrycode, $available);

	$available['label'] = 'List this product?';
	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'availableonamazon'.$countrycode, $available);

	$available['label'] = 'List this product?';
	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'availableonamazon'.$countrycode, $available);
		
	$available['label'] = 'List this product?';
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'availableonamazon'.$countrycode, $available);

	$available['label'] = 'List this product?';
	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'availableonamazon'.$countrycode, $available);


/// Category ID on Amazon x				[amazoncategorycode_xx]

	$categoryid = 	array(	'type'              => 'text',
							'backend'           => '',
							'frontend'          => '',
							'label'             => 'Category ID',
							'input'             => 'text',
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
	

/// Amazon x Markup Percent				[AmazonXXMarkupPercent]

	// as this is also a text field, we can just reuse the field setup.
	
	$categoryid['label'] = 'Percentage Markup';
	$countrycode = "_us";	
	$installer->addAttribute('catalog_product', 'amazonpcentmarkup'.$countrycode, $categoryid);

	$categoryid['label'] = 'Percentage Markup';
	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amazonpcentmarkup'.$countrycode, $categoryid);

	$categoryid['label'] = 'Percentage Markup';
	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amazonpcentmarkup'.$countrycode, $categoryid);

	$categoryid['label'] = 'Percentage Markup';
	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amazonpcentmarkup'.$countrycode, $categoryid);
		
	$categoryid['label'] = 'Percentage Markup';
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amazonpcentmarkup'.$countrycode, $categoryid);

	$categoryid['label'] = 'Percentage Markup';
	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amazonpcentmarkup'.$countrycode, $categoryid);

/// Amazon x Markup Amount				[AmazonXXMarkupAmount]

	// we need to modify the input type to price for the next two attribute sets...
	$categoryid['input'] = 'price';
	// done... carry on.
	
	$categoryid['label'] = 'Additional Value Markup';
	$countrycode = "_us";	
	$installer->addAttribute('catalog_product', 'amazonvaluemarkup'.$countrycode, $categoryid);

	$categoryid['label'] = 'Additional Value Markup';
	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amazonvaluemarkup'.$countrycode, $categoryid);

	$categoryid['label'] = 'Additional Value Markup';
	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amazonvaluemarkup'.$countrycode, $categoryid);

	$categoryid['label'] = 'Additional Value Markup';
	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amazonvaluemarkup'.$countrycode, $categoryid);
		
	$categoryid['label'] = 'Additional Value Markup';
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amazonvaluemarkup'.$countrycode, $categoryid);

	$categoryid['label'] = 'Additional Value Markup';
	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amazonvaluemarkup'.$countrycode, $categoryid);

/// Amazon x Custom Price				[AmazonXXCustomPrice]

	$categoryid['label'] = 'If price source is custom, enter price';
	$countrycode = "_us";	
	$installer->addAttribute('catalog_product', 'amazoncustomprice'.$countrycode, $categoryid);

	$categoryid['label'] = 'If price source is custom, enter price';
	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amazoncustomprice'.$countrycode, $categoryid);

	$categoryid['label'] = 'If price source is custom, enter price';
	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amazoncustomprice'.$countrycode, $categoryid);

	$categoryid['label'] = 'If price source is custom, enter price';
	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amazoncustomprice'.$countrycode, $categoryid);
		
	$categoryid['label'] = 'If price source is custom, enter price';
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amazoncustomprice'.$countrycode, $categoryid);

	$categoryid['label'] = 'If price source is custom, enter price';
	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amazoncustomprice'.$countrycode, $categoryid);


/// Amazon x Custom Description			[AmazonXXCustomDesc]

	// next, we need text areas.
	$categoryid['input'] = 'textarea';
	// done... carry on.
		
	$categoryid['label'] = 'If description source is custom, enter here';
	$countrycode = "_us";	
	$installer->addAttribute('catalog_product', 'amazoncustomdesc'.$countrycode, $categoryid);

	$categoryid['label'] = 'If description source is custom, enter here';
	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amazoncustomdesc'.$countrycode, $categoryid);

	$categoryid['label'] = 'If description source is custom, enter here';
	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amazoncustomdesc'.$countrycode, $categoryid);

	$categoryid['label'] = 'If description source is custom, enter here';
	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amazoncustomdesc'.$countrycode, $categoryid);
		
	$categoryid['label'] = 'If description source is custom, enter here';
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amazoncustomdesc'.$countrycode, $categoryid);

	$categoryid['label'] = 'If description source is custom, enter here';
	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amazoncustomdesc'.$countrycode, $categoryid);

/// Amazon x Custom Name				[AmazonXXCustomName]

	// text again...
	$categoryid['input'] = 'text';
	// done... carry on.
		
	$categoryid['label'] = 'If product name source is custom, enter here';
	$countrycode = "_us";	
	$installer->addAttribute('catalog_product', 'amazoncustomname'.$countrycode, $categoryid);

	$categoryid['label'] = 'If product name source is custom, enter here';
	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amazoncustomname'.$countrycode, $categoryid);

	$categoryid['label'] = 'If product name source is custom, enter here';
	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amazoncustomname'.$countrycode, $categoryid);

	$categoryid['label'] = 'If product name source is custom, enter here';
	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amazoncustomname'.$countrycode, $categoryid);
		
	$categoryid['label'] = 'If product name source is custom, enter here';
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amazoncustomname'.$countrycode, $categoryid);

	$categoryid['label'] = 'If product name source is custom, enter here';
	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amazoncustomname'.$countrycode, $categoryid);


/// Amazon ISBN / UPC / EAN / ASIN		[asin_xx]

	// hmm.. does this need to be per store? Yes, because ASIN may be.
	
	$categoryid['label'] = 'Product Identifier';
	$countrycode = "_us";	
	$installer->addAttribute('catalog_product', 'asin'.$countrycode, $categoryid);

	$categoryid['label'] = 'Product Identifier';
	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'asin'.$countrycode, $categoryid);

	$categoryid['label'] = 'Product Identifier';
	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'asin'.$countrycode, $categoryid);

	$categoryid['label'] = 'Product Identifier';
	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'asin'.$countrycode, $categoryid);
		
	$categoryid['label'] = 'Product Identifier';
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'asin'.$countrycode, $categoryid);

	$categoryid['label'] = 'Product Identifier';
	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'asin'.$countrycode, $categoryid);
	
	//////////////////////	UPDATE 25/10/2009 MN
	/*			We also need the following values per store:
				asintype_				=>		asin	isbn	ean		upc
				fulfillmentlatency_
				allowspecialprices_
				amazonnamesource_		=>		magento		custom		amazon
				amazondescsource_		=>		magento_short	 magento	custom		amazon
				amazonimagesource_		=>		magento		amazon
				amazoncurrencysource_	=>		magento		custom								
				amazoncustomcurrency_	=>		use currency source object 				*/
				
	
	
	$categoryid['label'] = 'Average number of days before shipment';
	$countrycode = "_us";	
	$installer->addAttribute('catalog_product', 'fulfillmentlatency'.$countrycode, $categoryid);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'fulfillmentlatency'.$countrycode, $categoryid);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'fulfillmentlatency'.$countrycode, $categoryid);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'fulfillmentlatency'.$countrycode, $categoryid);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'fulfillmentlatency'.$countrycode, $categoryid);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'fulfillmentlatency'.$countrycode, $categoryid);
	
	////////////////////
	
	$categoryid['input'] = 'select';
	$categoryid['label'] = 'If price source is Magento, follow special price rules?';
	$categoryid['source'] = 'eav/entity_attribute_source_boolean';
	$countrycode = "_us";
	$installer->addAttribute('catalog_product', 'allowspecialprices'.$countrycode, $categoryid);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'allowspecialprices'.$countrycode, $categoryid);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'allowspecialprices'.$countrycode, $categoryid);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'allowspecialprices'.$countrycode, $categoryid);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'allowspecialprices'.$countrycode, $categoryid);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'allowspecialprices'.$countrycode, $categoryid);
	
	//////////	Now we need to make custom sources.
	
	$categoryid['label'] = 'Product Identifier Type';
	$categoryid['source'] = 'amazonimport/source_asintype';
	$countrycode = "_us";
	$installer->addAttribute('catalog_product', 'asintype'.$countrycode, $categoryid);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'asintype'.$countrycode, $categoryid);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'asintype'.$countrycode, $categoryid);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'asintype'.$countrycode, $categoryid);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'asintype'.$countrycode, $categoryid);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'asintype'.$countrycode, $categoryid);
	
	
	$categoryid['label'] = 'Amazon Product Name Source';
	$categoryid['source'] = 'amazonimport/source_amazonpn';
	$countrycode = "_us";
	$installer->addAttribute('catalog_product', 'amazonnamesource'.$countrycode, $categoryid);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amazonnamesource'.$countrycode, $categoryid);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amazonnamesource'.$countrycode, $categoryid);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amazonnamesource'.$countrycode, $categoryid);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amazonnamesource'.$countrycode, $categoryid);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amazonnamesource'.$countrycode, $categoryid);
	
	
	$categoryid['label'] = 'Amazon Product Description Source';
	$categoryid['source'] = 'amazonimport/source_amazonpd';
	$countrycode = "_us";
	$installer->addAttribute('catalog_product', 'amazondescsource'.$countrycode, $categoryid);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amazondescsource'.$countrycode, $categoryid);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amazondescsource'.$countrycode, $categoryid);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amazondescsource'.$countrycode, $categoryid);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amazondescsource'.$countrycode, $categoryid);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amazondescsource'.$countrycode, $categoryid);
	
	
	$categoryid['label'] = 'Amazon Product Image Source';
	$categoryid['source'] = 'amazonimport/source_amazonpi';
	$countrycode = "_us";
	$installer->addAttribute('catalog_product', 'amazonimagesource'.$countrycode, $categoryid);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amazonimagesource'.$countrycode, $categoryid);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amazonimagesource'.$countrycode, $categoryid);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amazonimagesource'.$countrycode, $categoryid);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amazonimagesource'.$countrycode, $categoryid);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amazonimagesource'.$countrycode, $categoryid);
	
	
	$categoryid['label'] = 'Product Price Currency Source';
	$categoryid['source'] = 'amazonimport/source_amazonppc';
	$countrycode = "_us";
	$installer->addAttribute('catalog_product', 'amazoncurrencysource'.$countrycode, $categoryid);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amazoncurrencysource'.$countrycode, $categoryid);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amazoncurrencysource'.$countrycode, $categoryid);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amazoncurrencysource'.$countrycode, $categoryid);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amazoncurrencysource'.$countrycode, $categoryid);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amazoncurrencysource'.$countrycode, $categoryid);
	
	
	$categoryid['label'] = 'Product Price Source';
	$categoryid['source'] = 'amazonimport/source_amazonppc';
	$countrycode = "_us";
	$installer->addAttribute('catalog_product', 'amazonpricesource'.$countrycode, $categoryid);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amazonpricesource'.$countrycode, $categoryid);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amazonpricesource'.$countrycode, $categoryid);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amazonpricesource'.$countrycode, $categoryid);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amazonpricesource'.$countrycode, $categoryid);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amazonpricesource'.$countrycode, $categoryid);
	
	
	$categoryid['label'] = 'If currency source is custom, choose currency';
	$categoryid['source'] = 'amazonimport/source_currency';
	$countrycode = "_us";
	$installer->addAttribute('catalog_product', 'amazoncustomcurrency'.$countrycode, $categoryid);

	$countrycode = "_ca";
	$installer->addAttribute('catalog_product', 'amazoncustomcurrency'.$countrycode, $categoryid);

	$countrycode = "_uk";
	$installer->addAttribute('catalog_product', 'amazoncustomcurrency'.$countrycode, $categoryid);

	$countrycode = "_fr";
	$installer->addAttribute('catalog_product', 'amazoncustomcurrency'.$countrycode, $categoryid);
		
	$countrycode = "_de";
	$installer->addAttribute('catalog_product', 'amazoncustomcurrency'.$countrycode, $categoryid);

	$countrycode = "_jp";
	$installer->addAttribute('catalog_product', 'amazoncustomcurrency'.$countrycode, $categoryid);
	
	


// okay, now we need to add each attribute to the attribute sets in the correct groups for that attribute.
foreach ($installer->getAllAttributeSetIds('catalog_product') as $attributeSetId) {
	
	// Amazon.com Initialisation.
	$installer->addAttributeGroup('catalog_product', $attributeSetId, 'Amazon USA Listing');

	$countrycode = "_us";
	$asn = 'Amazon USA Listing';
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'availableonamazon'.$countrycode),10);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'fulfillmentlatency'.$countrycode),20);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonnamesource'.$countrycode),40);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomname'.$countrycode),50);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazondescsource'.$countrycode),60);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomdesc'.$countrycode),70);
		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonimagesource'.$countrycode),80);
		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonpricesource'.$countrycode),90);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'allowspecialprices'.$countrycode),100);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomprice'.$countrycode),110);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonpcentmarkup'.$countrycode),120);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonvaluemarkup'.$countrycode),130);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncurrencysource'.$countrycode),140);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomcurrency'.$countrycode),150);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'asin'.$countrycode),160);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'asintype'.$countrycode),170);
	
	
	// Amazon.ca Initialisation.
	$installer->addAttributeGroup('catalog_product', $attributeSetId, 'Amazon Canada Listing');
	
	
	$countrycode = "_ca";
	$asn = 'Amazon Canada Listing';
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'availableonamazon'.$countrycode),10);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'fulfillmentlatency'.$countrycode),20);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonnamesource'.$countrycode),40);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomname'.$countrycode),50);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazondescsource'.$countrycode),60);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomdesc'.$countrycode),70);
		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonimagesource'.$countrycode),80);
		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonpricesource'.$countrycode),90);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'allowspecialprices'.$countrycode),100);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomprice'.$countrycode),110);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonpcentmarkup'.$countrycode),120);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonvaluemarkup'.$countrycode),130);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncurrencysource'.$countrycode),140);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomcurrency'.$countrycode),150);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'asin'.$countrycode),160);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'asintype'.$countrycode),170);
	
	// Amazon.co.uk Initialisation.
	$installer->addAttributeGroup('catalog_product', $attributeSetId, 'Amazon UK Listing');
	
	
	$countrycode = "_uk";
	$asn = 'Amazon UK Listing';
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'availableonamazon'.$countrycode),10);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'fulfillmentlatency'.$countrycode),20);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonnamesource'.$countrycode),40);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomname'.$countrycode),50);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazondescsource'.$countrycode),60);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomdesc'.$countrycode),70);
		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonimagesource'.$countrycode),80);
		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonpricesource'.$countrycode),90);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'allowspecialprices'.$countrycode),100);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomprice'.$countrycode),110);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonpcentmarkup'.$countrycode),120);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonvaluemarkup'.$countrycode),130);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncurrencysource'.$countrycode),140);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomcurrency'.$countrycode),150);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'asin'.$countrycode),160);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'asintype'.$countrycode),170);
	
	
	// Amazon.fr Initialisation.
	
    $installer->addAttributeGroup('catalog_product', $attributeSetId, 'Amazon France Listing');
	
	
	$countrycode = "_fr";	
	$asn = 'Amazon France Listing';
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'availableonamazon'.$countrycode),10);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'fulfillmentlatency'.$countrycode),20);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonnamesource'.$countrycode),40);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomname'.$countrycode),50);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazondescsource'.$countrycode),60);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomdesc'.$countrycode),70);
		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonimagesource'.$countrycode),80);
		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonpricesource'.$countrycode),90);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'allowspecialprices'.$countrycode),100);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomprice'.$countrycode),110);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonpcentmarkup'.$countrycode),120);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonvaluemarkup'.$countrycode),130);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncurrencysource'.$countrycode),140);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomcurrency'.$countrycode),150);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'asin'.$countrycode),160);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'asintype'.$countrycode),170);
	
	
	// Amazon.de Initialisation.
	
    $installer->addAttributeGroup('catalog_product', $attributeSetId, 'Amazon Germany Listing');
		
	$countrycode = "_de";
	$asn = 'Amazon Germany Listing';
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'availableonamazon'.$countrycode),10);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'fulfillmentlatency'.$countrycode),20);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonnamesource'.$countrycode),40);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomname'.$countrycode),50);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazondescsource'.$countrycode),60);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomdesc'.$countrycode),70);
		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonimagesource'.$countrycode),80);
		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonpricesource'.$countrycode),90);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'allowspecialprices'.$countrycode),100);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomprice'.$countrycode),110);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonpcentmarkup'.$countrycode),120);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonvaluemarkup'.$countrycode),130);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncurrencysource'.$countrycode),140);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomcurrency'.$countrycode),150);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'asin'.$countrycode),160);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'asintype'.$countrycode),170);
	
	// Amazon.jp Initialisation.
	
	$installer->addAttributeGroup('catalog_product', $attributeSetId, 'Amazon Japan Listing');
		
		
	$countrycode = "_jp";
	$asn = 'Amazon Japan Listing';
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'availableonamazon'.$countrycode),10);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'fulfillmentlatency'.$countrycode),20);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonnamesource'.$countrycode),40);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomname'.$countrycode),50);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazondescsource'.$countrycode),60);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomdesc'.$countrycode),70);
		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonimagesource'.$countrycode),80);
		
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonpricesource'.$countrycode),90);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'allowspecialprices'.$countrycode),100);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomprice'.$countrycode),110);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonpcentmarkup'.$countrycode),120);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazonvaluemarkup'.$countrycode),130);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncurrencysource'.$countrycode),140);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'amazoncustomcurrency'.$countrycode),150);
	
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'asin'.$countrycode),160);
	$installer->addAttributeToSet('catalog_product', $attributeSetId, $asn, $installer->getAttributeId('catalog_product', 'asintype'.$countrycode),170);
	
	
}


$installer->installEntities();

$installer->endSetup(); 

?>