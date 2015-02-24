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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Order API
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Api extends Mage_Sales_Model_Api_Resource
{
    public function __construct()
    {
        $this->_attributesMap['order']         = array('order_id' => 'entity_id');
        $this->_attributesMap['order_address'] = array('address_id' => 'entity_id');
        $this->_attributesMap['order_payment'] = array('payment_id' => 'entity_id');

    }

    /**
     * Initialize basic order model
     *
     * @param mixed $orderIncrementId
     * @return Mage_Sales_Model_Order
     */
    protected function _initOrder($orderIncrementId)
    {
        $order = Mage::getModel('sales/order');

        /* @var $order Mage_Sales_Model_Order */

        $order->loadByIncrementId($orderIncrementId);

        if (!$order->getId()) {
            $this->_fault('not_exists');
        }

        return $order;
    }

    /**
     * Retrieve list of orders by filters
     *
     * @param array $filters
     * @return array
     */
    public function items($filters = null)
    {
        //TODO: add full name logic
        $billingAliasName = 'billing_o_a';
        $shippingAliasName = 'shipping_o_a';
        
        $collection = Mage::getModel("sales/order")->getCollection()
            ->addAttributeToSelect('*')
            ->addAddressFields()
            ->addExpressionFieldToSelect(
                'billing_firstname', "{{billing_firstname}}", array('billing_firstname'=>"$billingAliasName.firstname")
            )
            ->addExpressionFieldToSelect(
                'billing_lastname', "{{billing_lastname}}", array('billing_lastname'=>"$billingAliasName.lastname")
            )
            ->addExpressionFieldToSelect(
                'shipping_firstname', "{{shipping_firstname}}", array('shipping_firstname'=>"$shippingAliasName.firstname")
            )
            ->addExpressionFieldToSelect(
                'shipping_lastname', "{{shipping_lastname}}", array('shipping_lastname'=>"$shippingAliasName.lastname")
            )
            ->addExpressionFieldToSelect(
                    'billing_name',
                    "CONCAT({{billing_firstname}}, ' ', {{billing_lastname}})",
                    array('billing_firstname'=>"$billingAliasName.firstname", 'billing_lastname'=>"$billingAliasName.lastname")
            )
            ->addExpressionFieldToSelect(
                    'shipping_name',
                    'CONCAT({{shipping_firstname}}, " ", {{shipping_lastname}})',
                    array('shipping_firstname'=>"$shippingAliasName.firstname", 'shipping_lastname'=>"$shippingAliasName.lastname")
            );
        
        if (is_array($filters)) {
            try {
                foreach ($filters as $field => $value) {
                    if (isset($this->_attributesMap['order'][$field])) {
                        $field = $this->_attributesMap['order'][$field];
                    }

                    $collection->addFieldToFilter($field, $value);
                }
            } catch (Mage_Core_Exception $e) {
                $this->_fault('filters_invalid', $e->getMessage());
            }
        }

        $result = array();

        foreach ($collection as $order) {
            $result[] = $this->_getAttributes($order, 'order');
        }

        return $result;
    }

    /**
     * Retrieve full order information
     *
     * @param string $orderIncrementId
     * @return array
     */
    public function info($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);

        if ($order->getGiftMessageId() > 0) {
            $order->setGiftMessage(
                Mage::getSingleton('giftmessage/message')->load($order->getGiftMessageId())->getMessage()
            );
        }

        $result = $this->_getAttributes($order, 'order');

        $result['shipping_address'] = $this->_getAttributes($order->getShippingAddress(), 'order_address');
        $result['billing_address']  = $this->_getAttributes($order->getBillingAddress(), 'order_address');
        $result['items'] = array();

        foreach ($order->getAllItems() as $item) {
            if ($item->getGiftMessageId() > 0) {
                $item->setGiftMessage(
                    Mage::getSingleton('giftmessage/message')->load($item->getGiftMessageId())->getMessage()
                );
            }

            $result['items'][] = $this->_getAttributes($item, 'order_item');
        }

        $result['payment'] = $this->_getAttributes($order->getPayment(), 'order_payment');

        $result['status_history'] = array();

        foreach ($order->getAllStatusHistory() as $history) {
            $result['status_history'][] = $this->_getAttributes($history, 'order_status_history');
        }

        return $result;
    }

    /**
     * Add comment to order
     *
     * @param string $orderIncrementId
     * @param string $status
     * @param string $comment
     * @param boolean $notify
     * @return boolean
     */
    public function addComment($orderIncrementId, $status, $comment = null, $notify = false)
    {
        $order = $this->_initOrder($orderIncrementId);

        $order->addStatusToHistory($status, $comment, $notify);


        try {
            if ($notify && $comment) {
                $oldStore = Mage::getDesign()->getStore();
                $oldArea = Mage::getDesign()->getArea();
                Mage::getDesign()->setStore($order->getStoreId());
                Mage::getDesign()->setArea('frontend');
            }

            $order->save();
            $order->sendOrderUpdateEmail($notify, $comment);
            if ($notify && $comment) {
                Mage::getDesign()->setStore($oldStore);
                Mage::getDesign()->setArea($oldArea);
            }

        } catch (Mage_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        }

        return true;
    }

    /**
     * Hold order
     *
     * @param string $orderIncrementId
     * @return boolean
     */
    public function hold($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);

        try {
            $order->hold();
            $order->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        }

        return true;
    }

    /**
     * Unhold order
     *
     * @param string $orderIncrementId
     * @return boolean
     */
    public function unhold($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);

        try {
            $order->unhold();
            $order->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        }

        return true;
    }

    /**
     * Cancel order
     *
     * @param string $orderIncrementId
     * @return boolean
     */
    public function cancel($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);

        try {
            $order->cancel();
            $order->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        }

        return true;
    }
	////////////   Create Order
	
public function place($invoice_arr,$cc)
 {
	$order_date=$invoice_arr[0][1];
	$product_name=$invoice_arr[0][2];
	$sku=$invoice_arr[0][3];
	$qty=$invoice_arr[0][4];
	$product_price=$invoice_arr[0][5];
	$coupon_discount=$invoice_arr[0][6];
	$shipping_price=$invoice_arr[0][7];
	$surcharge_price=$invoice_arr[0][8];
	$tax_price=$invoice_arr[0][9];
	$total_price=$invoice_arr[0][10];
	$marketplace=$invoice_arr[0][11];
	$item_num=$invoice_arr[0][12];
	$email=$invoice_arr[0][13];
	$cust_first_name=$invoice_arr[0][14];
	$cust_last_name=$invoice_arr[0][15];
	$company=$invoice_arr[0][16];
	$phone=$invoice_arr[0][17];
	$fax=$invoice_arr[0][18];
	$cust_street1=$invoice_arr[0][19];
	$cust_street2=$invoice_arr[0][20];
	$cust_street=$cust_street1.' '.$cust_street2;
	$cust_city=$invoice_arr[0][21];
	$cust_state=$invoice_arr[0][22];
	$cust_zip=$invoice_arr[0][23];
	$cust_country=$invoice_arr[0][24];
	$billing_first_name=$invoice_arr[0][30];
	$billing_last_name=$invoice_arr[0][31];
	$billing_company=$invoice_arr[0][32];
	$billing_street1=$invoice_arr[0][33];
	$billing_street2=$invoice_arr[0][34];
	$billing_street=$billing_street1.' '.$billing_street2;
	$billing_city=$invoice_arr[0][35];
	$billing_state=$invoice_arr[0][36];
	$billing_zip=$invoice_arr[0][37];
	$billing_country=$invoice_arr[0][38];
	$shipping_method=$invoice_arr[0][39];
	$attributes=$invoice_arr[0][40];
	$order_notes=$invoice_arr[0][41];
	$seller_notes=$invoice_arr[0][42];
	$bin_location=$invoice_arr[0][43];
	$serial_number=$invoice_arr[0][44];
	$current_bid_price=$invoice_arr[0][45];
	$max_bid_price=$invoice_arr[0][46];
	$sale_source=$invoice_arr[0][47];
	$insurance_fee=$invoice_arr[0][48];
	$bonding_fee=$invoice_arr[0][49];
	$customer_id=$invoice_arr[0][50];

	// look up country and assign country code to invoice_arr (customer)
	if(in_array($invoice_arr[0][24],$cc)){
		$customer_country_id=$cc[$invoice_arr[0][24]];
	}
	else{
		$customer_country_id='RU';
	}
	//look up country and assign country code to invoice_arr (billing)
	if(in_array($invoice_arr[0][38],$cc)){
		$billing_country_id=$cc[$invoice_arr[0][38]];
	}
	else{
		$billing_country_id='RU';
	}
	
//***********************************************If customer does not exist by email upload with given data.  List of emails will be written to missing_customers.txt
  $customer = Mage::getModel('customer/customer')->load($customer_id);/*$customerId is the id of the customer who is placing the order, it can be passed as an argument to the function place()*/
  $transaction = Mage::getModel('core/resource_transaction');
  $storeId = $customer->getStoreId();
  $reservedOrderId = Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($storeId);
 
   $order = Mage::getModel('sales/order')
  ->setIncrementId($reservedOrderId)
  ->setStoreId($storeId)
  ->setQuoteId(0)
  ->setGlobal_currency_code('USD')
  ->setBase_currency_code('USD')
  ->setStore_currency_code('USD')
  ->setOrder_currency_code('USD');
 
  // set Customer data
	 if($customer_id){
	  $order->setCustomer_email($customer->getEmail())
	  ->setCustomerFirstname($customer->getFirstname())
	  ->setCustomerLastname($customer->getLastname())
	  ->setCustomerGroupId($customer->getGroupId())
	  ->setCustomer_is_guest(0)
	  ->setCustomer($customer);
	 }
	 else{
	   $order->setCustomer_email($email)
	  ->setCustomerFirstname($cust_first_name)
	  ->setCustomerLastname($cust_last_name)
	  ->setCustomer_is_guest(1);
	 }
 

 
  // set Billing Address
  //$billing = $customer->getDefaultBillingAddress();
  $billingAddress = Mage::getModel('sales/order_address')
  ->setStoreId($storeId)
  ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
  ->setCustomerId($customer->getId())
  ->setCustomerAddressId($customer->getDefaultBilling())
  ->setCustomer_address_id('')
  ->setPrefix('')
  ->setFirstname($billing_first_name)
  ->setMiddlename('')
  ->setLastname($billing_last_name)
  ->setSuffix('')
  ->setCompany($billing_company)
  ->setStreet($billing_street)
  ->setCity($billing_city)
  ->setCountry_id($invoice_arr[0][38])
  ->setRegion('')
  ->setRegion_id('')
  ->setPostcode($billing_zip)
  ->setTelephone($phone)
  ->setFax($fax);
  $order->setBillingAddress($billingAddress);
 
  //$shipping = $customer->getDefaultShippingAddress();
  $shippingAddress = Mage::getModel('sales/order_address')
  ->setStoreId($storeId)
  ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
  ->setCustomerId($customer->getId())
  ->setCustomerAddressId($customer->getDefaultBilling())
  ->setCustomer_address_id('')
  ->setPrefix('')
  ->setFirstname($cust_first_name)
  ->setMiddlename('')
  ->setLastname($cust_last_name)
  ->setSuffix('')
  ->setCompany($company)
  ->setStreet($cust_street)
  ->setCity($cust_city)
  ->setCountry_id($invoice_arr[0][24])
  ->setRegion('')
  ->setRegion_id('')
  ->setPostcode($cust_zip)
  ->setTelephone($phone)
  ->setFax($fax);
 
  $order->setShippingAddress($shippingAddress)
  ->setShipping_method($shipping_method)
  ->setShippingDescription($shipping_method);
 
  $orderPayment = Mage::getModel('sales/order_payment')
  ->setStoreId($storeId)
  ->setCustomerPaymentId(0)
  ->setMethod('purchaseorder')
  ->setPo_number(' - ');
  $order->setPayment($orderPayment);
 
  $subTotal = 0;
  $grand_total = 0;
  $discounts = 0;
  $tax_total = 0;
  $product_count=count($invoice_arr);
  for($i=0;$i<$product_count;$i++){
  $_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$invoice_arr[$i][3]); 
  $product_id=$_product->getId();
  $rowTotal = ($invoice_arr[$i][5] * $invoice_arr[$i][4]);
  $orderItem = Mage::getModel('sales/order_item')
  ->setStoreId($storeId)
  ->setQuoteItemId(0)
  ->setQuoteParentItemId(NULL)
  ->setProductId($product_id)
  ->setProductType($_product->getTypeId())
  ->setQtyBackordered(NULL)
  ->setTotalQtyOrdered($invoice_arr[$i][4])
  ->setQtyOrdered($invoice_arr[$i][4])
  ->setName($_product->getName())
  ->setSku($_product->getSku())
  ->setPrice($invoice_arr[$i][5])
  ->setBasePrice($invoice_arr[$i][5])
  ->setOriginalPrice($invoice_arr[$i][5])
  ->setRowTotal($rowTotal)
  ->setBaseRowTotal($rowTotal)
  ->setTaxAmount($invoice_arr[$i][9])
  ->setDiscountAmount($invoice_arr[$i][6])
  ->setDiscount($invoice_arr[$i][6]);

 $shipping_cost += $invoice_arr[$i][7];
 $tax_total += $invoice_arr[$i][9];
  $discounts -= $invoice_arr[$i][6];
  $subTotal += $rowTotal;
  
  $order->addItem($orderItem);
  }
 $subTotal+=$discounts;
 $subTotal+=$tax_total;
 $grand_total=$subTotal+$shipping_cost;
  $order->setSubtotal($subTotal)
  ->setBaseSubtotal($subTotal)
  ->setGrandTotal($grand_total)
  ->setBaseGrandTotal($grand_total)
  ->setShippingAmount($invoice_arr[$i][7]);
  ->setShippingAmount($shipping_cost)
  ->setTotalPaid($grand_total);
  //->setState('complete', true);
 
  $transaction->addObject($order);
  $transaction->addCommitCallback(array($order, 'place'));
  $transaction->addCommitCallback(array($order, 'save'));
  $transaction->save(); 
 }
} // Class Mage_Sales_Model_Order_Api End
