<?php


class OCM_Mods_Model_Resource_Sales_Order_Grid_Collection extends Mage_Sales_Model_Resource_Order_Grid_Collection
{
    /**
     * Init collection select
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        $this->getSelect()->join(
            'sales_flat_order',
            'main_table.entity_id = sales_flat_order.entity_id',
            array('customer_email')
        );
        parent::_initSelect();
        return $this;
    }


}