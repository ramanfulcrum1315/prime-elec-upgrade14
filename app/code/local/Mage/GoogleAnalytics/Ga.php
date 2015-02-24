<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleAnalytics
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * GoogleAnalitics Page Block
 *
 * @category   Mage
 * @package    Mage_GoogleAnalytics
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleAnalytics_Block_Ga extends Mage_Core_Block_Template
{
    /**
     * @deprecated after 1.4.1.1
     * @see self::_getOrdersTrackingCode()
     * @return string
     */
    public function getQuoteOrdersHtml()
    {
        return '';
    }

    /**
     * @deprecated after 1.4.1.1
     * self::_getOrdersTrackingCode()
     * @return string
     */
    public function getOrderHtml()
    {
        return '';
    }

    /**
     * @deprecated after 1.4.1.1
     * @see _toHtml()
     * @return string
     */
    public function getAccount()
    {
        return '';
    }

    /**
     * Get a specific page name (may be customized via layout)
     *
     * @return string|null
     */
    public function getPageName()
    {
        return $this->_getData('page_name');
    }

    /**
     * Render regular page tracking javascript code
     * The custom "page name" may be set from layout or somewhere else. It must start from slash.
     *
     * @link http://code.google.com/apis/analytics/docs/gaJS/gaJSApiBasicConfiguration.html#_gat.GA_Tracker_._trackPageview
     * @link http://code.google.com/apis/analytics/docs/gaJS/gaJSApi_gaq.html
     * @param string $accountId
     * @return string
     */
    protected function _getPageTrackingCode($accountId)
    {
    $pageName   = trim($this->getPageName());
    $optPageURL = '';
    if ($pageName && preg_match('/^\/.*/i', $pageName)) {
        $optPageURL = ", '{$this->jsQuoteEscape($pageName)}'";
    }
    return "
        ga('create', '".$this->jsQuoteEscape($accountId)."', {'cookieDomain':'".Mage::getStoreConfig('web/cookie/cookie_domain', Mage::app()->getStore() )."'});
        ga('send', 'pageview' ".$optPageURL.");
    ";
}

    /**
     * Render information about specified orders and their items
     *
     * @link http://code.google.com/apis/analytics/docs/gaJS/gaJSApiEcommerce.html#_gat.GA_Tracker_._addTrans
     * @return string
     */
    protected function _getOrdersTrackingCode()
    {
    $orderIds = $this->getOrderIds();
    if (empty($orderIds) || !is_array($orderIds)) {
        return;
    }
    $collection = Mage::getResourceModel('sales/order_collection')
        ->addFieldToFilter('entity_id', array('in' => $orderIds))
    ;
    $result = array("
        // Transaction code...
        ga('require', 'ecommerce', 'ecommerce.js');
    ");

    foreach ($collection as $order) {
        if ($order->getIsVirtual()) {
            $address = $order->getBillingAddress();
        } else {
            $address = $order->getShippingAddress();
        }

        $result[] = "
            ga('ecommerce:addTransaction', {
                id:          '".$order->getIncrementId()."', // Transaction ID
                affiliation: '".$this->jsQuoteEscape(Mage::app()->getStore()->getFrontendName())."', // Affiliation or store name
                revenue:     '".$order->getBaseGrandTotal()."', // Grand Total
                shipping:    '".$order->getBaseShippingAmount()."', // Shipping cost
                tax:         '".$order->getBaseTaxAmount()."', // Tax

            });
        ";

        foreach ($order->getAllVisibleItems() as $item) {

            $result[] = "
            ga('ecommerce:addItem', {

                id:       '".$order->getIncrementId()."', // Transaction ID.
                sku:      '".$this->jsQuoteEscape($item->getSku())."', // SKU/code.
                name:     '".$this->jsQuoteEscape($item->getName())."', // Product name.
                category: '', // Category or variation. there is no 'category' defined for the order item
                price:    '".$item->getBasePrice()."', // Unit price.
                quantity: '".$item->getQtyOrdered()."' // Quantity.

            });
        ";

        }
        $result[] = "ga('ecommerce:send');";
    }
    return implode("\n", $result);
}

    /**
     * Render GA tracking scripts
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!Mage::helper('googleanalytics')->isGoogleAnalyticsAvailable()) {
            return '';
        }

        return parent::_toHtml();
    }
}
