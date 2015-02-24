<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition End User License Agreement
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magento.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_UrlRewrite
 * @copyright Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license http://www.magento.com/license/enterprise-edition
 */

/* @var $this Mage_Core_Model_Resource_Setup */
$tableName = $this->getTable('enterprise_urlrewrite/redirect');

$indexes = $this->getConnection()->getIndexList($tableName);
$indexName = $this->getIdxName(
    'enterprise_urlrewrite/redirect',
    array('identifier'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);
$this->getConnection()->dropIndex($tableName, $indexName);
$this->getConnection()->addIndex(
    $tableName,
    $this->getIdxName(
        'enterprise_urlrewrite/redirect',
        array('identifier', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('identifier', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);