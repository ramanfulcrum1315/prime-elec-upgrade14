<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');

$installer->startSetup();
	$installer->run("
	ALTER TABLE {$resource->getTableName('navigationmenu/contents')} ADD COLUMN `menuitem_id` int(11) NOT NULL default '0';
	ALTER TABLE {$resource->getTableName('navigationmenu/contents')} ADD COLUMN `position` smallint(6) NOT NULL default '0'
	");
$installer->endSetup();

$items= Mage::helper('navigationmenu/setup')->getMenuItemsDefault();
$a=1;
$model=Mage::getModel("navigationmenu/menuitems");
foreach ($items as $item){
	$data['title']= $item['name'];
	$data['category_id']= $item['id'];
	$data['category_name']= $item['name'];
	$data['status']= $item['status'];
	$data['order']=$a;
	$data['type']=1;
	$data['column']=3;
	$data['store_ids']= $item['store_ids'];
	$a++;
	$model->setData($data);
	$model->save();
}

