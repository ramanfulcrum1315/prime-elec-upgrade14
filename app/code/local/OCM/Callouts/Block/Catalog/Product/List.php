<?php

class OCM_Callouts_Block_Catalog_Product_List extends Mage_Catalog_Block_Product_List
{

    /**
     * Get catalog layer model
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        /*
        $layer = Mage::registry('current_layer');
        if ($layer) {
            return $layer;
        }
        */
        return Mage::getSingleton('callouts/new_layer');
    }


}