blockedShippings = [];

function blockShipping(id)
{
    blockedShippings[blockedShippings.length] = id;
}


function copyShippingValue(el)
{
    var isInternational = el.id.substr(0, 1) == 'i';
    var shippingName = el.id.replace(isInternational ? 'iship_text_' : 'shippingcost_text_', '');

    if (isInternational) {
        if (!$('ishipaddit_text_' + shippingName).value || (blockedShippings.indexOf(el.id) == -1)) {
            $('ishipaddit_text_' + shippingName).value = el.value;
        }
    } else {
        if (!$('shippingadditcost_text_' + shippingName).value || (blockedShippings.indexOf(el.id) == -1)) {
            $('shippingadditcost_text_' + shippingName).value = el.value;
        }
    }
}

ShippingActions = Class.create();
ShippingActions.prototype = {
    initialize: function(templateData, shipping){

        this.templateData = templateData;
        this.shipping = shipping;
        this.firstLoad = 0;
        
       
    },
    getShippingArray: function(){

    var url = getShippingArrayUrl + 'siteid/' + $('marketplace').value;

	new Ajax.Request(url, {
	  method: 'post',
	  onSuccess: function(transport) {
	    if (transport.responseText!="end"){
                
	    	var data = transport.responseText.evalJSON(true);
	    	$('shipping_block').innerHTML               = data['shipping'];
	    	$('international_shipping_block').innerHTML = data['international'];
	    	$('ship_to_td').innerHTML                   = data['location'];


                    if(ShippingActions.templateData['templateId'] != ""){

//                        try{
                            for (var i = 0; i <= 200; i++){
                                if (ShippingActions.shipping[i]['shipping_value'] != ""){
                                    if (ShippingActions.shipping[i]['shipping_type'] == '0'){
                                        $('ship_' + ShippingActions.shipping[i]['shipping_value']).checked                          = "checked";
                                		$('shippingprior_'+ShippingActions.shipping[i]['shipping_value']).disabled = false;
                                        $('smcs_' + ShippingActions.shipping[i]['shipping_value']).value = ShippingActions.shipping[i]['cost_mode'];
                                        ShippingActions.changeCostMode(ShippingActions.shipping[i]['shipping_value']);
                                        if (ShippingActions.shipping[i]['cost_mode'] == '1') {
                                            $('shippingcost_text_' + ShippingActions.shipping[i]['shipping_value']).value   = ShippingActions.shipping[i]['cost_value'];
                                            $('shippingadditcost_text_' + ShippingActions.shipping[i]['shipping_value']).value   = ShippingActions.shipping[i]['cost_additional_items'];
                                        }
                                        $('shippingprior_' + ShippingActions.shipping[i]['shipping_value']).value   = ShippingActions.shipping[i]['priority'];

                                        $('smcs_'+ShippingActions.shipping[i]['shipping_value']).disabled = false;
                                    }
                                    if (ShippingActions.shipping[i]['shipping_type'] == '1'){
                                        $('international_ship_' + ShippingActions.shipping[i]['shipping_value']).checked                          = "checked";
                                        $('shippingprior_'+ShippingActions.shipping[i]['shipping_value']).disabled = false;
                                        $('ismcs_' + ShippingActions.shipping[i]['shipping_value']).value = ShippingActions.shipping[i]['cost_mode'];
                                        ShippingActions.changeInternationalCostMode(ShippingActions.shipping[i]['shipping_value']);
                                        if (ShippingActions.shipping[i]['cost_mode'] == '1') {
                                            $('iship_text_' + ShippingActions.shipping[i]['shipping_value']).value   = ShippingActions.shipping[i]['cost_value'];
                                            $('ishipaddit_text_' + ShippingActions.shipping[i]['shipping_value']).value   = ShippingActions.shipping[i]['cost_additional_items'];
                                        }
                                        $('shippingprior_' + ShippingActions.shipping[i]['shipping_value']).value   = ShippingActions.shipping[i]['priority'];
                                        $('ismcs_'+ShippingActions.shipping[i]['shipping_value']).disabled = false;
                                    }
                                }else
                                    break;
                            }
//                        }catch(e){}

                        // Checked Ship To location checkboxes
                        if (ShippingActions.templateData['ship_to_location'] != ""){
                        	
                        	var locationArray      = ShippingActions.templateData['ship_to_location'];
                        	for (var k = 0; k < locationArray.length; k++){
                            
                        			if (locationArray[k]['name'] != ""){
                        				
                        				//##!! $('shippingLocCost_'+locationArray[k]['name']).value = locationArray[k]['price'];
                                        $('shippingLocCost_'+locationArray[k]['name']).value = 1;
                        				$('shippingLocCost_'+locationArray[k]['name']).disabled = false;

                        				$('shippingLocation_' + locationArray[k]['name']).checked = "checked";
                        				
                                    }

                            }
                        }

                    }
            }

	  }
	});

    },
    changeCostMode: function(shippingName){

    	if ($('use_domestic_calculated').value == '1')
    		$('smcs_' + shippingName).value = '3';
    	
        switch($('smcs_' + shippingName).value){
            
            case '0':
                $('shippingcost_text_' + shippingName).style.display   = "none";
                $('shippingadditcost_text_' + shippingName).style.display   = "none";
                $('shippingcost_select_' + shippingName).style.display = "none";
                $('shippingadditcost_select_' + shippingName).style.display = "none";
//                alert(this.checkShippingPaymentMethods());
                if (this.checkShippingPaymentMethods())
                	$('use_domestic_calculated').value = 0;
                this.calculatedShippingMode(1);
                break;
            case '1':
                $('shippingcost_text_' + shippingName).style.display   = "";
                $('shippingadditcost_text_' + shippingName).style.display   = "";
                $('shippingcost_select_' + shippingName).style.display = "none";
                $('shippingadditcost_select_' + shippingName).style.display = "none";
                if (this.checkShippingPaymentMethods())
                	$('use_domestic_calculated').value = 0;
                this.calculatedShippingMode(1);
                break;
            case '2':
                $('shippingcost_text_' + shippingName).style.display   = "none";
                $('shippingadditcost_text_' + shippingName).style.display   = "none";
                AttributesActions.renderAttributes('shippingcost_select_'+shippingName, 'shippingcost_select_'+shippingName+'_span', 0, '170', '', [shippingName, false]);
                AttributesActions.renderAttributes('shippingadditcost_select_'+shippingName, 'shippingadditcost_select_'+shippingName+'_span', 0, '170', '', [shippingName, true]);
                $('shippingcost_select_' + shippingName).style.display = "";
                $('shippingadditcost_select_' + shippingName).style.display = "";
                if (this.checkShippingPaymentMethods())
                	$('use_domestic_calculated').value = 0;
                this.calculatedShippingMode(1);
                break;
            case '3':
            	$('shippingcost_text_' + shippingName).style.display   = "none";
                $('shippingadditcost_text_' + shippingName).style.display   = "none";
                $('shippingcost_select_' + shippingName).style.display = "none";
                $('shippingadditcost_select_' + shippingName).style.display = "none";
                $('use_domestic_calculated').value = 1;
                this.calculatedShippingMode(1);
                break;
            default:
                break;
                

        }

    },
    simpleChangeCostMode: function(shippingName){

        
        switch($('smcs_' + shippingName).value){
            
            case '0':
                $('shippingcost_text_' + shippingName).style.display   = "none";
                $('shippingadditcost_text_' + shippingName).style.display   = "none";
                $('shippingcost_select_' + shippingName).style.display = "none";
                $('shippingadditcost_select_' + shippingName).style.display = "none";
//                alert(this.checkShippingPaymentMethods());
                if (this.checkShippingPaymentMethods())
                	$('use_domestic_calculated').value = 0;
                break;
            case '1':
                $('shippingcost_text_' + shippingName).style.display   = "";
                $('shippingadditcost_text_' + shippingName).style.display   = "";
                $('shippingcost_select_' + shippingName).style.display = "none";
                $('shippingadditcost_select_' + shippingName).style.display = "none";
                if (this.checkShippingPaymentMethods())
                	$('use_domestic_calculated').value = 0;
                break;
            case '2':
                $('shippingcost_text_' + shippingName).style.display   = "none";
                $('shippingadditcost_text_' + shippingName).style.display   = "none";
                AttributesActions.renderAttributes('shippingcost_select_'+shippingName, 'shippingcost_select_'+shippingName+'_span', 0, '170', '', [shippingName, false]);
                AttributesActions.renderAttributes('shippingadditcost_select_'+shippingName, 'shippingadditcost_select_'+shippingName+'_span', 0, '170', '', [shippingName, true]);
                $('shippingcost_select_' + shippingName).style.display = "";
                $('shippingadditcost_select_' + shippingName).style.display = "";
                if (this.checkShippingPaymentMethods())
                	$('use_domestic_calculated').value = 0;
                break;
            case '3':
            	$('shippingcost_text_' + shippingName).style.display   = "none";
                $('shippingadditcost_text_' + shippingName).style.display   = "none";
                $('shippingcost_select_' + shippingName).style.display = "none";
                $('shippingadditcost_select_' + shippingName).style.display = "none";
                $('use_domestic_calculated').value = 1;
                break;
            default:
                break;
                

        }

    },
    checkShippingPaymentMethods: function(){
    	var calculatedFlag = true;
    	var domesticElements = document.getElementsByClassName('shipping_attibutes_selects');
    	for (var i=0;i<domesticElements.length;i++){
    		if (domesticElements[i].value == '3')
    			calculatedFlag = false;
    	}
    	return calculatedFlag;
    },
    checkInternationalShippingPaymentMethods: function(){
    	var calculatedFlag = true;
    	var internationalElements = document.getElementsByClassName('shipping_attibutes_selects_int');
    	for (var i=0;i<internationalElements.length;i++){
    		if (internationalElements[i].value == '3')
    			calculatedFlag = false;
    	}
    	return calculatedFlag;
    },
    changeInternationalCostMode: function(shippingName){

    	if ($('use_int_calculated').value == '1')
    		$('ismcs_' + shippingName).value = '3';
    	
        switch($('ismcs_' + shippingName).value){

            case '0':
                $('iship_text_' + shippingName).style.display   = "none";
                $('ishipaddit_text_' + shippingName).style.display   = "none";
                $('iship_select_' + shippingName).style.display = "none";
                $('ishipaddit_select_' + shippingName).style.display = "none";
                if (this.checkInternationalShippingPaymentMethods())
                	$('use_int_calculated').value = 0;
                this.calculatedShippingMode(1);
                break;
            case '1':
                $('iship_text_' + shippingName).style.display   = "";
                $('ishipaddit_text_' + shippingName).style.display   = "";
                $('iship_select_' + shippingName).style.display = "none";
                $('ishipaddit_select_' + shippingName).style.display = "none";
                if (this.checkInternationalShippingPaymentMethods())
                	$('use_int_calculated').value = 0;
                this.calculatedShippingMode(1);
                break;
            case '2':
                $('iship_text_' + shippingName).style.display   = "none";
                $('ishipaddit_text_' + shippingName).style.display   = "none";
                AttributesActions.renderAttributes('iship_select_'+shippingName, 'iship_select_'+shippingName+'_span', 0, '170', '', [shippingName, false]);
                AttributesActions.renderAttributes('ishipaddit_select_'+shippingName, 'ishipaddit_select_'+shippingName+'_span', 0, '170', '', [shippingName, true]);
                $('iship_select_' + shippingName).style.display = "";
                $('ishipaddit_select_' + shippingName).style.display = "";
                if (this.checkInternationalShippingPaymentMethods())
                	$('use_int_calculated').value = 0;
                this.calculatedShippingMode(1);
                break;
            case '3':
                $('iship_text_' + shippingName).style.display   = "none";
                $('ishipaddit_text_' + shippingName).style.display   = "none";
                $('iship_select_' + shippingName).style.display = "none";
                $('ishipaddit_select_' + shippingName).style.display = "none";
                $('use_int_calculated').value = 1;
                this.calculatedShippingMode(1);
                break;
            
            default:
                break;


        }

    },
    simpleChangeInternationalCostMode: function(shippingName){

        switch($('ismcs_' + shippingName).value){

            case '0':
                $('iship_text_' + shippingName).style.display   = "none";
                $('ishipaddit_text_' + shippingName).style.display   = "none";
                $('iship_select_' + shippingName).style.display = "none";
                $('ishipaddit_select_' + shippingName).style.display = "none";
                if (this.checkInternationalShippingPaymentMethods())
                	$('use_int_calculated').value = 0;
                break;
            case '1':
                $('iship_text_' + shippingName).style.display   = "";
                $('ishipaddit_text_' + shippingName).style.display   = "";
                $('iship_select_' + shippingName).style.display = "none";
                $('ishipaddit_select_' + shippingName).style.display = "none";
                if (this.checkInternationalShippingPaymentMethods())
                	$('use_int_calculated').value = 0;
                break;
            case '2':
                $('iship_text_' + shippingName).style.display   = "none";
                $('ishipaddit_text_' + shippingName).style.display   = "none";
                AttributesActions.renderAttributes('iship_select_'+shippingName, 'iship_select_'+shippingName+'_span', 0, '170', '', [shippingName, false]);
                AttributesActions.renderAttributes('ishipaddit_select_'+shippingName, 'ishipaddit_select_'+shippingName+'_span', 0, '170', '', [shippingName, true]);
                $('iship_select_' + shippingName).style.display = "";
                $('ishipaddit_select_' + shippingName).style.display = "";
                if (this.checkInternationalShippingPaymentMethods())
                	$('use_int_calculated').value = 0;
                break;
            case '3':
                $('iship_text_' + shippingName).style.display   = "none";
                $('ishipaddit_text_' + shippingName).style.display   = "none";
                $('iship_select_' + shippingName).style.display = "none";
                $('ishipaddit_select_' + shippingName).style.display = "none";
                $('use_int_calculated').value = 1;
                break;
            default:
                break;


        }

    },
    calculatedShippingMode: function(nonDirect){
    	
    	if (($('use_shipping').value == '1' && $('use_domestic_calculated').value == '1') || ($('use_int_calculated').value == '1' && $('international_use_shipping').value == '1')){
        	
    		$('calculating_shipping_tr1').style.display = '';
    		$('calculating_shipping_tr2').style.display = '';
    		$('calculating_shipping_tr3').style.display = '';
    		$('calculating_shipping_tr3_1').style.display = '';
    		$('calculating_shipping_tr3_11').style.display = '';
    		
    		$('calculating_shipping_tr1_1').style.display = '';
			$('calculating_shipping_tr2_1').style.display = '';
			$('calculating_shipping_tr3_1').style.display = '';
			$('calculating_shipping_tr4_1').style.display = '';
			
			$('calculated_block').style.display = '';
			$('international_shipping_warning_td').style.display = 'none';
    		
    		if ($('use_domestic_calculated').value == '1'){
    			$('calculating_shipping_tr4').style.display = '';
    			$('calculating_shipping_tr4_1').style.display = '';
        	}else{
    			$('calculating_shipping_tr4').style.display = 'none';
    			$('calculating_shipping_tr4_1').style.display = 'none';
            }
    			
    		if ($('use_int_calculated').value == '1'){
    			$('calculating_shipping_tr5').style.display = '';
    			$('calculating_shipping_tr5_1').style.display = '';
        	}else{
    			$('calculating_shipping_tr5').style.display = 'none';
    			$('calculating_shipping_tr5_1').style.display = 'none';
            }
    		
    		    		
    		    	
//    		if (nonDirect != 1){		
    				
				if ($('use_int_calculated').value == '1' && $('international_use_shipping').value == '1'){
		    		var checks = document.getElementsByClassName('shipping_attibutes_selects_int');
		    		for(var i=0; i < checks.length;i++){
		    			checks[i].value='3';
		    			var elId = checks[i].id;
		    			var curElementValue = elId.replace('ismcs_',"");
		    			this.simpleChangeInternationalCostMode(curElementValue);
		    		}
				}else{
					if (nonDirect != 1){
			    		var checks = document.getElementsByClassName('shipping_attibutes_selects_int');
			    		for(var i=0; i<checks.length;i++){
			    			checks[i].value='0';
			    		}
					}
				}
	    		
	    		if ($('use_shipping').value == '1' && $('use_domestic_calculated').value == '1'){		
		    		var checks = document.getElementsByClassName('shipping_attibutes_selects');
		    		for(var i=0; i<checks.length;i++){
		    			checks[i].value='3';
		    			var elId = checks[i].id;
		    			var curElementValue = elId.replace('smcs_',"");
		    			this.simpleChangeCostMode(curElementValue);
		    		}
				}else{
					if (nonDirect != 1){
			    		var checks = document.getElementsByClassName('shipping_attibutes_selects');
			    		for(var i=0; i<checks.length;i++){
			    			checks[i].value='0';
			    		}
					}
				}
    		
//    		}
    		
    				
    		
    	}else{
    	
    		if (nonDirect != 1 && firstLoad == 1){
	    		var checks = document.getElementsByClassName('shipping_attibutes_selects_int');
	    		for(var i=0; i<checks.length;i++){
	    			checks[i].value='0';
	    		}
	    		
	    		var checks = document.getElementsByClassName('shipping_attibutes_selects');
	    		for(var i=0; i<checks.length;i++){
	    			checks[i].value='0';
	    		}
    		}    	    					    		
    				
			$('calculating_shipping_tr1').style.display = 'none';
			
			$('calculated_block').style.display = 'none';
			
			$('lbs').value 						  = '';
	        $('oz').value 						  = '';
	        $('package_size').value 			  = 'None';
	        $('calculate_width').value 			  = '';
	        $('calculate_height').value 		  = '';
	        $('calculate_depth').value 			  = '';
	        $('domestic_handling_fee').value 	  = '';
	        $('international_handling_fee').value = '';
	        $('post_code').value 				  = '';
	        $('calculation_weght_mode').value 	  = '1';
	        $('package_size_mode').value 		  = '1';
	        $('dimentions_mode').value 			  = '1';
	        $('handling_feed_mode').value 		  = '1';
	        $('inthandling_feed_mode').value 	  = '1';
	        
	        AttributesActions.changeCalculationWeghtMode();
	        AttributesActions.changeCalculationPackageSizeMode();
	        AttributesActions.changeCalculationDimentionsMode();
	        AttributesActions.changeCalculationHandlingFeeMode();
	        AttributesActions.changeCalculationIntHandlingFeeMode();
			
			$('calculating_shipping_tr1_1').style.display = 'none';
			$('calculating_shipping_tr2_1').style.display = 'none';
			$('calculating_shipping_tr3_1').style.display = 'none';
			$('calculating_shipping_tr4_1').style.display = 'none';
			$('calculating_shipping_tr5_1').style.display = 'none';
			$('calculating_shipping_tr3_11').style.display = 'none';
			
    		$('calculating_shipping_tr2').style.display = 'none';
    		$('calculating_shipping_tr3').style.display = 'none';
    		$('calculating_shipping_tr3_1').style.display = 'none';
    		
    		$('calculating_shipping_tr4').style.display = 'none';
    		$('calculating_shipping_tr5').style.display = 'none';
        	
    	}
    	
    	
    	
    }
};
