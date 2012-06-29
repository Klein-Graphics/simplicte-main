function reset_option_form() {
    $('#add_option').get(0).reset();
    $('#add_option input[type="hidden"]').val('');
    $('#add_option .flags tbody').empty(); 
    $('#add_option .fileinput-button span').html('Upload Image');
    $('#add_option .image-thumbnail').empty();      
    $('#add_option .bar').css('width',0);
    if ($('#add_option .inf-option-stock').is('.active')) {
        $('#add_option .inf-option-stock').click();
    }
    $('#options input[name="cat"]').data('changed',false);
};

function load_option(data) {
    var new_row = $(data.content);                                                                                                                                 
    
    if (!data.row || data.row-1 < 0 || !$('#added_options tbody').length) {              
        $('#added_options tbody').prepend(new_row);
    } else {
        $('#added_options tbody tr').eq(data.row-1).after(new_row);
    }                                
    
    $('#added_options [rel="tooltip"]').tooltip();
    
    //Reset the form
    reset_option_form();
                                
    if ($('.item-stock-selection .stock-option option[value="'+data.cat+'"]').length == 0) {                
        $('.item-stock-selection .stock-option').append('<option value="'+data.cat+'">'+data.cat+'</option>');
    }
    
    new_row.find('.delete-row').click(function() {
        var this_cat = $(this).closest('tr').find('input[name*="cat"]').val();
        $(this).closest('tr').remove();
        if ($('#added_options input[name*="cat"][value="'+this_cat+'"]').length == 0) {
            $('.item-stock-selection .stock-option option').remove('[value="'+this_cat+'"]');    
        }                    
        $('#added_options tbody tr').each(function() {
            $(this).find('[name*="optorder"]').val($(this).index('#added_options tbody tr'));
        });
    });
    
    new_row.find('.move-up').click(function() {
        var row = $(this).closest('tr');                    
        if (row.prev()) {
            row.insertBefore(row.prev());
            $('#added_options tbody tr').each(function() {
                $(this).find('[name*="optorder"]').val($(this).index('#added_options tbody tr'));
            });
        }
    });
    
    new_row.find('.move-down').click(function() {
        var row = $(this).closest('tr');
        if (row.next()) {
            row.insertAfter(row.next());
            $('#added_options tbody tr').each(function() {
                $(this).find('[name*="optorder"]').val($(this).index('#added_options tbody tr'));
            });
        }                    
    });
    
    new_row.find('.edit-option').click(function() {
        var raw_inputs = new_row.find('input').serializeArray();
        var inputs = {};
        var name;
        var value;
        for (var i=0; i<raw_inputs.length; i++) {                    
            name = raw_inputs[i]['name'].match(/options\[(\d*)\]\[(.*)\]/);
            if (! inputs['row_id']) {
                inputs['row_id'] = name[1];
            }
            
            name = name[2];
            inputs[name] = raw_inputs[i]['value'];
        }
        
        if (inputs.weight) {
            if (inputs.weight < 1) {
                $('#add_option .show_weight').val(inputs.weight*16);
                $('#add_option .weight-selection .btn-group .btn')
                    .removeClass('active')
                    .filter('[value="16"]').addClass('active');
            } else {
                $('#add_option .show_weight').val(inputs.weight);
                $('#add_option .weight-selection .btn-group .btn')
                    .removeClass('active')
                    .filter('[value="1"]').addClass('active');
            }
        }
        
        $('#add_option [name="weight"]').val(inputs.weight);                
        
        $('#add_option .image-thumbnail').empty().append(new_row.find('img'));
        
        if (inputs.stock) {
            if (inputs.stock == 'inf') {                
                $('.inf-option-stock').click().addClass('active');
                $('.inf-option-stock').prev()
                        .addClass('disabled')
                        .css('color','white');
            } else {
                $('.inf-option-stock').removeClass('active');
                $('.inf-option-stock').prev()
                    .removeClass('disabled')
                    .css('color','');
            }                    
        }
        
        if (inputs.flags) {                    
            var flags = inputs.flags.split(',');
            
            for (i=0;i<flags.length;i++) {
                flags[i] = flags[i].split(':');                                                
                $('#add_option .flags tbody').append('<tr>'+
                        '<td><input type="text" name="flags['+i+'][flag]" value="'+flags[i].shift()+'"/></td>'+
                        '<td><input type="text" name="flags['+i+'][args]" value="'+flags[i].join(':')+'"/></td>'+
                        '<td><i class="icon-remove delete-row"></i></td>'+
                    '</tr>'
                   );
            }       
        } 
        
        delete inputs.flags;
        delete inputs.weight; 
        
        for (name in inputs) {
            $('#add_option input[name="'+name+'"]').val(inputs[name]); 
        }                                                            
        
        var this_cat = $(this).closest('tr').find('input[name*="cat"]').val();
        $(this).closest('tr').remove();
        if ($('#added_options input[name*="cat"][value="'+this_cat+'"]').length == 0) {
            $('.item-stock-selection .stock-option option').remove('[value="'+this_cat+'"]');    
        }        
    });
}

function register_item_handlers(scope) {
    scope = (scope) ? $(scope) : $('#items_table');    
    
    scope.find('.delete-item').click(function() {        
        var row = $(this).closest('tr');
        var location = $(this).val();
        $('#confirm_delete_modal').modal('show');
        $('#confirm_delete_modal .btn-danger').click(function(e) {
            var btn = $(this).addClass('disabled').html('Removing...');
            $.get(location,function() {
                btn.removeClass('disabled').html('I\'m Sure! Delete it.');
                $('#confirm_delete_modal').modal('hide');
                row.fadeOut(function(){$(this).remove()});
            });
        });
    });
    
    scope.find('.edit-item').click(function() {
        var location = $(this).val();
        $('#add_items_modal').modal('show').find('.modal-header h3').html('Loading...');
        
        $.get(location,function(data) {
            $('#add_items_modal .modal-header h3').html('Edit Item');            
            for(var i=0;i<data.options.length;i++) {
                load_option(data.options[i]);
            } 
            
            if (data.item.flags) {                    
                var flags = inputs.flags.split(',');
                
                for (i=0;i<flags.length;i++) {
                    flags[i] = flags[i].split(':');                                                
                    $('#item .flags tbody').append('<tr>'+
                            '<td><input type="text" name="flags['+i+'][flag]" value="'+flags[i].shift()+'"/></td>'+
                            '<td><input type="text" name="flags['+i+'][args]" value="'+flags[i].join(':')+'"/></td>'+
                            '<td><i class="icon-remove delete-row"></i></td>'+
                        '</tr>'
                       );
                }       
            }
            
            delete data.item.flags;
            
            for (var name in data.item) {
                $('#item input[name="'+name+'"]').val(data.item[name]);
            }
            
            $('#item textarea').val(data.item.description);
            
            $('#item .image-thumbnail').html(data.item_thumb);
            
            if (data.item.weight < 1) {
                $('#item .show_weight').val(data.item.weight*16);
                $('#item .weight-selection .btn[value="16"]').addClass('active');
            } else {
                $('#item .show_weight').val(data.item.weight);
                $('#item .weight-selection .btn[value="1"]').addClass('active');
            }
            
            if (data.item.stock == 'inf') {
                $('#item .stock-type option[value="inf"]').attr('selected','selected');
            } else if (isNaN(data.item.stock) == false) {
                $('#item .stock-type option[value=""]').attr('selected','selected');
                $('.item-stock-selection .display_stock').val(data.item.stock);
            } else {
                $('#item .stock-type option[value="opt"]').attr('selected','selected');
                var dep_option = data.item.stock.split(':')
                dep_option = dep_option[1];
                $('#item .stock-option option[value="'+dep_option+'"]').attr('selected','selected');
            }
            
            $('.item-stock-selection .stock-type').change();
            
        },'json');                
        
    });
}

$(document).ready(function(){     
    $('#add_items_modal').modal({
        show: false    
    }).on('hidden',function(){
        $('#add_items_modal form').each(function() {
            $(this).get(0).reset();
        }); 
        $('#item input[type="hidden"]').val('');
        $('#item .flags tbody').empty(); 
        $('#item .fileinput-button span').html('Upload Image');
        $('#item .image-thumbnail').empty(); 
        $('#item .stock-type option').removeAttr('selected');
        $('#item .stock-type option').eq(0).attr('selected','selected').change();
        $('#item .btn-grp .btn.active').removeClass('active');
        $('#item .bar').css('width',0);
        $('#added_options tbody').empty();
        $(this).find('#save_item').html('Save').removeClass('disabled');
       
        
        reset_option_form();
    });   
    
    register_item_handlers();
    
    $('a[href="#add_items_modal"]').click(function() {
        $('#add_items_modal .modal-header h3').html('Add Item');
    });  
    
    $('.item-stock-selection .stock-option').hide();
    
    $('.item-stock-selection .stock-type').change(function(){   
        $('.item-stock-selection .stock-option, .item-stock-selection .display_stock').hide();
        
        switch ($(this).val()) {
            case '':
                $('#item input[name="stock"]').val($('.item-stock-selection .display_stock').show().val());
                break;
                
            case 'inf':
                $('#item input[name="stock"]').val('inf');                
                break;
            
            case 'opt':
                $('#item input[name="stock"]').val('opt:'+$('.item-stock-selection .stock-option').show().val());
                break;                                          
        } 
    }); 
    
    $('.item-stock-selection .display_stock').keyup(function() {
        $('#item input[name="stock"]').val($('.item-stock-selection .display_stock').val());
    });   
    
    $('.item-stock-selection .stock-option').change(function() {
        $('#item input[name="stock"]').val('opt:'+$('.item-stock-selection .stock-option').show().val());    
    });
    
    $('.inf-option-stock').toggle(function(e) {
        $(this).addClass('active');
        $(this).prev()
            .addClass('disabled')
            .css('color','white')
            .data('prev-value',$(this).prev().val())
            .val('inf');
    },function (e) {
        $(this).removeClass('active');
        $(this).prev()
            .removeClass('disabled')
            .css('color','')
            .val($(this).prev().data('prev-value'));    
    });
    
    $('#add_option input[name="stock"]').focus(function(){
        if ($(this).is('.disabled')) {
            $(this).blur();
        }
    });    
    
    $('.weight-selection').each(function() {
        var ws = $(this);
        ws.find('.btn')
            .click(function(e) {
                e.preventDefault();                                
                ws.find('.btn').first().tooltip('hide');
                weight = ws.find('.show_weight').val()/$(this).val();
                ws.find('[name="weight"]').val(weight);
            })
            .first().tooltip({
                placement: 'top',
                trigger: 'manual',
                title: 'Select a weight unit'
            });
        
        ws.find('.show_weight').change(function(){
            if (!ws.find('[name="weight"]').val()) {
                ws.find('.btn').first().tooltip('show');
            }
        });
          
    });   
    
    $('.flags').each(function() {
        var fl = $(this);
        fl.find('button').click(function(e) {
            e.preventDefault();
            var flag_id = fl.find('tbody > tr').length;
            fl.find('tbody')
                .append('<tr>'+
                            '<td><input type="text" name="flags['+flag_id+'][flag]" /></td>'+
                            '<td><input type="text" name="flags['+flag_id+'][args]" /></td>'+
                            '<td><i class="icon-remove delete-row"></i></td>'+
                        '</tr>'
                       );
            $('.delete-row').click(function() {
                $(this).closest('tr').remove();
            });
        });                
        
    });
    
    $('#options [name="name"]').keyup(function() {
        if (!$('#options [name="cat"]').data('changed')) {
            $('#options [name="cat"]').val($(this).val());            
        }
    });
    
    $('#options [name="cat"]').change(function() {
        $(this).data('changed',($(this).val() != false));        
    });
    
    $('.image-upload').each(function() {        
        var bar = $(this).find('.progress > .bar');
        var btn = $(this).find('.btn');
        var hidden = $(this).find('.file-location');
        var thumb = $(this).find('.image-thumbnail');
        $(this).find('.input-file').fileupload({
            dataType: 'json',
            maxChunkSize: 1000000,
            submit: function() {
                btn.addClass('disabled').find('span').html('Uploading...');
                bar.width('0%');
            },
            progress: function(e, data) {
                bar.width(parseInt(data.loaded / data.total * 100, 10)+'%');
            },
            done: function(e, data) {
                if (data.result[0].error) {
                    btn
                        .removeClass('disabled')
                        .addClass('btn-error')
                        .find('span').html('Upload Failed!');     
                } else {                
                    btn
                        .removeClass('disabled')
                        .find('span').html('Change Image');  
                        hidden.val(data.result[0].url);
                    thumb.html('<img src="'+data.result[0].thumbnail_url+'" alt="Thumbnail" />');
                }
            },
            fail: function() {
                btn
                    .removeClass('disabled')
                    .addClass('btn-error')
                    .find('span').html('Upload Failed!');  
            }
        });
    });
    
    $('#add_option').submit(function(e){
        e.preventDefault();
        if ($(this).find('input[name="name"]').val()) { //Add the line            
            $('#add_option_message').fadeOut();
            if ($(this).find('input[name="optorder"]').val()==="") {
                $(this).find('input[name="optorder"]').val($('#added_options tbody tr').length);   
            } 
            
            if ($(this).find('input[name="row_id"]').val()==="") {
                $(this).find('input[name="row_id"]').val($('#added_options tbody tr').length);   
            }                                                            
            
            $.post($(this).attr('action'),$(this).serialize(),function(data) {               
                load_option(data);
            },'json');
            
        } else { //Show the error
            $('#add_option_message').fadeOut(function(){
                $(this).html('Please specifiy an item name');
            }).fadeIn();
        }  
    });
    
    $('#save_item').not('.disabled').click(function(){
        var btn = $(this);
        if ($('#item input[name="name"]').val()) {            
            $(this).html('Saving...').addClass('disabled');
            
            var item_form = $('#item');
            var option_form = $('#added_options');
            
            var together = $('#item, #added_options');
            
            $.post(item_form.attr('action'),$(together).serialize(),function(data) {                
                var new_row = $(data);    
                var the_id;
                if (the_id = item_form.find('input[name="id"]').val()) {                          
                    register_item_handlers($('.item-'+the_id).closest('tr').html(new_row.html()));
                } else {
                    new_row.hide();
                    $('#items_table tbody').prepend(new_row);
                    new_row.fadeIn(); 
                    register_item_handlers(new_row);                                 
                }                                
                
                $('#add_items_modal').modal('hide');                           
                
            });
        } else {
            $('#add_item_message').fadeOut(function(){
                $(this).html('Please specify an item name');
            }).fadeIn();
        } 
        
        
    });        
    
    $('#search').keyup(function(){
        words = $(this)
            .val()
            .replace(/([\[\\\*\^\$\.\|\?\+\)])/g,'\\$1')
            .replace(/\s+/g,'|')
            .replace(/\|$/,'');

        regex = new RegExp('('+words+')','i');
        $('tbody > tr').filter(function(){
                        return regex.test($(this).data('search'));
                }).show();
        $('tbody > tr').filter(function(){
                        return !regex.test($(this).data('search'));
                }).hide();
    });
});
