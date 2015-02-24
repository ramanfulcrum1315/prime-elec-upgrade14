GeneralActions = Class.create();
GeneralActions.prototype = {
    initialize: function(templateData){

        this.templateData = templateData;

	},
	auctionChange : function(click){
		
		var auctionType = $('action_type').value;

	    if (auctionType == 'Chinese'){

	    	$('best_offer_block_error').style.display = "";
	    	$('best_offer_block').style.display          = "none";
	    	
	        $('qty_template').value               = 1;
	        $('qty_template').readOnly 			  = true;
	        $('use_qty_attributes').style.display = 'none';
	        $('durationId1').style.display        = '';

	        $('durationId3').style.display='';
	        $('durationId5').style.display='';
	        $('durationId7').style.display='';
	        $('durationId10').style.display='';

	        if (click == 1)
	            $('durationId3').selected='selected';

	        $('durationId30').style.display='none';
	        $('durationId100').style.display='none';


	        $('item_qty_3_tr').style.display='none';


	        $('item_qty_mode0').style.display='none';
	        $('item_qty_mode2').style.display='none';
	        $('item_qty_mode3').style.display='none';
	        $('item_qty_mode').value = '1';

	        if ($('accounts').value != ""){

	                $('store_name_string_tr').style.display = 'none';
	                $('warning_store_b').innerHTML = "<b>Please select 'Store Inventory' Listing to have access to this section</b>";
	                $('warning_store_b').style.display = "";
	                $('tr_with_categories').style.display = "none";
	                $('tr_with_categories_attributes').style.display = "none";
	                $('storecategory_selected_type_tr').style.display = "none";

	        }

	    }

	    if (auctionType == 'FixedPriceItem'){
	        
	    	$('best_offer_block_error').style.display = "none";
	    	$('best_offer_block').style.display          = "";
	    	
	        $('qty_template').readOnly = false;
	        $('use_qty_attributes').style.display = '';
	        $('durationId1').style.display='none';

	        if (click == 1)
	            $('durationId3').selected='selected';
	        $('durationId3').style.display='';
	        $('durationId5').style.display='';
	        $('durationId7').style.display='';
	        $('durationId10').style.display='';
	        $('durationId30').style.display='';
	        $('durationId100').style.display='';

	        $('item_qty_mode0').style.display='';
	        $('item_qty_mode2').style.display='';
	        $('item_qty_mode3').style.display='';

	       if ($('accounts').value != ""){


	                $('store_name_string_tr').style.display = '';
	                $('warning_store_b').style.display = "none";
	                StoreActions.storeGetFromTypeChange();
	                $('storecategory_selected_type_tr').style.display = "";

	        }


	    }
	    if (auctionType == 'StoresFixedPrice'){

//	    	$('second_chance_offer_block').style.display = "none";
	    	$('best_offer_block').style.display          = "";
	       
	        $('qty_template').readOnly 					 = false;
	        $('use_qty_attributes').style.display 		 = '';
	        $('durationId1').style.display				 = 'none';

	        $('durationId3').style.display				 = 'none';
	        $('durationId5').style.display				 = 'none';
	        $('durationId7').style.display				 = 'none';
	        $('durationId10').style.display				 = 'none';
	        $('durationId30').selected					 = 'selected';

	        $('durationId30').style.display				 = '';
	        $('durationId100').style.display			 = '';
	        $('item_qty_mode0').style.display			 = '';
	        $('item_qty_mode2').style.display			 = '';
	        $('item_qty_mode3').style.display			 = '';

	        if ($('accounts').value != ""){

	                $('store_name_string_tr').style.display = '';
	                $('warning_store_b').style.display 		= "none";
	                
	                StoreActions.storeGetFromTypeChange();
	                
	                $('storecategory_selected_type_tr').style.display = "";

	        }

	    }

	    this.auctionChangePrice();
		
    },
    auctionChangePrice: function(){
    
    	var auctionType = $('action_type').value;

	    if (auctionType == 'FixedPriceItem'){

	        $('reserve_tr_1').style.display = 'none';
	        $('reserve_price_mode_tr').style.display = 'none';

	        $('buy_tr_1').style.display = 'none';
	        $('buynow_price_mode_tr').style.display = 'none';

	        $('reserve_price_mode').value = "0";
	        $('buynow_price_mode').value = "0";
	       
	    }
	    if (auctionType == 'StoresFixedPrice'){
	        $('reserve_tr_1').style.display = 'none';
	        $('reserve_price_mode_tr').style.display = 'none';

	        $('buy_tr_1').style.display = 'none';
	        $('buynow_price_mode_tr').style.display = 'none';

	        $('reserve_price_mode').value = "0";
	        $('buynow_price_mode').value = "0";

	    }
    	
    }
}