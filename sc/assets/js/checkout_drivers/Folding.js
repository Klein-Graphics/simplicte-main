page_display.checkout = {
  show: function(callback) {
    callback = callback || function(){};
    
    page_display.cart.show();
    
    $('#checkout').load(sc_location('ajax/checkout'),function(){
      $(this).slideDown(callback());
    });
  },
  
  hide: function(callback) {
    callback = callback || function(){};
    
    $('#checkout').slideUp(callback());
  },
  
  refresh: function() {
    this.checkout.hide(function(){this.checkout.show()});    
  },
  
  bind: function() {
    $('.sc_checkout').click(function(e){
      e.preventDefault();
      page_display.checkout.show();
    });
  }
}
