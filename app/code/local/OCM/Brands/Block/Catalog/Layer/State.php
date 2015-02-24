<?php
class OCM_Brands_Block_Catalog_Layer_State extends Mage_Catalog_Block_Layer_State
{

    /**
     * Remove Current Brand from state
     *
     * @return array
     */
    public function getActiveFilters()
    {
        $filters = parent::getActiveFilters();
        if($this->helper('brands')->getCurrentBrand()) {
            foreach ($filters as $i => $filter) {
                if($filter->getFilter()->getRequestVar()==$this->helper('brands')->getBrandAttrCode()) {
                    unset($filters[$i]);
                }
            }
        }
        return $filters;
    }


}
