page_display.checkout = {
  show: function(callback) {
    callback = callback || function(){};
    
    page_display.cart.show();
    
    page_display.checkout.load(sc_location('ajax/checkout'));
  },    
  
  load: function(link_url,data) {
    $('#sc_checkout').load(link_url,data,function(r,ts,xhr){
        if (xhr.status == 204) {
            return false;
        } 
        
        $('.sc-message-return-link').click(function(e) {
            e.preventDefault();
        
            page_display.checkout.load(sc_location('ajax/checkout'));
        }); 
        
        checkout = $(this);
                        
        checkout.css('height','').slideDown();                      
                
        $("#sc_checkout form.sc_account_form").submit(function(e) {        
            e.preventDefault();
            var previous_password = [];
            
            $(this).children('[type=password]').val(function(i,v) { 
                if (!v) return '';
                previous_password[i] = v;                    
                return $().crypt({
                    method: 'md5',
                    source: v
                });
            });
            
            form = $(this);           
            
            $.post($(this).attr('action'),$(this).serialize(),function(data){
                switch (data.do_this) {
                    
                    case 'refresh':
                        page_display.account.refresh();
                        page_display.checkout.load(sc_location('ajax/checkout/verify_cart')); 
                    break;                  
                    
                    case 'display_good':
                        form.siblings('.sc_display')
                            .removeClass('sc_bad')
                            .addClass('sc_good')
                            .html(data.message); 
                        page_display.account.refresh();
                        page_display.checkout.load(sc_location('ajax/checkout/verify_cart'));  
                    break;
                    
                    case 'display_error':
                        form.siblings('.sc_display')
                            .removeClass('sc_good')
                            .addClass('sc_bad')
                            .html(data.message);
                        checkout.css('height','').animate({
                            height: checkout.prop('scrollHeight')
                        });
                    break;               
                    
                    case 'load':
                        page_display.checkout.load(data.location,data.data);
                    break;
                        
                }
            },'json');
                                 
            $(this).children('[type=password]').val(function(i,v) { 
                return previous_password[i];                
            });
        });
        
        $('#sc_checkout .sc_close_link').click(function(e){ 
            e.preventDefault();
            page_display.checkout.hide();
        });    
    });
  },
  
  hide: function(callback) {
    callback = callback || function(){};
    
    $.get(sc_location('ajax/checkout/cancel/'), function() {    
        $('#sc_checkout').slideUp(callback());
    });
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
