$(document).ready(function(){
	
	// Header Drupal Search Box
	$('#header [name=search_block_form]').val('Search this site...');
	$('#header input[name=op]').val('');
	$('#header [name=search_block_form]').focus(function () {
	$('#header [name=search_block_form]').val('');
	});
	
	// Show Stanford Search Box
	$("#javascript").show();
	
	// Header Stanford Search Box
	$('#header [name=q]').val('Search Stanford...');
	$('#header [name=q]').focus(function () {
	$('#header [name=q]').val('');
	});
	
	// Drawer Toggle Expand
	$("#menu_expand").click(function (){
		$("#nav_drawer").slideToggle("slow");
		$('#menu_expand').hide();
		$('#menu_hide').show();
	});
	
	// Drawer Toggle Hide
	$("#menu_hide").click(function () {
		$("#nav_drawer").slideToggle("slow");
		$('#menu_expand').show();
		$('#menu_hide').hide();
	});
	
});