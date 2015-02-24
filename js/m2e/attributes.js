AttributesActions = Class.create();
AttributesActions.prototype = {
	initialize: function (templateData, specifics, shipping) {

		this.templateData = templateData;
		this.specifics = specifics;
		this.shipping = shipping;

	},
	// Set selects for all fields using attributes
	getAllAttributesForSet: function ()
	{
		// reset cached data
		// NB! this is static property
		AttributesActions.attrData = '';

		var setid = $('attribute_set').value;

		if (!setid)
			return;

		this.getAttributesForConfigurable();
		this.getTemplatesListsForSet(setid);

		this.prepareAttributes(setid);

		this.renderAttributes('use_price_from', 'use_price_from_span');
		this.renderAttributes('use_reserve_from', 'use_reserve_from_span');
		this.renderAttributes('use_now_from', 'use_now_from_span');
		this.renderAttributes('select_attributes_for_name', 'select_attributes_for_name_span', 0, '150');
		this.renderAttributes('select_attributes_for_subtitle', 'select_attributes_for_subtitle_span', 0, '150');
		this.renderAttributes('select_attributes', 'select_attributes_span');
		this.renderAttributes('select_attributes_for_qty', 'select_attributes_for_qty_span');
		this.renderAttributes('main_category_attribute', 'main_category_attribute_container');
		this.renderAttributes('secondary_category_attribute', 'secondary_category_attribute_container');
		this.renderAttributes('store_category_attribute', 'store_category_attribute_container');
		this.renderAttributes('store_category_attribute2', 'store_category_attribute_container2');
		this.renderAttributes('product_image_attribute', 'product_image_attribute_span');
		this.renderAttributes('action_type_attribute', 'action_type_attribute_span');
		this.renderAttributes('attributes_for_variation_qty', 'select_attributes_for_variation_qty_span');
		this.renderAttributes('variation_price_from', 'use_variation_price_from_span');

		this.renderAttributes('best_b_attribute', 'best_b_attribute_td');
		this.renderAttributes('best_acc_attribute', 'best_acc_attribute_td');
		this.renderAttributes('best_reg_attribute', 'best_reg_attribute_td');

                this.renderAttributes('product_details_isbn_ca', 'product_details_isbn_ca_td');
                this.renderAttributes('product_details_epid_ca', 'product_details_epid_ca_td');
                this.renderAttributes('product_details_upc_ca', 'product_details_upc_ca_td');
                this.renderAttributes('product_details_ean_ca', 'product_details_ean_ca_td');

		for (var i = 0; i <= 30; i++) {
			try {
				this.renderAttributes('attribute_custom_attribute_select_' + i, 'attribute_container_' + i, 0, '', i, '');
			} catch (e) {
				//console.log(i);
				break;
			}
		}
	},

	// Get Price and Description Templates ===========
	getTemplatesListsForSet: function (setId) {

		var url = templatesListsUrl + 'setid/' + setId;
		new Ajax.Request(url, {
			method: 'post',
			onSuccess: function (transport, json) {
				var data = transport.responseText.evalJSON(true);
/*
console.log(
'title', AttributesActions.templateData['template_title'],
'modify', AttributesActions.templateData['modify_template']
);
*/
				$('td_select_template_description').innerHTML = data['description'];
				$('templates_price_list').innerHTML = data['price'];

                $('modify_template').value = AttributesActions.templateData['modify_template'];
                $('template_title').value = AttributesActions.templateData['template_title'];

				if (AttributesActions.templateData['modify_template'] == '0') {
					if (AttributesActions.templateData['template_title'] != "") {

						if (AttributesActions.templateData['template_title'] != 0 && AttributesActions.templateData['modify_template'] == '0') {

							$('modify_template_a').style.display = "";
							var trArray = $$('.description_tab_tr');
							trArray.each(function (tr) {
								tr.style.display = 'none';
							});

						}

					}
				}

                $('template_price_title').value = AttributesActions.templateData['template_price_title'];
                $('modify_pricetemplate').value = AttributesActions.templateData['modify_pricetemplate'];

				if (AttributesActions.templateData['modify_pricetemplate'] == '0') {
					if (AttributesActions.templateData['template_price_title'] != "") {

						if (AttributesActions.templateData['template_price_title'] != 0 && AttributesActions.templateData['modify_pricetemplate'] == '0') {

							$('modify_pricetemplate_a').style.display = "";
							var trArray = $$('.price_tab_tr');
							trArray.each(function (tr) {
								tr.style.display = 'none';
							});

						}

					}
				}
				//if (AttributesActions.templateData['templateId'] != "") DescriptionActions.changeModifyPriceTemplateMode(1);

			}
		});

	},
	//Checked attributes during Edit the Template
	checkSpecificAttributesSelect: function (specificKey) {

		$('attribute_custom_attribute_select_' + specificKey).value = this.specifics[specificKey]['content'];

	},
	//Checked attributes during Edit the Template
	checkShippingAttributesSelect: function (shippingName, isAddit) {
        isAddit = isAddit || false;

		for (var i = 0; i <= 200; i++) {
			if (this.shipping[i]['shipping_value'] == shippingName) {
                var key, value;
                //console.log(shippingName, isAddit);
				try {
                    key = isAddit ? 'shippingadditcost_select_' : 'shippingcost_select_';
                    value = isAddit ? this.shipping[i]['cost_additional_items'] : this.shipping[i]['cost_value'];
					$(key + shippingName).value = value;
				} catch (e) {}
				try {
                    key = isAddit ? 'ishipaddit_select_' : 'iship_select_';
                    value = isAddit ? this.shipping[i]['cost_additional_items'] : this.shipping[i]['cost_value'];
                    //console.log(key, value);
					$(key + shippingName).value = value;
				} catch (e) {}
				break;
			}

		}

	},

	//Checked attributes during Edit the Template
	checkAttributesSelect: function (name, aValue) {
		if (this.templateData[name] != "") {
			$(name).value = this.templateData[name];
		}

		try {
			if (aValue != 0) $(name).value = aValue;
		} catch (e) {}

	},

	prepareAttributes: function(setid)
	{
		var url = getAttributesBySetUrlNew + 'setid/' + setid;
		new Ajax.Request(url, {
			method: 'post',
			asynchronous : false,
			onSuccess: function (transport)
			{
				var data = transport.responseText.evalJSON(true);
				//console.log(data);
				var cachedOptions = '';
				data.each(function(v)
				{
					//var v = data[i];
					cachedOptions += '<option value="' + v.code + '">' + v.label + '</option>\n';
				});
				//console.log(cachedOptions);
				// NB! static property
				AttributesActions.attrData = cachedOptions;
			}
		});
	},

	renderAttributes: function (name, insertTo, aValue, width, specificKey, shippingField)
	{
		var style = width ? ' style="width: ' + width + 'px;"' : '';
		var txt = '<select name="' + name + '" id="' + name + '"' + style + '>\n';

		if (name == 'secondary_category_attribute') {
			txt += '<option value=""></option>\n';
		}

        if (shippingField && shippingField[1]) {
            txt += '<option value=""></option>\n';
        }

		txt += AttributesActions.attrData;
		txt += '</select>';

		$(insertTo).innerHTML = txt;

		var needChecks = [
			'use_reserve_from',
			'use_price_from',
			'use_now_from',
			'select_attributes_for_qty',
			'main_category_attribute',
			'secondary_category_attribute',
			'store_category_attribute',
			'store_category_attribute2',
			'product_image_attribute',
			'action_type_attribute',
			'variation_price_from',
			'attributes_for_variation_qty',
                        'variation_images_attribute',
			'weight_attribute_lbs',
			'weight_attribute_oz',
			'package_size_attribute',
			'dimentions_attribute_width',
			'dimentions_attribute_height',
			'dimentions_attribute_depth',
			'handling_feed_attribute',
			'attributes_for_variation_qty',
			'item_condition_attr',
			'best_b_attribute',
			'best_acc_attribute',
			'best_reg_attribute',
                        

            'product_details_isbn_ca',
            'product_details_epid_ca',
            'product_details_upc_ca',
            'product_details_ean_ca'
		];

		if (needChecks.indexOf(name) != -1) {
			this.checkAttributesSelect(name, aValue);
		}

		try {
			if (shippingField != '') {
				this.checkShippingAttributesSelect(shippingField[0], shippingField[1]);
			}
		} catch (e) {}

		try {
			if (specificKey >= 0) {
				this.checkSpecificAttributesSelect(specificKey);
			}
		} catch (e) {}
	},

	changeAttrbibutesTypeForSpecific: function (id) {

		var selectedValue = $('select_view_for_specific_' + id).value;

		switch (selectedValue) {
		case '0':
			$('attributeselect_' + id).style.display = "none";
			$('attributetext_' + id).style.display = "none";
			$('attribute_container_' + id).style.display = "none";
			$('attribute_string_' + id).style.display = "none";
			break;
		case '1':
			$('attributeselect_' + id).style.display = "";
			$('attributetext_' + id).style.display = "none";
			$('attribute_container_' + id).style.display = "none";
			$('attribute_string_' + id).style.display = "";
			break;
		case '2':
			$('attributeselect_' + id).style.display = "none";
			$('attributetext_' + id).style.display = "none";
			$('attribute_container_' + id).style.display = "";
			$('attribute_string_' + id).style.display = "";
			break;
		case '3':
			$('attributeselect_' + id).style.display = "none";
			$('attributetext_' + id).style.display = "";
			$('attribute_container_' + id).style.display = "none";
			$('attribute_string_' + id).style.display = "";
			break;
		default:
			break;

		}

	},
	//Change type for Listing Type (Selected or Attribute)
	listingTypeChange: function () {

		if ($('listingType').value == '0') {
			$('action_type').style.display = '';
			$('action_type_attribute_span').style.display = 'none';
			$('action_type').addClassName('required-entry');
		} else {
			$('action_type').style.display = 'none';
			$('action_type_attribute_span').style.display = '';
			$('action_type').removeClassName('required-entry');
		}
	},
        getAttributesForConfigurable: function () {

		var setid = $('attribute_set').value;

		var url = getAttributesForConfigurableUrl + 'setid/' + setid;
                var urlForSelect =  getAttributesForConfigurableAsSelect + 'setid/' + setid
		new Ajax.Request(url, {
			method: 'post',
			onSuccess: function (transport) {

				if (transport.responseText != "end") {


					$('multivariation_error').style.display = "none";
					$('attributes_checkboxes').innerHTML = transport.responseText;
					$('use_multivariation_tr').style.display = "";

					MultivariableActions.multivariationModeChange();

				} else {

					//                        $('multivariation_error').style.display="";
					//                        $('use_multivariation_tr').style.display="none";
					//                        $('variation_price_tr').style.display = 'none';
					//                        $('variation_sku_tr').style.display = 'none';
					//                        $('variation_qty_tr').style.display = 'none';
					//                        $('variation_qty_tr_1').style.display = 'none';
					//                        $('variation_qty_tr_2').style.display = 'none';
					//                        $('custom_variation_price_attribute_tr').style.display = 'none';
					//                        $('use_configurable_tr').style.display = 'none';
				}

			}
		});

                new Ajax.Request(urlForSelect, {
                    method: 'post',
                    onSuccess: function (transport) {
                        if (transport.responseText != "end") {
                            $('variation_attributes_images_select').innerHTML = transport.responseText;
                            $('variation_images_tr').style.display = "";

                            if (this.templateData["variation_images_attribute"] != "") {
                                    $("variation_images_attribute").value = this.templateData["variation_images_attribute"];
                            }
                        }
                    }
                });
	},

	changeCalculationWeghtMode: function () {

		var setid = $('attribute_set').value;

		if ($('calculation_weght_mode').value == '1') {
			$('calculating_shipping_tr1_0').style.display = 'none';
			$('calculating_shipping_tr1_1').style.display = '';
		} else {
			this.renderAttributes('weight_attribute_lbs', 'weight_attribute_lbs_span', 0, '80');
			this.renderAttributes('weight_attribute_oz', 'weight_attribute_oz_span', 0, '80');
			$('calculating_shipping_tr1_0').style.display = '';
			$('calculating_shipping_tr1_1').style.display = 'none';
		}
	},

	changeCalculationPackageSizeMode: function () {

		var setid = $('attribute_set').value;

		if ($('package_size_mode').value == '1') {
			$('calculating_shipping_tr2_0').style.display = 'none';
			$('calculating_shipping_tr2_1').style.display = '';
		} else {
			this.renderAttributes('package_size_attribute', 'package_size_attribute_td');
			$('calculating_shipping_tr2_0').style.display = '';
			$('calculating_shipping_tr2_1').style.display = 'none';
		}

	}, changeCalculationDimentionsMode: function () {

		var setid = $('attribute_set').value;

		if ($('dimentions_mode').value == '1') {
			$('calculating_shipping_tr3_0').style.display = 'none';
			$('calculating_shipping_tr3_1').style.display = '';
		} else {
			this.renderAttributes('dimentions_attribute_width', 'dimentions_attribute_width_span', 0, '80');
			this.renderAttributes('dimentions_attribute_height', 'dimentions_attribute_height_span', 0, '80');
			this.renderAttributes('dimentions_attribute_depth', 'dimentions_attribute_depth_span', 0, '80');
			$('calculating_shipping_tr3_0').style.display = '';
			$('calculating_shipping_tr3_1').style.display = 'none';
		}

	}, changeCalculationHandlingFeeMode: function () {

		var setid = $('attribute_set').value;

		if ($('handling_feed_mode').value == '1') {
			$('calculating_shipping_tr4_0').style.display = 'none';
			$('calculating_shipping_tr4_1').style.display = '';
		} else {
			this.renderAttributes('handling_feed_attribute', 'handling_feed_attribute_span');
			$('calculating_shipping_tr4_0').style.display = '';
			$('calculating_shipping_tr4_1').style.display = 'none';
		}

	}, changeCalculationIntHandlingFeeMode: function () {

		var setid = $('attribute_set').value;

		if ($('inthandling_feed_mode').value == '1') {
			$('calculating_shipping_tr5_0').style.display = 'none';
			$('calculating_shipping_tr5_1').style.display = '';
		} else {
			this.renderAttributes('inthandling_feed_attribute', 'international_handling_fee_span');
			$('calculating_shipping_tr5_0').style.display = '';
			$('calculating_shipping_tr5_1').style.display = 'none';
		}

    },
    accepOfferModeChange: function()
    {
        $('use_offer_tr4').style.display = "none";
        $('use_offer_tr3').style.display = "none";

    	if ($('accept_offer_mode').value == '1') {
    		$('use_offer_tr3').style.display = "";
    	} else if ($('accept_offer_mode').value == '2') {
    		$('use_offer_tr4').style.display = "";
        }
    },

    rejectOfferModeChange: function()
    {
        $('use_offer_tr7').style.display = "none";
        $('use_offer_tr6').style.display = "none";

    	if ($('reject_offer_mode').value == '1') {
    		$('use_offer_tr6').style.display = "";
    	} else if ($('reject_offer_mode').value == '2') {
    		$('use_offer_tr7').style.display = "";
    	}
    },

    bestOfferModeChange: function(){

    	if ($('use_best_offer').value == '0'){

    		$('use_offer_tr1').style.display = "none";
    		$('use_offer_tr2').style.display = "none";
    		$('use_offer_tr3').style.display = "none";
    		$('use_offer_tr4').style.display = "none";
    		$('use_offer_tr5').style.display = "none";
    		$('use_offer_tr6').style.display = "none";
    		$('use_offer_tr7').style.display = "none";

    	}else{

    		$('use_offer_tr1').style.display = "";
    		$('use_offer_tr2').style.display = "";
    		$('use_offer_tr5').style.display = "";
    		$('use_offer_tr6').style.display = "";
    		this.accepOfferModeChange();
    	}

    },
    secondChanceModeChange: function(){

    	if ($('use_second_chance').value == '0'){

    		$('use_secondchance_tr1').style.display = "none";
    		$('use_secondchance_tr2').style.display = "none";
    		$('use_secondchance_tr3').style.display = "none";

    	}else{

    		$('use_secondchance_tr1').style.display = "";
    		$('use_secondchance_tr2').style.display = "";
    		this.secondChancePriceModeChange();

    	}

    },
    secondChancePriceModeChange: function(){

    	if ($('second_chance_price').value == '2'){

    		$('use_secondchance_tr3').style.display = "";

    	}else{

    		$('use_secondchance_tr3').style.display = "none";

    	}

    }

};

function printItemsSpecifications(id)
{
  var o = $('attributeselect_' + id).options;
  var s = '';
  for (i = 1; i < o.length; i++) {
      s += o[i].value + '\n';
  }
  alert(s);
}

