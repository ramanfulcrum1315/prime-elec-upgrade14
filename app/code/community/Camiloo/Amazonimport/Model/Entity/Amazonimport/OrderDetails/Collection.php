<?php


class Camiloo_Amazonimport_Model_Entity_Amazonimport_OrderDetails_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('amazonimport/amazonimport_orderDetails');
    }

    public function setOrderFilter($orderId)
    {
        $this->addAttributeToFilter('parent_id', $orderId);
        return $this;
    }

    public function setAmazonOrderFilter($amazonOrderId)
    {
        $this->addAttributeToFilter('amazon_order_id', $amazonOrderId);
        return $this;
    }

}

?>
