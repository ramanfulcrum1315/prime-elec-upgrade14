<?php

class OCM_Callouts_Model_New_Layer extends Mage_Catalog_Model_Layer
{

    /**
     * Retrieve current layer product collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('news_from_date', array(
                    'to' => date('Y-m-d')
                    ))
                ->addAttributeToFilter('news_to_date', array(
                    'from' => date('Y-m-d')
                    ));

            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }

        return $collection;
    }





}