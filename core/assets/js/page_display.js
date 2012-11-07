page_display = new Object();

function bind_labels() {
    $('label.sc_label_left').unbind('click').click(function(){
        $(this).prev('input[type="text"]').select();
        $(this).prev('input[type="checkbox"], select').click();
    });
    $('label.sc_label_right').unbind('click').click(function(){
        $(this).next('input[type="text"]').select();
        $(this).next('input[type="checkbox"], select').click();        
    }); 
}

$(document).ready(function() {

    $('.ajax_loader').ajaxStart(function() {
        $(this).show();
        $('body').addClass('loading');
    }).ajaxStop(function() {
        $(this).hide();
        $('body').removeClass('loading');
        bind_labels();
    }).hide();    
    
    bind_labels();               

    for (driver in page_display) {
        if (typeof page_display[driver].bind == 'function') {
            page_display[driver].bind();
        }
    } 

    $('.sc_add_to_cart_form').submit(function(e){
        e.preventDefault();
        the_form = $(this);
        
        $.post($(this).attr('action'),$(this).serialize(),function(data){
            the_form.find('.sc_message_area').html(data).show().delay(1500).fadeOut('slow');
            page_display.cart.refresh();
        });
        
        page_display.checkout.hide();
    });       

    if (location.hash=='#checkout') {
        page_display.checkout.show();
    }

});
