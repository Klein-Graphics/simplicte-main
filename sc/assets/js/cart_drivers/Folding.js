page_display.cart = {
  displayed: false,
  
  show: function(callback) {
    callback = callback || function(){};


    $('.sc_view_cart')
      .unbind('click')
      .click(function(e) {
        e.preventDefault();      
        page_display.cart.hide();
      });
      
    $('.sc_view_cart > img').attr('src',sc_button('close_cart'));   
    this.displayed = true;

    $('#display_cart').load(sc_location('ajax/view_cart'),function(){
      $(this).slideDown(callback());
    });
  },
  
  hide: function(callback) {
    callback = callback || function(){};
    
    $('.sc_view_cart')
      .unbind('click')
      .click(function(e) {
        e.preventDefault();      
        page_display.cart.show();
      });        
    
    this.displayed = false;
    
    $('.sc_view_cart > img').attr('src',sc_button('view_cart'));
    
    $('#display_cart').slideUp(callback());
  },
  
  clear: function(callback) {
    callback = callback || function(){};
    this.refresh(function(inner_callback) {
      $('#display_cart').load(sc_location('ajax/clear_cart'),function(){
        inner_callback();
      });
    },callback);
  },
  
  refresh: function(middle_callback,end_callback) {
    middle_callback = middle_callback || function(finished){finished()};
    end_callback = end_callback || function(){};
    
    if (this.displayed) {
      page_display.cart.hide(
        middle_callback(function(){
          page_display.cart.show(function(){
            end_callback();
          })
       })
      );    
    }
  },
  
  bind: function() {
    $('.sc_view_cart').click(function(e) {
       e.preventDefault();      
       page_display.cart.show();
    });
    
    $('.sc_clear_cart').click(function(e) {
      e.preventDefault();
      page_display.cart.clear();
    });
           
  }
}
