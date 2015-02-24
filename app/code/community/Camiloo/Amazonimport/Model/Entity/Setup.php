<?php

 class Camiloo_Amazonimport_Model_Entity_Setup extends Mage_Eav_Model_Entity_Setup
{
    public function getDefaultEntities()
    {
        return array(
        	'amazonimport_orderDetails' => array(
                'entity_model'      => 'amazonimport/orderDetails',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id' => array('type'=>'static'),
                    'amazon_order_id' => array('type'=>'varchar'),
                    'amazon_country_id' => array('type'=>'varchar')
                )
			
        ));
    }
}
?>
