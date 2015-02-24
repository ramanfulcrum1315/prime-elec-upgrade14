<?php
/**
 * Productexport.php
 * CommerceThemes @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commercethemes.com/LICENSE-M1.txt
 *
 * @category   Product
 * @package    Productexport
 * @copyright  Copyright (c) 2003-2009 CommerceThemes @ InterSEC Solutions LLC. (http://www.commercethemes.com)
 * @license    http://www.commercethemes.com/LICENSE-M1.txt
 */ 


class Mage_Catalog_Model_Convert_Parser_Productexport
    extends Mage_Eav_Model_Convert_Parser_Abstract
{
    const MULTI_DELIMITER = ' , ';
    protected $_resource;

    /**
     * Product collections per store
     *
     * @var array
     */
    protected $_collections;

    protected $_productTypes = array(
        'simple'=>'Simple',
        'bundle'=>'Bundle',
        'configurable'=>'Configurable',
        'grouped'=>'Grouped',
        'virtual'=>'Virtual',
    );

    protected $_inventoryFields = array();

    protected $_imageFields = array();

    protected $_systemFields = array();
    protected $_internalFields = array();
    protected $_externalFields = array();

    protected $_inventoryItems = array();

    protected $_productModel;

    protected $_setInstances = array();

    protected $_store;
    protected $_storeId;
    protected $_attributes = array();

    public function __construct()
    {
        foreach (Mage::getConfig()->getFieldset('catalog_product_dataflow', 'admin') as $code=>$node) {
            if ($node->is('inventory')) {
                $this->_inventoryFields[] = $code;
                if ($node->is('use_config')) {
                    $this->_inventoryFields[] = 'use_config_'.$code;
                }
            }
            if ($node->is('internal')) {
                $this->_internalFields[] = $code;
            }
            if ($node->is('system')) {
                $this->_systemFields[] = $code;
            }
            if ($node->is('external')) {
                $this->_externalFields[$code] = $code;
            }
            if ($node->is('img')) {
                $this->_imageFields[] = $code;
            }
        }
    }

    /**
     * @return Mage_Catalog_Model_Mysql4_Convert
     */
    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Mage::getResourceSingleton('catalog_entity/convert');
                #->loadStores()
                #->loadProducts()
                #->loadAttributeSets()
                #->loadAttributeOptions();
        }
        return $this->_resource;
    }

    public function getCollection($storeId)
    {
        if (!isset($this->_collections[$storeId])) {
            $this->_collections[$storeId] = Mage::getResourceModel('catalog/product_collection');
            $this->_collections[$storeId]->getEntity()->setStore($storeId);
        }
        return $this->_collections[$storeId];
    }

    public function getProductTypeName($id)
    {
        return isset($this->_productTypes[$id]) ? $this->_productTypes[$id] : false;
    }

    public function getProductTypeId($name)
    {
        return array_search($name, $this->_productTypes);
    }

    /**
     * Retrieve product model cache
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProductModel()
    {
        if (is_null($this->_productModel)) {
            $productModel = Mage::getModel('catalog/product');
            $this->_productModel = Mage::objects()->save($productModel);
        }
        return Mage::objects()->load($this->_productModel);
    }

    /**
     * Retrieve current store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            try {
                $store = Mage::app()->getStore($this->getVar('store'));
            }
            catch (Exception $e) {
                $this->addException(Mage::helper('catalog')->__('Invalid store specified'), Varien_Convert_Exception::FATAL);
                throw $e;
            }
            $this->_store = $store;
        }
        return $this->_store;
    }

    /**
     * Retrieve store ID
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->_storeId = $this->getStore()->getId();
        }
        return $this->_storeId;
    }

    public function getAttributeSetInstance()
    {
        $productType = $this->getProductModel()->getType();
        $attributeSetId = $this->getProductModel()->getAttributeSetId();

        if (!isset($this->_setInstances[$productType][$attributeSetId])) {
            $this->_setInstances[$productType][$attributeSetId] =
                Mage::getSingleton('catalog/product_type')->factory($this->getProductModel());
        }

        return $this->_setInstances[$productType][$attributeSetId];
    }

    /**
     * Retrieve eav entity attribute model
     *
     * @param string $code
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttribute($code)
    {
        if (!isset($this->_attributes[$code])) {
            $this->_attributes[$code] = $this->getProductModel()->getResource()->getAttribute($code);
        }
        return $this->_attributes[$code];
    }

    /**
     * @deprecated not used anymore
     */
    public function parse()
    {
        $data = $this->getData();

        $entityTypeId = Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getId();

        $result = array();
        $inventoryFields = array();
        foreach ($data as $i=>$row) {
            $this->setPosition('Line: '.($i+1));
            try {
                // validate SKU
                if (empty($row['sku'])) {
                    $this->addException(Mage::helper('catalog')->__('Missing SKU, skipping the record'), Mage_Dataflow_Model_Convert_Exception::ERROR);
                    continue;
                }
                $this->setPosition('Line: '.($i+1).', SKU: '.$row['sku']);

                // try to get entity_id by sku if not set
                if (empty($row['entity_id'])) {
                    $row['entity_id'] = $this->getResource()->getProductIdBySku($row['sku']);
                }

                // if attribute_set not set use default
                if (empty($row['attribute_set'])) {
                    $row['attribute_set'] = 'Default';
                }
                // get attribute_set_id, if not throw error
                $row['attribute_set_id'] = $this->getAttributeSetId($entityTypeId, $row['attribute_set']);
                if (!$row['attribute_set_id']) {
                    $this->addException(Mage::helper('catalog')->__("Invalid attribute set specified, skipping the record"), Mage_Dataflow_Model_Convert_Exception::ERROR);
                    continue;
                }

                if (empty($row['type'])) {
                    $row['type'] = 'Simple';
                }
                // get product type_id, if not throw error
                $row['type_id'] = $this->getProductTypeId($row['type']);
                if (!$row['type_id']) {
                    $this->addException(Mage::helper('catalog')->__("Invalid product type specified, skipping the record"), Mage_Dataflow_Model_Convert_Exception::ERROR);
                    continue;
                }

                // get store ids
                $storeIds = $this->getStoreIds(isset($row['store']) ? $row['store'] : $this->getVar('store'));
                if (!$storeIds) {
                    $this->addException(Mage::helper('catalog')->__("Invalid store specified, skipping the record"), Mage_Dataflow_Model_Convert_Exception::ERROR);
                    continue;
                }

                // import data
                $rowError = false;
                foreach ($storeIds as $storeId) {
                    $collection = $this->getCollection($storeId);
                    $entity = $collection->getEntity();

                    $model = Mage::getModel('catalog/product');
                    $model->setStoreId($storeId);
                    if (!empty($row['entity_id'])) {
                        $model->load($row['entity_id']);
                    }
                    foreach ($row as $field=>$value) {
                        $attribute = $entity->getAttribute($field);

                        if (!$attribute) {
                            //$inventoryFields[$row['sku']][$field] = $value;

                            if (in_array($field, $this->_inventoryFields)) {
                                $inventoryFields[$row['sku']][$field] = $value;
                            }
                            continue;
                            #$this->addException(Mage::helper('catalog')->__("Unknown attribute: %s", $field), Mage_Dataflow_Model_Convert_Exception::ERROR);
                        }
                        if ($attribute->usesSource()) {
                            $source = $attribute->getSource();
                            $optionId = $this->getSourceOptionId($source, $value);
                            if (is_null($optionId)) {
                                $rowError = true;
                                $this->addException(Mage::helper('catalog')->__("Invalid attribute option specified for attribute %s (%s), skipping the record", $field, $value), Mage_Dataflow_Model_Convert_Exception::ERROR);
                                continue;
                            }
                            $value = $optionId;
                        }
                        $model->setData($field, $value);

                    }//foreach ($row as $field=>$value)

                    //echo 'Before **********************<br/><pre>';
                    //print_r($model->getData());
                    if (!$rowError) {
                        $collection->addItem($model);
                    }
                    unset($model);
                } //foreach ($storeIds as $storeId)
            } catch (Exception $e) {
                if (!$e instanceof Mage_Dataflow_Model_Convert_Exception) {
                    $this->addException(Mage::helper('catalog')->__("Error during retrieval of option value: %s", $e->getMessage()), Mage_Dataflow_Model_Convert_Exception::FATAL);
                }
            }
        }

        // set importinted to adaptor
        if (sizeof($inventoryFields) > 0) {
            Mage::register('current_imported_inventory', $inventoryFields);
            //$this->setInventoryItems($inventoryFields);
        } // end setting imported to adaptor

        $this->setData($this->_collections);
        return $this;
    }

    public function setInventoryItems($items)
    {
        $this->_inventoryItems = $items;
    }

    public function getInventoryItems()
    {
        return $this->_inventoryItems;
    }

    /**
     * Unparse (prepare data) loaded products
     *
     * @return Mage_Catalog_Model_Convert_Parser_Product
     */
    public function unparse()
    {
        $entityIds = $this->getData();

        foreach ($entityIds as $i => $entityId) {
					
					$allproduct = $this->getProductModel()
												->setData(array())
												->load($entityId);
						
					if ($this->getStore()->getCode() == Mage_Core_Model_Store::ADMIN_CODE) {
						$websiteCodes = array();
						foreach ($allproduct->getWebsiteIds() as $websiteId) {
						
							$websiteCode = Mage::app()->getWebsite($websiteId)->getCode();
							#print_r(Mage::app()->getWebsite($websiteId)->getStoreIds());
							
							$allstoreids = Mage::app()->getWebsite($websiteId)->getStoreIds();
									foreach ($allstoreids as $storeId) {
										
										$product = $this->getProductModel()
												->setData(array())
												->setStoreId($storeId)
												->load($entityId);
										$product->setTypeInstance($this->getAttributeSetInstance());
										/* @var $product Mage_Catalog_Model_Product */
						
										$position = Mage::helper('catalog')->__('Line %d, SKU: %s', ($i+1), $product->getSku());
										$this->setPosition($position);
						
										$row = array(
												'store'         => $this->getStore()->getCode(),
												'websites'      => '',
												'attribute_set' => $this->getAttributeSetName($product->getEntityTypeId(), $product->getAttributeSetId()),
												'type'          => $product->getTypeId(),
										);
											
										
										$row['websites'] = $websiteCode;
										
										foreach ($product->getData() as $field => $value) {
                if (in_array($field, $this->_systemFields) || is_object($value)) {
                    continue;
                }

                $attribute = $this->getAttribute($field);
                if (!$attribute) {
                    continue;
                }
								#print_r($attribute);
                if ($attribute->usesSource()) {
										
										$finalproductattributes = "";
										$row['config_attributes'] = '';
										if($product->getTypeId() == "configurable") {
											  $cProduct = Mage::getModel('catalog/product')->load($product->getId());
												//check if product is a configurable type or not
												if ($cProduct->getData('type_id') == "configurable")
												{
														 //get the configurable data from the product
														 $config = $cProduct->getTypeInstance(true);
														 //loop through the attributes                                  
														 foreach($config->getConfigurableAttributesAsArray($cProduct) as $attributes)
														 { 
																 #$finalproductattributes .= $attributes["label"] . ",";
														 		 $finalproductattributes .= $attributes['attribute_code'] . ",";
																 
														 }
												} 

										}
										$row['config_attributes'] = substr_replace($finalproductattributes,"",-1);
										
                    $option = $attribute->getSource()->getOptionText($value);
                    if ($value && empty($option)) {
                        $message = Mage::helper('catalog')->__("Invalid option id specified for %s (%s), skipping the record", $field, $value);
                        $this->addException($message, Mage_Dataflow_Model_Convert_Exception::ERROR);
                        continue;
                    }
                    if (is_array($option)) {
                        $value = join(self::MULTI_DELIMITER, $option);
                    } else {
                        $value = $option;
                    }
                    unset($option);
                }
                elseif (is_array($value)) {
                    continue;
                }

                $row[$field] = $value;
            }
					
										if ($stockItem = $product->getStockItem()) {
												foreach ($stockItem->getData() as $field => $value) {
														if (in_array($field, $this->_systemFields) || is_object($value)) {
																continue;
														}
														$row[$field] = $value;
												}
										}
					
										foreach ($this->_imageFields as $field) {
												if (isset($row[$field]) && $row[$field] == 'no_selection') {
														$row[$field] = null;
												}
										}
										$row['related'] = "";
										$incoming_RelatedProducts = $product->getRelatedProducts();
										foreach($incoming_RelatedProducts as $relatedproducts_str){
											#print_r($relatedproducts_str);
											#echo "SKU: " . $relatedproducts_str->getSku();
											$row['related'] .= $relatedproducts_str->getSku() . ",";
										} 
										
										$row['upsell'] = "";
										$incoming_UpSellProducts = $product->getUpSellProducts();
										foreach($incoming_UpSellProducts as $UpSellproducts_str){
											#print_r($relatedproducts_str);
											#echo "SKU: " . $UpSellproducts_str->getSku();
											$row['upsell'] .= $UpSellproducts_str->getSku() . ",";
										}
								
										$row['crosssell'] = "";
										$incoming_CrossSellProducts = $product->getCrossSellProducts ();
										foreach($incoming_CrossSellProducts as $CrossSellproducts_str){
											#print_r($relatedproducts_str);
											#echo "SKU: " . $CrossSellproducts_str->getSku();
											$row['crosssell'] .= $CrossSellproducts_str->getSku() . ",";
										}
						
										/* EXPORTS TIER PRICING */
										#$_tierPrices = Mage::getModel('bundle/product_price')->getTierPrice("",$product);
										#print_r($product->getTierPrice());
										$row['tier_prices'] = "";
										#$incoming_tierps = $product->getTierPrice();
										$incoming_tierps = $product->getData('tier_price');
										#print_r($incoming_tierps);
										if(is_array($incoming_tierps)) {
										foreach($incoming_tierps as $tier_str){
											#print_r($tier_str);
											$row['tier_prices'] .= $tier_str['cust_group'] . "=" . round($tier_str['price_qty']) . "=" . $tier_str['price'] . "|";
										}
										}
										/* EXPORTS ASSOICATED CONFIGURABLE SKUS */
										$row['associated'] = '';
										if($product->getTypeId() == "configurable") {
											$associatedProducts = Mage::getSingleton('catalog/product_type')->factory($product)->getUsedProducts($product);
											#print_r($associatedProducts->getUsedProducts($product));
											#echo "ID: " . $product2->getId();
											foreach($associatedProducts as $associatedProduct) {
													$row['associated'] .= $associatedProduct->getSku() . ",";
											}
										}
										
										/* EXPORTS ASSOICATED BUNDLE SKUS */
										$row['bundle_options'] = '';
										if($product->getTypeId() == "bundle") {
											$finalbundleoptions = "";
											$finalbundleselectionoptions = "";
											$optionModel = Mage::getModel('bundle/option')->getResourceCollection()->setProductIdFilter($product->getId());
												
											foreach($optionModel as $eachOption) {
													$resource = Mage::getSingleton('core/resource');
													$OptiondataDB = $resource->getConnection('catalog_write');
													$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
													
													$selectOptionID = "SELECT title FROM ".$prefix."catalog_product_bundle_option_value WHERE option_id = ".$eachOption->getData('option_id')."";
													$Optiondatarows = $OptiondataDB->fetchAll($selectOptionID);
														foreach($Optiondatarows as $Option_row)
														{
															$finaltitle = $Option_row['title'];
														}
													$finalbundleoptions .=  $finaltitle . "," . $eachOption->getData('type') . "," . $eachOption->getData('required') . "," . $eachOption->getData('position') . "|";
													
													
													$selectionModel = Mage::getModel('bundle/selection')->setOptionId($eachOption->getData('option_id'))->getResourceCollection();
													#print_r($selectionModel->getData());
													foreach($selectionModel as $eachselectionOption) {
														#echo "t: " . $eachselectionOption->getData('selection_price_type');
														if($eachselectionOption->getData('option_id') == $eachOption->getData('option_id')) {
														$finalbundleselectionoptions .=  $eachselectionOption->getData('sku') . ":" . $eachselectionOption->getData('selection_price_value') . ":" . $eachselectionOption->getData('is_default') . ":" . $eachselectionOption->getData('selection_qty') . ":" . $eachselectionOption->getData('selection_can_change_qty') . "|";
														}
													}
											}
											$row['bundle_options'] = substr_replace($finalbundleoptions,"",-1);
											$row['bundle_selections'] = substr_replace($finalbundleselectionoptions,"",-1);
										}
										
										/* EXPORTS ASSOICATED GROUPED SKUS */
										$row['grouped'] = '';
										if($product->getTypeId() == "grouped") {
											$associatedProducts = Mage::getSingleton('catalog/product_type')->factory($product)->getAssociatedProducts($product);
											foreach($associatedProducts as $associatedProduct) {
													$row['grouped'] .= $associatedProduct->getSku() . ",";
											}
										}
										
										/* EXPORTS DOWNLOADABLE OPTIONS */
										$row['downloadable_options'] = '';
										$finaldownloabledproductoptions = "";
										if($product->getTypeId() == "downloadable") {
										
											$_linkCollection = Mage::getModel('downloadable/link')->getCollection()
												->addProductToFilter($product->getId())
												->addTitleToResult($product->getStoreId())
												->addPriceToResult($product->getStore()->getWebsiteId());
				
										 foreach ($_linkCollection as $link) {
											/* @var Mage_Downloadable_Model_Link $link */
											#print_r($link);
											
											if($link->getLinkUrl() !="") {
											$finaldownloabledproductoptions .= $link->getTitle() . "," . $link->getPrice() . "," . $link->getNumberOfDownloads() . "," . $link->getLinkType() . "," . $link->getLinkUrl() . "|";
											} else {
											$finaldownloabledproductoptions .= $link->getTitle() . "," . $link->getPrice() . "," . $link->getNumberOfDownloads() . "," . $link->getLinkType() . "," . $link->getLinkFile() . "|";
											}
										 }
										 $row['downloadable_options'] = substr_replace($finaldownloabledproductoptions,"",-1);
											
										}
										
										/* EXPORTS CUSTOM OPTIONS */
										#print_r($product->getOptions());
										foreach ($product->getOptions() as $o) {
											#print_r($o->getData());
											#echo "CUSTOM OPTIONS NAME: " . $o->getData('title') . ":" . $o->getData('type') . ":" . $o->getData('is_require') . ":". $o->getData('sort_order');
											$customoptionvalues = "";
											$customoptionstitle = $o->getData('title') . ":" . $o->getData('type') . ":" . $o->getData('is_require') . ":". $o->getData('sort_order');
											
											foreach ( $o->getValues() as $oValues ) {
												if($oValues->getData('price_type')=="") { $price_type = "fixed"; } else { $price_type = $oValues->getData('price_type'); }
												if($oValues->getData('price')=="") { $price = "0.0000"; } else { $price = $oValues->getData('price'); }
												if($oValues->getData('sku')=="") { $sku = "sku"; } else { $sku = $oValues->getData('sku'); }
												if($oValues->getData('sort_order')=="") { $sort_order = "0"; } else { $sort_order = $oValues->getData('sort_order'); }
												
												$customoptionvalues .= $oValues->getData('title') . ":" . $price_type . ":" . $price . ":" . $sku . ":" . $sort_order . "|";
											}
											$row[$customoptionstitle] = substr_replace($customoptionvalues,"",-1);
										}
					
										$batchExport = $this->getBatchExportModel()
												->setId(null)
												->setBatchId($this->getBatchModel()->getId())
												->setBatchData($row)
												->setStatus(1)
												->save();
											
											
									}
                }
                #$row['websites'] = join(',', $websiteCodes);
				}
				else {
						/*
						$row['websites'] = $this->getStore()->getWebsite()->getCode();
						if ($this->getVar('url_field')) {
								$row['url'] = $product->getProductUrl(false);
						}
						*/
				}





        }

        return $this;
    }

    /**
     * Retrieve accessible external product attributes
     *
     * @return array
     */
    public function getExternalAttributes()
    {
        $entityTypeId = Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getId();
        $productAttributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entityTypeId)
            ->load();

            var_dump($this->_externalFields);

        $attributes = $this->_externalFields;

        foreach ($productAttributes as $attr) {
            $code = $attr->getAttributeCode();
            if (in_array($code, $this->_internalFields) || $attr->getFrontendInput() == 'hidden') {
                continue;
            }
            $attributes[$code] = $code;
        }

        foreach ($this->_inventoryFields as $field) {
            $attributes[$field] = $field;
        }

        return $attributes;
    }
}