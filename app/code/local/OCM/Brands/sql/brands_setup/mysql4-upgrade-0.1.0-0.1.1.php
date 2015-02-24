<?php

$installer = $this;

$installer->startSetup();


$installer->run("

ALTER TABLE `brands` ADD `show_in_menu` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `brands` ADD `menu_position` int(11) NOT NULL DEFAULT '0';

");


Mage::getModel('cms/block')
    ->setTitle('Brands')
    ->setContent('Here is brands content')
    ->setIdentifier('ocm-brands-block')
    ->setIsActive(false)
    ->save();
          
$installer->endSetup();
