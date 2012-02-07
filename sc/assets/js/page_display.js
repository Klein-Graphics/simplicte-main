page_display = new Object();

$(document).ready(function() {

  $('.ajax_loader').ajaxStart(function() {
    $(this).show();
  }).ajaxStop(function() {
    $(this).hide();
  }).hide();

  //load the cart info section
  $('.sc_cartinfo').load(sc_location('ajax/cart_info'));      
  
  //bind the view/clear/checkout buttons
  page_display.cart.bind();
  page_display.checkout.bind();
  

});
