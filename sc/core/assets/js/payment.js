$(document).ready(function() {
    $('.sc_payment_gateway_select').change(function() {
        $(this).siblings('.sc_payment_gateway_area')
            .load(sc_location('ajax/checkout/load_payment'),{method: $(this).attr('value')});
    });
});
