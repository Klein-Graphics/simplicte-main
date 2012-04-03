page_display.account = {

    close: function() {        
        $('#sc_account_action').slideUp();    
    },
    
    load: function(link_url,data){
        $('#sc_account_action').load(link_url,data,function(){                 
            $('#sc_account_action').slideDown();
            $("form.sc_account_form").submit(function(e) {
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
                
                $.post($(this).attr('action'),$(this).serialize(),function(data){
                    switch (data.do_this) {
                        case 'refresh':
                            location.reload();
                        break;
                        
                        case 'display_good':
                            $('.sc_display')
                                .removeClass('sc_bad')
                                .addClass('sc_good')
                                .html(data.message); 
                                setTimeout('page_display.account.close()',500);   
                        break;
                        
                        case 'display_error':
                            $('.sc_display')
                                .removeClass('sc_good')
                                .addClass('sc_bad')
                                .html(data.message);
                        break;                        
                        
                        case 'load':
                            page_display.account.load(data.location,data.data);
                        break;
                            
                    }
                },'json');
                                     
                $(this).children('[type=password]').val(function(i,v) { 
                    return previous_password[i];                
                });
            });            
            
            $('#sc_account_action .sc_close_link').click(function(e){ 
                e.preventDefault();
                page_display.account.close();
            });
        });
    }, 
    
    bind: function() {
      $(".sc_account_link").click(function(e) {
        e.preventDefault();
        
        link_url = $(this).attr('href');
        
        if ($('#sc_account_action').css('display') != 'none') {
            $("#sc_account_action").slideUp(function() {
                page_display.account.load(link_url);
            });
        } else {
            page_display.account.load(link_url);
        }
        
        
      });      
    
    }
}
