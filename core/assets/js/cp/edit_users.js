$('.modify-user').click(function(e) {
    e.preventDefault();
    if ($(this).hasClass('saving')) {
        return;
    }
    
    var row = $(this).closest('tr');
    var that = $(this);
    
    if ($(this).hasClass('save-user')) {
        $(this)
            .removeClass('save-user')
            .addClass('saving')
            .find('i')
                .removeClass('icon-ok')
                .addClass('icon-refresh-spin');                                             
        
        row.find('input[type="password"]').each(function(){
            if ($(this).val()) {
                $(this)
                    .data('orig',$(this).val())
                    .val($().crypt({method:'md5',source:$(this).val()})); 
            }
        });                                
        
        $.post($(this).attr('href'),row.find('input').serialize(),function(data) {
            var bad_input;
        
            row.find('input[type="password"]').each(function(){
                $(this).val($(this).data('orig'));                        
            });
            
            that
                .removeClass('saving')
                .find('i')
                    .removeClass('icon-refresh-spin');
            
            row.find('td').removeClass('error').find('input').popover({animation:false}).popover('hide');                
            if (data.ack == '1') {
                that.find('i').addClass('icon-edit');
                        
                row.find('td').not('.no-edit, .master-account, .password').find('input').replaceWith(function() {
                   return $(this).val(); 
                });
                
                row.find('.master-account input').replaceWith(function() {
                    return ($(this).attr('checked')) ? 'Yes' : 'No';
                });
                
                row.find('.password').html('********');
            } else {                
                that
                    .addClass('save-user')
                    .find('i')
                        .addClass('icon-ok');
                for (bad_input in data.bad_inputs) {
                    row.find('input[name="'+bad_input+'"]')
                        .popover({
                            content: data.bad_inputs[bad_input],
                            trigger: 'manual',
                            placement: (bad_input == 'confirm_password')?'bottom':'top',
                            animation: true                                                      
                        })
                        .popover('show')
                        .parent()
                            .addClass('error');
                }
                    
            }
        },'json');
    } else {
        $(this)
            .addClass('save-user')
            .attr('title','Save Changes')
            .find('i')
                .removeClass('icon-edit')
                .addClass('icon-ok');        
        
        row.find('td').not('.no-edit, .master-account, .password').html(function(index,oldhtml) {
            return '<input type="text" value="'+oldhtml+'" name="'+$(this).data('name')+'">';                              
        });
        
        row.find('.master-account').html(function(index,oldhtml) {
            return '<input type="checkbox" '+((oldhtml == "Yes")?'checked="checked" ':'')+'name="master">';
        });
        
        row.find('.password').html('<input type="password" name="passwordmd5" placeholder="Password"><br><input rel="tooltip" type="password" name="confirm_password" placeholder="Confirm">');
    }
        
});

$('.reset-password').click(function(e) {
    e.preventDefault();
    var link = $(this);
    $.get($(this).attr('href'),function(data) {        
        link
            .attr('title',data)
            .tooltip({                
                trigger: 'manual'
            })
            .tooltip('show');
        
        
        setTimeout(function(){link.tooltip('hide')},3000);
    },'html');   
});

$('.delete-user').click(function(e) {
    e.preventDefault();
    var row = $(this).closest('tr');
    var button = $(this);
    
    $.get($(this).attr('href'),function(data) {
        button
            .removeClass('icon-trash')
            .addClass('icon-refresh-spin');
        if (data == 'ok') {
            row.fadeOut();   
        } else {
            button
            .removeClass('icon-refresh-spin')
            .addClass('icon-trash');    
            alert(data);
        }
    },'html');
});

$('.cp-ajax-loader').hide();
$('#add_user_modal .message-area').hide();

$('#add_user_modal form').submit(function(e) {
    e.preventDefault();
    var form = $(this);
    
    $('.cp-ajax-loader').show();    
    $.post($(this).attr('action'),$(this).serialize(),function(data) {
        $('#add_user_modal .message-area').fadeOut();
        
        $('.cp-ajax-loader').hide();
        if (data == 'ok') {
            window.location.reload();
        } else {
            $('#add_user_modal .message-area').queue(function(next){$(this).html(data);next()}).fadeIn();
        }
    },'html');
});
