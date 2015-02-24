<?php

	class Camiloo_Amazonimport_Block_Refresher extends Mage_Adminhtml_Block_Template {
		
		  public function __construct()
  		  {
		
				echo "<form id=\"form1\" name=\"form1\" method=\"post\" action=\"/amazonimport/listingstool/upload/\">
					 	 <input type=\"hidden\" name=\"form_key\" id=\"form_key\" value=\"".$this->getFormkey()."\">
						 <input type=\"hidden\" name=\"country\" id=\"country\" value=\"".$this->getRequest()->getPost('country')."\">
					  </form>
					  <script type=\"text/javascript\">
						setTimeout(\"document.getElementById('form1').submit()\",200);
					</script>";
	
		  }
	}