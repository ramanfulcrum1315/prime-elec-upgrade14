jQuery.noConflict();
jQuery(function() {
	jQuery('.salesperson-accordmore').live("click", function () {
		jQuery(this).next(".salesperson-accordmore-content").toggle("slow");
		jQuery(this).toggleClass("salesperson-accordmoreopen");
    });
});
