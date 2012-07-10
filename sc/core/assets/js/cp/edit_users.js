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
