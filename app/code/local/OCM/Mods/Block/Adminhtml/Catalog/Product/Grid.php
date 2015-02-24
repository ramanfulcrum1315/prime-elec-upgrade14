<?php

class OCM_Mods_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{

    protected function _prepareCollection()
    {
        $store = $this->_getStore();

        //parent::_prepareCollection();
        
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
// Add Items to collection 
            ->addAttributeToSelect('supplier')
            ->addAttributeToSelect('bin_location')
            ->addAttributeToSelect('item_condition')
            ->addAttributeToSelect('lot_mumber')
// End Items to collection 
            //->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');

        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            //$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }

        $this->setCollection($collection);


        if ($this->getCollection()) {

            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter   = $this->getParam($this->getVarNameFilter(), null);

            if (is_null($filter)) {
                $filter = $this->_defaultFilter;
            }

            if (is_string($filter)) {
                $data = $this->helper('adminhtml')->prepareFilterString($filter);
                $this->_setFilterValues($data);
            }
            else if ($filter && is_array($filter)) {
                $this->_setFilterValues($filter);
            }
            else if(0 !== sizeof($this->_defaultFilter)) {
                $this->_setFilterValues($this->_defaultFilter);
            }

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $this->_setCollectionOrder($this->_columns[$columnId]);
            }

            if (!$this->_isExport) {
                $this->getCollection()->load();
                $this->_afterLoadCollection();
            }
        }

        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }
/**/

    protected function _prepareColumns()
    {
        parent::_prepareColumns();

		$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'item_condition');
        $options = array();
        foreach( $attribute->getSource()->getAllOptions(true, true) as $option ) {
           $options[$option['value']] = $option['label'];
        }
        $this->addColumnAfter('item_condition',
            array(
                'header'=> Mage::helper('catalog')->__('Item Condition'),
                'width' => '50px',
                'index' => 'item_condition',
                'type'  => 'options',
                'options' => $options,
        ),'name');

		$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'supplier');
        $options = array();
        foreach( $attribute->getSource()->getAllOptions(true, true) as $option ) {
           $options[$option['value']] = $option['label'];
        }
        $this->addColumnAfter('supplier',
            array(
                'header'=> Mage::helper('catalog')->__('Supplier'),
                'width' => '50px',
                'index' => 'supplier',
                'type'  => 'options',
                'options' => $options,
        ),'item_condition');
        
        
        $this->addColumnAfter('bin_location',
            array(
                'header'=> Mage::helper('catalog')->__('Bin Location'),
                'width' => '50px',
                'index' => 'bin_location',
        ),'supplier');


        $this->addColumnAfter('lot_mumber',
            array(
                'header'=> Mage::helper('catalog')->__('Lot Number'),
                'width' => '50px',
                'index' => 'lot_mumber',
        ),'supplier');

        
        $this->removeColumn('type');
        $this->removeColumn('set_name');
        $this->removeColumn('visibility');

        $this->sortColumnsByOrder();        
        return $this;


    }
/**/
}
