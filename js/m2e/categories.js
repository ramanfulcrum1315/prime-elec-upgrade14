CategoryActions = Class.create();
CategoryActions.prototype = {
    initialize: function(templateData){

        this.templateData = templateData;
        this.canditionData = "";

    },

    // Get select with subcategories ====================

    getSelect : function(num, typeFlag){

        var parent_id = 0;

        if (num!=7){
            if (typeFlag == 'second'){
                parent_id = $("secondarycategories_"+num).value;
                document.getElementById('secondaryselected_category').value=parent_id;
            }else{
                parent_id = $("categories_"+num).value;
                document.getElementById('selected_category').value=parent_id;
            }

        }
        
        var site_id = $('marketplace').value;
        var url = categoryNextSelectUrl + 'num/'+num+'/parent/'+parent_id+'/siteid/'+site_id+'/second/'+typeFlag;
        if (num!=7)
            num++;
        else
            num=0;
        for (var i=num; i<6; i++){
            if (typeFlag == 'second'){
                $("secondaryselect_"+i).innerHTML="";
            }else{
                $("select_"+i).innerHTML="";
            }
        }

        new Ajax.Request(url, {
          method: 'post',
          onSuccess: function(transport) {
              if (typeFlag == 'second'){
                    if (transport.responseText!="end")
                        $("secondaryselect_"+num).innerHTML = transport.responseText;
                    if (transport.responseText=="end")
                        $("secondaryconfirm_categories_button").style.display = '';
                }else{
                    if (transport.responseText!="end")
                        $("select_"+num).innerHTML = transport.responseText;
//                    alert(transport.responseText);
                    if (transport.responseText=="end")
                        $("confirm_categories_button").style.display = '';
                }
              }
          }
        );

    },
// Change select category from: ===============================================
    categorySelectedTypeChange: function(element){

    	if (element.value == '')
    		return;
    	
    	if (element.value == '1'){

            $('custom_ebay_category_attribute').style.display = "";
            $('ebay_category').style.display                  = "none";

            AttributesActions.renderAttributes('item_condition_attr', 'item_condition_tr');
          
            $('custom_secondary_ebay_category_attribute').style.display = "";
            $('custom_secondary_ebay_category').style.display           = "none";

            $('specifics_block_error').style.display = "";
            $('specifics_block').style.display = "none";
            $('item_specifics_error').innerHTML = "<b>This eBay Category has no Item Specifics.</b>";

            $('end_category').removeClassName('required-entry');

            if ($('marketplace').value != ""){
                $('payment_warning_b').style.display       = "none";
                $('payment_block').style.display           = "";

                var checkboxes = $$(".payment_class");
                for (i=0; i < checkboxes.length;i++){
                    checkboxes[i].style.display = '';
                    $('payment_text_'+checkboxes[i].value).style.display = ""
                }
            }

        }else{
        	
        	if (!$F('marketplace')) {
                //TODO: i18n
                alert('You may select Marketplace first!');
                element.value = '';
                return false;
            }

            this.getSelect(7);
            this.getSelect(7, 'second');
        	
            $('item_condition_tr').innerHTML = conditionData;
            
            $('custom_ebay_category_attribute').style.display = "none";
            $('ebay_category').style.display                  = "";

            $('specifics_block_error').style.display = "none";
            $('specifics_block').style.display = "";


            $('custom_secondary_ebay_category_attribute').style.display = "none";
            $('custom_secondary_ebay_category').style.display           = "";

            $('end_category').addClassName('required-entry');
            
            if ($('marketplace').value != ""){
                $('payment_warning_b').style.display       = "";
                $('payment_block').style.display           = "none";

                var checkboxes = $$(".payment_class");
                for (i=0; i < checkboxes.length;i++){
                    checkboxes[i].style.display = 'none';
                    $('payment_text_'+checkboxes[i].value).style.display = "none"
                }
            }

        }

    },
    // Actions after marketplace Change: ===============================================
    marketplaceChangeForCategory: function(){

        $('select_0').style.display                    = '';
        $('selected_category_text_span').style.display = 'none';

        $('secondaryselect_0').style.display                    = '';
        $('secondaryselected_category_text_span').style.display = 'none';

        $('category_selected_type').value = this.templateData['category_selected_type'];

            
    },
    //select a right action for categories
    selectConfirmActionForCategory: function(){

        if (this.templateData['category_selected_type'] == '0'){

            this.confirmMainCategory();
            this.confirmSecondaryCategory();

        }else{
        	
        	$('category_selected_type').value = this.templateData['category_selected_type'];
        	this.categorySelectedTypeChange($('category_selected_type'));
            
        }

    },
    // Action for Confirm button in categor============================================
    confirmMainCategory: function (callFlag){

        var cat_id = $('selected_category').value;

        if (cat_id =="")
            return;

        $('br_before_select').style.display = 'none';
             
        var url = getPaymentByCategoryUrl + 'catid/' + cat_id + '/store/' + $('marketplace').value;

        new Ajax.Request(url, {
          method: 'post',
          onSuccess: function(transport) {

              var data = transport.responseText.evalJSON(true);

              $("end_category").value                      = "flag";
              $('category_name_b').innerHTML               = data['category_name'];


              $('select_1').innerHTML = '';
              $('select_2').innerHTML = '';
              $('select_3').innerHTML = '';
              $('select_4').innerHTML = '';
              $('select_5').innerHTML = '';

              window.setTimeout(function()
              {
                  $('select_0').hide();
                  $('selected_category_text_span').show();
                  $('confirm_categories_button').hide();
              }, 400);

              $('payment_block').style.display = "";

              
              conditionData = data['condition'];
              $('item_condition_tr').innerHTML = data['condition'];
              $('item_condition').value = templateData['condition'];
              
              for (var i = 0; i < data['payments'].length; i++){

                    try{
                        $('pay_'+data['payments'][i]).style.display = "";
                        $('payment_text_'+data['payments'][i]).style.display = "";
                    }catch (e){}
                    
                    $('payment_warning_b').style.display = "none";

              }
              CategoryActions.getCategoryAttributes(callFlag);
            }
        });

    },


    // Action for Confirm button in Secondary category============================================
    confirmSecondaryCategory: function (){
        
        var cat_id = $('secondaryselected_category').value;
        if (cat_id == "" || cat_id == 0) {
            return;
        }
 
        $('secondarybr_before_select').style.display = 'none';

        var url = getPaymentByCategoryUrl + 'catid/' + cat_id + '/store/' + $('marketplace').value;
        
        new Ajax.Request(url, {
          method: 'post',
          onSuccess: function(transport) {

              var data = transport.responseText.evalJSON(true);

              $("secondaryend_category").value="flag";

              $('secondarycategory_name_b').innerHTML = data['category_name'];

              $('secondaryselect_1').innerHTML = '';
              $('secondaryselect_2').innerHTML = '';
              $('secondaryselect_3').innerHTML = '';
              $('secondaryselect_4').innerHTML = '';
              $('secondaryselect_5').innerHTML = '';

             window.setTimeout(function()
             {
                 $('secondaryselect_0').hide();
                 $('secondaryselected_category_text_span').show();
                 $('secondaryconfirm_categories_button').hide();
             }, 600);

            }
        });

    },
    getCategoryAttributes: function (callFlag){

        var cat_id = $('selected_category').value;

        if (cat_id =="")
            return;

        var url = getItemsSpecificsByCategoryUrl + 'catid/' + cat_id + '/siteid/' + $('marketplace').value;

        new Ajax.Request(url, {
          method: 'post',
          onSuccess: function(transport, json) {

                var data = transport.responseText.evalJSON(true);

                if (data['number'] == 0){
                    $('specifics_block_error').style.display = "";
                    $('specifics_block').style.display = "none";
                    $('item_specifics_error').innerHTML = "<b>This eBay Category have no Item Specifics.</b>";
                }else{
                    $('specifics_block_error').style.display = "none";
                    $('specifics_block').style.display = "";
                }

                $('specifics_block').innerHTML = data['html'];
              
                for (var i = 0; i < data['number']; i++){

                    AttributesActions.renderAttributes('attribute_custom_attribute_select_'+i, 'attribute_container_'+i, 0, '', i);

                    if (this.specifics[i]['type'] > 0){
                    	
                    	if (callFlag != '1'){
                    		
                    		$('select_view_for_specific_'+i).value = this.specifics[i]['type'];
                        
	                        if (this.specifics[i]['type'] == 1)
	                            $('attributeselect_'+i).value = this.specifics[i]['content'];
	                        if (this.specifics[i]['type'] == 3)
	                            $('attributetext_'+i).value = this.specifics[i]['content'];
	                    	
                    	}
                        AttributesActions.changeAttrbibutesTypeForSpecific(i);

                    }

                }
                
             
            }
        });

    }
};

