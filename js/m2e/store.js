StoreActions = Class.create();
StoreActions.prototype = {
    initialize: function(templateData){

        this.templateData = templateData;
   
    },
    //Changes Store get From Type (Attributes or Api)
    storeGetFromTypeChange: function(categoryType){
    	
    	if (categoryType == null)
    		categoryType = "";
    	
    	if ($('storecategory_selected_type'+categoryType).value == '0'){
            
            $('tr_with_categories'+categoryType).style.display = "";
            $('tr_with_categories_attributes'+categoryType).style.display = "none";

        }else{

            $('tr_with_categories'+categoryType).style.display = "none";
            $('tr_with_categories_attributes'+categoryType).style.display = "";

        }

    	
    },
    fillStoreData: function(){

        $('storecategory_selected_type').value = this.templateData['storecategory_selected_type'];
        this.storeGetFromTypeChange();
        $('storecategory_selected_type2').value = this.templateData['storecategory_selected_type2'];
        this.storeGetFromTypeChange(2);

    }
};

