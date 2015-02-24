<?php
/**
 * Camiloo Limited
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.camiloo.co.uk/license.txt
 *
 * @category   Camiloo
 * @package    Camiloo_Amazonimport
 * @copyright  Copyright (c) 2011 Camiloo Limited (http://www.camiloo.co.uk)
 * @license    http://www.camiloo.co.uk/license.txt
 */


class Camiloo_Amazonimport_Model_Paymentmethodde extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'amzpaymentmethodde';
	protected $_infoBlockType = 'amazonimport/amzpaymentinfo';
	protected $_canUseCheckout = false;
	protected $_canUseForMultishipping = false;
	protected $_canUseInternal = false;
}

?>