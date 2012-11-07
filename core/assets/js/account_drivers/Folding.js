page_display.account = {

    close: function() {        
        $('#sc_account_action').slideUp();    
    },
    
    load: function(link_url,data){
        $('#sc_account_action').slideUp(function() {
            $('#sc_account_action').load(link_url,data,function(){                 
                $('#sc_account_action').slideDown();     
                
                console.log(
                $('.sc-message-return-link').click(function(e) {
                    e.preventDefault();
                
                    page_display.account.load(sc_location('ajax/login'));
                }));                    
                
                $("form.sc_account_form").submit(function(e) {
                    e.preventDefault();                    
                    
                    var previous_password = [];
                    
                    $(this).find('input[type="password"]').val(function(i,v) { 
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
                                page_display.account.refresh();
                                page_display.account.close();
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
                                         
                    $(this).find('input[type=password]').val(function(i,v) { 
                        return previous_password[i];                
                    });
                });            
                
                $('#sc_account_action .sc_close_link').click(function(e){ 
                    e.preventDefault();
                    page_display.account.close();
                });
            });
        });
    }, 
    
    refresh: function() {
        $("#sc_account_options").load(sc_location('ajax/account_options'),function(){
            page_display.account.bind();
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
      
      $('#sc_account_action').hide();
    
    }
};
