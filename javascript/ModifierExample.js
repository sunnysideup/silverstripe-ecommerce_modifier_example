(function($){
	$(document).ready(
		function() {
			ModifierExample.init();
		}
	);
})(jQuery);

var ModifierExample = {

	formSelector: "#ModifierExample_Form_ModifierExample",

	actionsSelector: ".Actions",

	loadingClass: "loading",

	init: function() {
		var options = {
			beforeSubmit: ModifierExample.showRequest,  // pre-submit callback
			success: ModifierExample.showResponse,  // post-submit callback
			dataType: "json"
		};
		jQuery(ModifierExample.formSelector).ajaxForm(options);
		jQuery(ModifierExample.formSelector + " " + ModifierExample.actionsSelector).hide();
		jQuery(ModifierExample.formSelector+ " input").change(
			function() {
				jQuery(ModifierExample.formSelector).submit();
			}
		);
	},

	// pre-submit callback
	showRequest: function (formData, jqForm, options) {
		jQuery(ModifierExample.formSelector).addClass(ModifierExample.loadingClass);
		return true;
	},

	// post-submit callback
	showResponse: function (responseText, statusText)  {
		jQuery(ModifierExample.formSelector).removeClass(ModifierExample.loadingClass);
		EcomCart.setChanges(responseText);
	}

}

