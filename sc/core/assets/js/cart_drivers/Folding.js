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

    $('#sc_display_cart').load(sc_location('ajax/view_cart'),function(){
      $(this).slideDown(function() {
        callback()
      });
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
    
    $('#sc_display_cart').slideUp(function() {
        callback();
    });
  },
  
  clear: function(callback) {
    callback = callback || function(){};
    this.refresh(function(inner_callback) {
      $('#sc_display_cart').load(sc_location('ajax/clear_cart'),function(){
        inner_callback();
      });
    },callback);
    page_display.checkout.hide();
  },
  
  refresh: function(middle_callback,end_callback) {
    middle_callback = middle_callback || function(finished){finished()};
    end_callback = end_callback || function(){};        
    
    if (this.displayed) {
      page_display.cart.hide(function() {
        middle_callback(function(){
          page_display.cart.show(function(){
            end_callback();
            $('.sc_cartinfo').load(sc_location('ajax/cart_info')); 
          })
       })
      });    
    } else {
        middle_callback(function() {
            end_callback();
            $('.sc_cartinfo').load(sc_location('ajax/cart_info'));
        });
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
    
    //load the cart info section
    $('.sc_cartinfo').load(sc_location('ajax/cart_info')); 
           
  }
}
