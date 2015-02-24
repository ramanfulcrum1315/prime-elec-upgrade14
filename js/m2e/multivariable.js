MultivariableActions = Class.create();
MultivariableActions.prototype = {
    initialize: function(templateData){

        this.templateData = templateData;
        

    },
    //Changes Store get From Type (Attributes or Api)
    variationPriceModeChange: function(){

        if ($('variation_price_mode').value == '3'){
            $('custom_variation_price_attribute_tr').style.display = '';
        }
        else{
            $('custom_variation_price_attribute_tr').style.display = 'none';
        }

    },
    multivariationModeChange: function(){

        if ($('use_multivariation').value == '1' || $('use_multivariation_custom').value == '1' || $('use_multivariation_bundle').value == '1' || $('use_multivariation_grouped').value == '1'){
            
            $('variation_price_tr').style.display = '';
            $('variation_sku_tr').style.display = '';
            $('variation_qty_tr').style.display = '';

            if ($('use_multivariation').value == '1') {
                $('use_configurable_tr').style.display = '';
                $('variation_images_tr').style.display = '';
            } else {
                $('use_configurable_tr').style.display = 'none';
                $('variation_images_tr').style.display = 'none';
            }

            $('multi_options').style.display = '';
            
        }
        else{

            $('variation_price_tr').style.display = 'none';
            $('variation_sku_tr').style.display = 'none';
            $('variation_qty_tr').style.display = 'none';
            $('variation_qty_tr_1').style.display = 'none';
            $('variation_qty_tr_2').style.display = 'none';
            $('custom_variation_price_attribute_tr').style.display = 'none';
            $('use_configurable_tr').style.display = 'none';
            $('multi_options').style.display = 'none';
            


        }

    },
    variableQtyModeChange: function(){

        var qMode = $('variation_qty_mode').value;

        if (qMode == '2'){

            $('variation_qty_tr_1').style.display = '';
            $('variation_qty_tr_2').style.display = 'none';

        }else if (qMode == '3'){

            $('variation_qty_tr_1').style.display = 'none';
            $('variation_qty_tr_2').style.display = '';
            
        }else{

            $('variation_qty_tr_1').style.display = 'none';
            $('variation_qty_tr_2').style.display = 'none';

        }

    }

};

