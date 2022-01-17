jQuery(document).ready(function ($) {
	
	var default_selected = $('#discount_type').val();
	if(default_selected != 'percent') {
		$('li.auto-coupon_options.auto-coupon_tab.coupon_price_range').hide();
	}
	
	$('#discount_type').change(function () {
		var selected = $(this).val();
		if(selected == 'percent') {
			$('li.auto-coupon_options.auto-coupon_tab.coupon_price_range').show();
		} else {
			$('li.auto-coupon_options.auto-coupon_tab.coupon_price_range').hide();
		}
	});

	jQuery( '#maximum_discount' ).attr( 'disabled', true );
});