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

class Camiloo_Amazonimport_Model_Timedjobscheduler extends Mage_Core_Model_Abstract
{
	public function runEveryHour(){
			// this function is called by the built in Magento Cronjob and activates hourly tasks
			Mage::getModel('amazonimport/cron')->productImport();

		if ("1HOUR" == Mage::getStoreConfig('amazonint/general/stockfrequency'))
		{
			Mage::getModel('amazonimport/cron')->refreshStockOrPrice("Stock");
		}
		if ("1HOUR" == Mage::getStoreConfig('amazonint/general/pricefrequency'))
		{
			Mage::getModel('amazonimport/cron')->refreshStockOrPrice("Price");
		}
	}
	
	public function runEveryFive(){
			// this function is called by the built in Magento Cronjob and activates regular tasks
			// which need to run every 5 minutes such as stock level synchronisation and order importing
			 Mage::getModel('amazonimport/cron')->runSurestream();
			 Mage::getModel('amazonimport/cron')->orderImport();
			 Mage::getModel('amazonimport/cron')->runDispatches();
	}
	
	public function runEveryThirty(){
		if ("30MINS" == Mage::getStoreConfig('amazonint/general/stockfrequency')
			|| "" == Mage::getStoreConfig('amazonint/general/stockfrequency'))
		{
			Mage::getModel('amazonimport/cron')->refreshStockOrPrice("Stock");
		}
		if ("30MINS" == Mage::getStoreConfig('amazonint/general/pricefrequency')
			|| "" == Mage::getStoreConfig('amazonint/general/pricefrequency'))
		{
			Mage::getModel('amazonimport/cron')->refreshStockOrPrice("Price");
		}
	}

	public function runEveryFifteen() {
		if ("15MINS" == Mage::getStoreConfig('amazonint/general/stockfrequency'))
		{
			Mage::getModel('amazonimport/cron')->refreshStockOrPrice("Stock");
		}
		if ("15MINS" == Mage::getStoreConfig('amazonint/general/pricefrequency'))
		{
			Mage::getModel('amazonimport/cron')->refreshStockOrPrice("Price");
		}
	}

	public function runEveryFourHours() {
		if ("4HOUR" == Mage::getStoreConfig('amazonint/general/stockfrequency'))
		{
			Mage::getModel('amazonimport/cron')->refreshStockOrPrice("Stock");
		}
		if ("4HOUR" == Mage::getStoreConfig('amazonint/general/pricefrequency'))
		{
			Mage::getModel('amazonimport/cron')->refreshStockOrPrice("Price");
		}
	}

	public function runEveryTwelveHours() {
		if ("12HOUR" == Mage::getStoreConfig('amazonint/general/stockfrequency'))
		{
			Mage::getModel('amazonimport/cron')->refreshStockOrPrice("Stock");
		}
		if ("12HOUR" == Mage::getStoreConfig('amazonint/general/pricefrequency'))
		{
			Mage::getModel('amazonimport/cron')->refreshStockOrPrice("Price");
		}
	}
	
	public function runOnlyAtMidnight(){
			// this function is called by the built in Magento Cronjob and activates regular tasks
			// which need to run every night at Midnight
			Mage::getModel('amazonimport/cron')->refreshLicenseExpiryInfo();
	}
	
	public function runeverything(){
		Mage::getModel('amazonimport/cron')->runSurestream();
		Mage::getModel('amazonimport/cron')->orderImport();
		Mage::getModel('amazonimport/cron')->productImport();
		Mage::getModel('amazonimport/cron')->refreshStockOrPrice("Stock");
		Mage::getModel('amazonimport/cron')->refreshStockOrPrice("Price");
		Mage::getModel('amazonimport/cron')->refreshLicenseExpiryInfo();
		Mage::getModel('amazonimport/cron')->runDispatches();
	}
}