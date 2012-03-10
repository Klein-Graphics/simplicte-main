page_display = new Object();

$(document).ready(function() {

  $('.ajax_loader').ajaxStart(function() {
    $(this).show();
  }).ajaxStop(function() {
    $(this).hide();
  }).hide();

  //load the cart info section
  $('.sc_cartinfo').load(sc_location('ajax/cart_info'));      

  for (driver in page_display) {
    if (typeof page_display[driver].bind == 'function') {
        page_display[driver].bind();
    }
  }  
  

});
