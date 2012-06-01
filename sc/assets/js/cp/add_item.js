$(document).ready(function(){    
    $('a.btn, button.btn').click(function(e) {
        e.preventDefault();
        weight = $(this).val()*$('#show_weight').val();
        $('#weight').val(weight);
        
    });
    
    $('#option_image_upload').fileupload({
        dataType: 'json',
        done: function(e,data) {
            console.log(data);
        }
    });
    
    $('#add_option').submit(function(e){
        e.preventDefault();
        if ($(this).find('input[name="option_name"]').val()) { //Add the line
            $('#add_option_message').fadeOut();    
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
