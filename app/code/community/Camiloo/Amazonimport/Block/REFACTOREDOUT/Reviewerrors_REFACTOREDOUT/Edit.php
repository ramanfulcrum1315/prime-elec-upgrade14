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

class Camiloo_Amazonimport_Block_Reviewerrors_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'amazonimport';
        $this->_controller = 'manualsetup';
        
        $this->_removeButton('save');
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('back');
	
        
        $this->_addButton('saveincomplete', array(
            'label'     => Mage::helper('adminhtml')->__('Save as Unsolved'),
            'onclick'   => 'savemanualsetup(0);',
            'class'     => 'scalable delete',
        ), -100);
        
        $this->_addButton('savecomplete', array(
            'label'     => Mage::helper('adminhtml')->__('Save as Solved'),
            'onclick'   => 'savemanualsetup(1);',
            'class'     => 'scalable save',
        ), -100);
        
        
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('amazonimport_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'amazonimport_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'amazonimport_content');
                }
            }
            
            var idarray = new Array();
            
            function savemanualsetup(state){
            	$('has_setup_completed').value = state;            	
                $('mainsaveform').submit();

            }

            function clonefrom(){
                window.location.href = $('edit_form').action;
            }
            
            function displayEnterCodeForm(element, currentvalue){
            	$('textedit-xmlkey').value = element;
            	$('textedit-keyvalue').value = currentvalue;
            	$('textedit').submit();
            	setTimeout(\"var editForm = new varienForm('edit_form', '');\", 1500);
   			}
            
            function saveEnterCode(element, currentvalue){
            	var newvalue = $(element+'-newvalue').value;
            	$(element+'-values').innerHTML = \"Please Wait - Saving... <img src='/skin/adminhtml/default/default/images/ajax-loader.gif' alt='Loading...' class='v-middle'>\"; 
            	$(element+'-buttons').innerHTML = \"\";             	
				$('editformopen').value = 0;
            	// pass to our datasave helper form.
            	$('saveform-xmlkey').value = element;
            	$('saveform-keyvalue').value = newvalue;
            	$('saveform').submit();
            	setTimeout(\"var editForm = new varienForm('edit_form', '');\", 1500);
            	
            }

            function cancelUpdate(){
            	$('editformopen').value = 0;
            	var element = $('canceledit-xmlkey').value;
            	$(element+'-values').innerHTML = $('canceledit-elementcontents').value;
            	$(element+'-buttons').innerHTML = $('canceledit-buttoncontents').value;
            	$('canceledit-elementcontents').value = '';
            	$('canceledit-buttoncontents').value = '';
            	setTimeout(\"var editForm = new varienForm('edit_form', '');\", 1500);
            }
            
            function saveEntryFormWithIndex(index){
            
          		$('editformopen').value = 0;

          		var arraytosend = new Array();
          		
          		for (var i=0; i < idarray.length; ++i){   

          				if($(idarray[i]).options){
          					arraytosend[''+idarray[i]+''] = $(idarray[i]).options[$(idarray[i]).selectedIndex].value;
						}else{
							if($(idarray[i]).type == 'radio'){
							//	arraytosend[''+idarray[i]+''] = $(idarray[i]).value;
							}else{
								arraytosend[''+idarray[i]+''] = $(idarray[i]).value;
							}
    					}
    			}
				
				idarray = [];
				
				$('serialized_values').value = serialize(arraytosend);
				$('saveentry_element_index').value = index;
          		$('saveentry').submit();
            	
          		var element = $('canceledit-xmlkey').value;
            	if(index == -1 || element.indexOf('-') > 0) {
	            	$(element+'-values').innerHTML = \"Please Wait - Saving... <img src='".Mage::getModel('amazonimport/amazonimport')->getSkinUrl()."skin/adminhtml/default/default/images/ajax-loader.gif' alt='Loading...' class='v-middle'>\"; 
	            	$(element+'-buttons').innerHTML = \"\";   
            	}
            	else {
            	
	            	$(element+'-'+index+'-values').innerHTML = \"Please Wait - Saving... <img src='".Mage::getModel('amazonimport/amazonimport')->getSkinUrl()."skin/adminhtml/default/default/images/ajax-loader.gif' alt='Loading...' class='v-middle'>\"; 
	            	$(element+'-'+index+'-buttons').innerHTML = \"\";   
            	}
            }
            
            function saveEntryForm(){
            	saveEntryFormWithIndex(-1);
            }            
            function displayEntryForm(element, currentvalue){
            
            	if($('editformopen').value == 0){
            		$('canceledit-xmlkey').value = element;
            		$('canceledit-elementcontents').value = $(element+'-values').innerHTML;
            		$('canceledit-buttoncontents').value = $(element+'-buttons').innerHTML;
            		$(element+'-values').innerHTML = \"Please Wait - Loading Values... <img src='/skin/adminhtml/default/default/images/ajax-loader.gif' alt='Loading...' class='v-middle'>\"; 
            		$('getentryform-xmlkey').value = element;
            		$('getentryform-currentvalue').value = currentvalue;
            		$('editformopen').value = 1;
            		$('getentryform').submit();
            		$('getentryform-currentvalue').value = '';
            	}else{
            		alert('An attribute is currently being edited. Please save or cancel this edit before selecting another attribute to edit');
            	}
            }
            
            function displayEntryFormWithIndex(element, currentvalue, index){
            	
	            if (index == -1) {
	            	displayEntryForm(element, currentvalue);
	            }
            	else {
	            	if($('editformopen').value == 0){
	            		$('canceledit-xmlkey').value = element;
	            		$('canceledit-elementcontents').value = $(element+'-'+index+'-values').innerHTML;
	            		$('canceledit-buttoncontents').value = $(element+'-'+index+'-buttons').innerHTML;
	            		$(element+'-'+index+'-values').innerHTML = \"Please Wait - Loading Values... <img src='".Mage::getModel('amazonimport/amazonimport')->getSkinUrl()."skin/adminhtml/default/default/images/ajax-loader.gif' alt='Loading...' class='v-middle'>\"; 
	            		$('getentryform-xmlkey').value = element;
	            		$('getentryform-currentvalue').value = currentvalue;
	            		$('getentryform-repelemindex').value = index;
	            		$('editformopen').value = 1;
	            		$('getentryform').submit();
	            		$('getentryform-currentvalue').value = '';
	            	}else{
	            		alert('An attribute is currently being edited. Please save or cancel this edit before selecting another attribute to edit');
	            	}
            	}
            }
            
        ";
    }

    public function getHeaderText()
    {
        
            return Mage::helper('amazonimport')->__('Manual Setup of Amazon Listing');
        
    }
}