$(document).ready(function(){    
    $('a.btn, button.btn').click(function(e) {
        e.preventDefault();
        weight = $(this).val()*$('#show_weight').val();
        $('#weight').val(weight);
        
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
            
            $.post($(this).attr('action'),$(this).serialize(),function(data) {                
                if (!data.row || data.row-1 < 0 || !$('#added_options tbody').length) {
                    $('#added_options tbody').prepend(data.content);
                } else {
                    $('#added_options tbody').eq(data.row-1).after(data.content);
                }
            },'json');
            
        } else { //Show the error
            $('#add_option_message').fadeOut(function(){
                $(this).html('Please specifiy an item name');
            }).fadeIn();
        }  
    });
    
    $('#add_items_modal').modal();
    
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
