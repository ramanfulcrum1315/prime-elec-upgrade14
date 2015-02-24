//TODO: move this function out
function toggleButton(button, condition)
{
	var bt = $(button);

		if (condition) {
			bt.disabled = '';
			bt.removeClassName('disabled');
		} else {
			bt.disabled = 'disabled';
			bt.addClassName('disabled');
		}
}

DescriptionActions = Class.create();
DescriptionActions.prototype = {
    initialize: function(templateData){

        this.templateData = templateData;
      
    },

    updatePreviewButton : function()
    {
        // for listing templates only
        var enable = false;
        if ($('modify_template').value == 1 && $('mode').value == 2) {
            enable = true;
        }

        // toggle "Use eBay Layout Template"
        if ($('modify_template').value == 1) {
            $('custom_description_tr_4', 'layout_block_1', 'layout_block_2').invoke(2 != $('mode').value ? 'show' : 'hide');
        } else {
             $('custom_description_tr_4', 'layout_block_1', 'layout_block_2').invoke('hide');
        }

        $$('.bt_preview').each(function(item)
        {
            toggleButton(item, enable);
        });
    },

    // change Modify Template Mode
    changeModifyTemplateMode : function(editFlag){

        var trArray = $$('.description_tab_tr');

        var currentMode = $('modify_template').value;

        if (editFlag == '1')
            currentMode = '0';
        if (editFlag == '0')
            currentMode = '1';

        if (currentMode == '0'){

           $('modify_template').value = '1';

            trArray.each(function(tr) {
              tr.style.display = '';
            });
            $('modify_template_a').innerHTML = "Cancel";

            this.productSubTitleMode();
            this.descriptionModeChange();
            this.productTitleMode();
            this.setLayoutMode();
            if ($('template_title').value != "" && $('template_title').value != '0')
                change_description();

       }else{
           
            $('modify_template').value = '0';

            trArray.each(function(tr) {
              tr.style.display = 'none';
            });
            $('modify_template_a').innerHTML = "Modify Template";
            

           
       }

        this.updatePreviewButton();
    },
    changeModifyPriceTemplateMode : function(editFlag){

        var trArray = $$('.price_tab_tr');

        var currentMode = $('modify_pricetemplate').value;

        if (editFlag == '1')
            currentMode = '0';
        if (editFlag == '0')
            currentMode = '1';

        if (currentMode == '0'){

           $('modify_pricetemplate').value = '1';

            trArray.each(function(tr) {
              tr.style.display = '';
            });
            $('modify_pricetemplate_a').innerHTML = "Cancel";

            if ($('template_price_title').value != "" && $('template_price_title').value != '0')
                change_price_template();

            startPriceModeChange();
            reservePriceModeChange();
            buynowPriceModeChange();

       }else{

            $('modify_pricetemplate').value = '0';

            trArray.each(function(tr) {
              tr.style.display = 'none';
            });
            $('modify_pricetemplate_a').innerHTML = "Modify Template";

       }

    },
    productSubTitleMode: function(){

       if ($('product_subtitle_mode').value == '1')
            $('custom_subtitle_tr').style.display = '';
       else
            $('custom_subtitle_tr').style.display = 'none';

    },
    productTitleMode: function(){

        if ($('product_name_mode').value == '1'){
            $('custom_title_tr').style.display = '';
        }else{
            $('custom_title_tr').style.display = 'none';
        }

    },
    descriptionModeChange: function()
    {
        var action = 2 == $('mode').value ? 'show' : 'hide';
        $('custom_description_tr', 'custom_description_tr_2', 'custom_description_tr_3', 'custom_description_tr_4_1').invoke(action);
        // toggle "Use eBay Layout Template"
        $('custom_description_tr_4') && $('custom_description_tr_4', 'layout_block_1', 'layout_block_2').invoke(2 != $('mode').value ? 'show' : 'hide');

        $$('.bt_preview').each(function(item)
        {
            toggleButton(item, action == 'show');
        });
    },

        preview : function()
        {
            tinyMCE.triggerSave();

            if ($('template_title'))
                $('preview_template').value = $('template_title').value;

            if ($('modify_template'))
                $('preview_mode').value = $('modify_template');

            $('preview_content').value  = $('content_text').value;
            previewForm.submit();
        },

    setLayoutMode: function(){

        if ($('use_layout').value == '1'){
            change_marketplace_description();
            $("layout_block_1").style.display = '';
            $("layout_block_2").style.display = '';
        }else{
            $("layout_block_1").style.display = 'none';
            $("layout_block_2").style.display = 'none';
        }

    },
    galleryPictureChange: function(){

        if ($('product_picture').value == '2')
            $('product_image_attribute_tr').style.display = '';
        else
            $('product_image_attribute_tr').style.display = 'none';

    }
 
};

