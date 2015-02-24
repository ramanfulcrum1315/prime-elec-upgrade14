<?php

 class Camiloo_Amazonimport_Model_Entity_Amazonimport_OrderDetails extends Mage_Eav_Model_Entity_Abstract
{

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('amazonimport_orderDetails');
        $read = $resource->getConnection('amazonimport_setup');
        $write = $resource->getConnection('amazonimport_write');
        $this->setConnection($read, $write);
    }

}
?>
