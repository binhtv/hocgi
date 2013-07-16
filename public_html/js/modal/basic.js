/*

 */

jQuery(function ($) {
	// Load dialog on page load
	//$('#basic-modal-content').modal();

	// Load dialog on click
	$('#popup_register').click(function (e) {
		$('#popup_formregister').modal();	
		$("#header .hidden").hide();	
		return false;
	});
	$('#_btnResult').click(function (e) {
		$('#popup_bieuquyet').modal();		
		return false;
	});
	
});
