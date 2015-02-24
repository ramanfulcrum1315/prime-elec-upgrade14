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

class Camiloo_Amazonimport_Block_Mapping_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'amazonimport';
        $this->_controller = 'mapping';
        
        $this->_removeButton('save');
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('back');
		
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('amazonimport_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'amazonimport_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'amazonimport_content');
                }
            }
            
            function displayEnterCodeFormWithIndex(element, currentvalue, index){
            	$('textedit-xmlkey').value = element;
            	$('textedit-keyvalue').value = currentvalue;
            	$('textedit-repelemindex').value = index;
            	$('textedit').submit();
   			}
            
            function displayEnterCodeForm(element, currentvalue){
            	$('textedit-xmlkey').value = element;
            	$('textedit-keyvalue').value = currentvalue;
            	$('textedit').submit();
   			}
   			
   			function saveEnterCodeWithIndex(element, currentvalue, index){
   			
   				var newvalue = '';
   				
   				if (index != -1) {
   					newvalue = $(element+'-'+index+'-newvalue').value;
   				}
   				else {
   					newvalue = $(element+'-newvalue').value;
   				}
   				
            	if ($('myattributeid')) {
            		$('saveform-attrvalue').value = $('myattributeid').value;
            	}
            	
            	if ($('myattributeid-'+index)) {
            		$('saveform-attrvalue').value = $('myattributeid-'+index).value;
            	}
            	
            	if($('myattributename')) {
            		$('saveform-attrkey').value = $('myattributename').value;
            	}
            	
            	if($('myattributename-'+index)) {
            		$('saveform-attrkey').value = $('myattributename-'+index).value;
            	}
            	
            	if (index == -1) {
            	
	            	$(element+'-values').innerHTML = \"Please Wait - Saving... <img src='".Mage::getModel('amazonimport/amazonimport')->getSkinUrl()."skin/adminhtml/default/default/images/ajax-loader.gif' alt='Loading...' class='v-middle'>\"; 
	            	$(element+'-buttons').innerHTML = \"\";        	

            	}
            	else
            	{
            		$(element+'-'+index+'-values').innerHTML = \"Please Wait - Saving... <img src='".Mage::getModel('amazonimport/amazonimport')->getSkinUrl()."skin/adminhtml/default/default/images/ajax-loader.gif' alt='Loading...' class='v-middle'>\"; 
	            	$(element+'-'+index+'-buttons').innerHTML = \"\";   
            	}
            	
            	// pass to our datasave helper form.
            	$('saveform-repelemindex').value = index;
            	$('saveform-xmlkey').value = element;
            	$('saveform-keyvalue').value = newvalue;
            	$('saveform').submit();
   			}
            
            function saveEnterCode(element, currentvalue){
            	saveEnterCodeWithIndex(element, currentvalue, -1);
            }

            function cancelUpdate(element, currentvalue){
				// pass to our datasave helper form.
            	$('canceledit-xmlkey').value = element;
            	$('canceledit-keyvalue').value = currentvalue;
            	$('canceledit').submit();
            }
            
            function cancelUpdateWithIndex(element, currentvalue, index){
				// pass to our datasave helper form.
            	$('canceledit-xmlkey').value = element;
            	$('canceledit-keyvalue').value = currentvalue;
            	$('canceledit-repelemindex').value = index;
            	$('canceledit').submit();	
            }
            
            function saveSelectForm(element, currentvalue){
            	var newvalue = $(element+'-newvalue').options[$(element+'-newvalue').selectedIndex].value;
            	
            	$(element+'-values').innerHTML = \"Please Wait - Saving... <img src='".Mage::getModel('amazonimport/amazonimport')->getSkinUrl()."skin/adminhtml/default/default/images/ajax-loader.gif' alt='Loading...' class='v-middle'>\"; 
            	$(element+'-buttons').innerHTML = \"\";     
            	
            	// pass to our datasave helper form.
            	$('saveform-xmlkey').value = element;
            	$('saveform-keyvalue').value = newvalue;
            	$('saveform').submit();
            }
            
            function displaySelectForm(element, currentvalue){
            	$(element+'-values').innerHTML = \"Please Wait - Loading Options... <img src='".Mage::getModel('amazonimport/amazonimport')->getSkinUrl()."skin/adminhtml/default/default/images/ajax-loader.gif' alt='Loading...' class='v-middle'>\"; 
            	$('getattributes-xmlkey').value = element;
            	$('getattributes-keyvalue').value = currentvalue;
            	$('getattributes').submit();
            }
            
            function displaySelectFormWithIndex(element, currentvalue, index){
	            if (index == -1) {
	            	$(element+'-values').innerHTML = \"Please Wait - Loading Options... <img src='".Mage::getModel('amazonimport/amazonimport')->getSkinUrl()."skin/adminhtml/default/default/images/ajax-loader.gif' alt='Loading...' class='v-middle'>\"; 
	            	
            	}
            	else {
            		$(element+'-'+index+'-values').innerHTML = \"Please Wait - Loading Options... <img src='".Mage::getModel('amazonimport/amazonimport')->getSkinUrl()."skin/adminhtml/default/default/images/ajax-loader.gif' alt='Loading...' class='v-middle'>\"; 
	            
            	}
            	$('getattributes-xmlkey').value = element;
            	$('getattributes-keyvalue').value = currentvalue;
            	$('getattributes-repelemindex').value = index;
            	$('getattributes').submit();
            }
        ";
    }

    public function getHeaderText()
    {
        
            return Mage::helper('amazonimport')->__('Field Mapping Setup');
        
    }
}