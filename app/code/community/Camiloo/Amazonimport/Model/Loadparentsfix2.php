<?php
class Camiloo_Amazonimport_Model_Loadparentsfix2 extends Mage_Catalog_Model_Product
{
	public function loadParentProductIds()
	{
	        return $this->_getResource()->getParentProductIds($this);
	}
}
?>