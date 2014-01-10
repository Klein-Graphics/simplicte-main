<div class="modal hide" id="add_discount_modal">
    <div class="modal-header">
        <button class="close" data-dismiss="modal">&times;</button>
        <h3>Add Discount</h3>
    </div>
    <div class="modal-body">
        <form id="create_update_discount" class="form-horizontal" action="<?=sc_cp('Stock/update_discount')?>">
            <input type="hidden" id="id" name="id" />
            <div class="control-group">
                <label class="control-label" for="code">Discount Code</label>
                <div class="controls">
                    <input type="text" id="code" name="code"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="desc">Description</label>
                <div class="controls">
                    <input type="text" id="desc" name="desc"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="action">Type</label>
                <div class="controls">
                    <select id="action" name="action">
                        <option disabled="disabled" selected="selected" value="">Choose a discount type</option>
                        <option value="percentoff">Percentage off of total</option>
                        <option value="fixedoff">Fixed amount off of total</option>
                        <option value="itempercentoff">Percentage off of a specific item</option>
                        <option value="itemfixedoff">Fixed amount off of a specific item</option>
                        <option value="bxgx">Buy X get Y</option>
                    </select>
                </div>
            </div>
            <div class="control-group option-input">
                <label class="control-label" for="item_name">Item</label>
                <div class="controls">
                    <input type="text" id="item_name" name="item_name" placeholder="Start typing item name"/><input type="hidden" id="item" name="item"/>
                </div>
            </div>
            <div class="control-group option-input">
                <label class="control-label" for="value">Discount</label>
                <div class="controls">
                    <div class="input-prepend"><span class="add-on">%</span><input type="text" class="span2" id="value" name="value"/></div>          
                </div>
            </div>
            <div class="control-group option-input">
                <label class="control-label" for="bamount">X Amount</label>
                <div class="controls">
                    <input type="text" id="bamount" name="bamount" />
                </div>
            </div>
            <div class="control-group option-input">
                <label class="control-label" for="gamount">Y Amount</label>
                <div class="controls">
                    <input type="text" id="gamount" name="gamount"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="expires">Expiry date</label>
                <div class="controls">
                    <input type="text" id="expires" name="expires" placeholder="mm/dd/yyyy hh:mm"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Modifiers</label>
                <div class="controls">
                    <input type="checkbox" id="free_shipping" name="modifiers[free_shipping]" value="1" /><label class="inline" for="free_shipping">Free Shipping</label><br>
                    <input type="checkbox" id="item_percent_all" name="modifiers[item_percent_all]" value="1" /><label class="inline" for="item_percent_all">Item-level percent discounts apply to any quantity</label>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <div class="button-message" id="add_discount_message"></div>
        <a href="#" class="btn" data-dismiss="modal">Close</a>
        <a href="#" class="btn btn-primary" id="save_discount">Save</a>  
    </div>
</div>

<table class="table table-bordered table-striped" id="discounts_table">
    <thead>
        <tr><td colspan="999">
            <a class="btn" data-toggle="modal" href="#add_discount_modal"><i class="icon-plus-sign"></i>Add Discount</a>
        </td></tr>
        <tr><td>Code</td><td>Store Description</td><td>What it does</td><td>Expires</td><td></td></tr>
    </thead>
    <tbody>
<?php foreach ($discounts as $d) : ?>
        <tr class="item-<?=$d->id?>">
            <td><?=$d->code?></td>
            <td><?=$d->desc?></td>
            <td><?=$d->what_it_does?></td>
            <td><?=$d->readable_expire?></td>
            <td>
                <button class="btn btn-mini btn-primary edit-discount" value="<?=sc_cp('Stock/get_discount/'.$d->id)?>"><i class="icon-pencil"></i></button>
                <button class="btn btn-mini btn-danger delete-discount" value="<?=sc_cp('Stock/delete_discount/'.$d->id)?>"><i class="icon-remove"></i></button>                
            </td>
        </tr>
<?php endforeach ?>        
    </tbody>
</table>
<script type="text/javascript" src="<?=sc_asset('js','jquery.liveSearch.js')?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".option-input").hide();
        $("#add_discount_modal").on('hidden', function() {
            $('#create_update_discount input').val('');
            $('#action').val('').change();
            $('[type="checkbox"]').attr('checked',false);
            $('#add_discount_message').hide();            
        });
        $("#item_name").liveSearch({
            url:"<?=sc_ajax('live_search/items/name/')?>",
            onLoad:function() {
                $('.sc_live_search_result').click(function(e) {
                    e.preventDefault();
                    
                    $('#item').val($(this).data('id'));
                    $('#item_name').val($(this).html());
                    $('#jquery-live-search').slideUp();
                });
            }
        });
        
        $('#action').change(function() {
            
            $(".option-input").hide();
            
            switch ($(this).val()) {
                case "percentoff":
                    $("#value")
                        .closest('.control-group').show()
                        .find('.add-on').html('%');     
                break;
                
                case "fixedoff":
                    $("#value")
                        .closest('.control-group').show()
                        .find('.add-on').html('$');     
                break;

                case "itempercentoff":
                    $("#value")
                        .closest('.control-group').show()
                        .find('.add-on').html('%'); 
                    $("#item_name").closest('.control-group').show(); 
                break;
                
                case "itemfixedoff":   
                    $("#value")
                        .closest('.control-group').show()
                        .find('.add-on').html('$');     
                    $("#item_name").closest('.control-group').show();    
                break;

                case "bxgx":
                    $("#item_name").closest('.control-group').show();      
                    $("#bamount").closest('.control-group').show();        
                    $("#gamount").closest('.control-group').show();     
                break;
            }
        });
        
        $('#create_update_discount').submit(function(e) {
            e.preventDefault();
            
            $.post($(this).attr('action'), $(this).serialize(),function(result) {
                if (result.ACK==0) {
                    $('#add_discount_message').fadeOut(function(){
                        $(this).html(result.message).fadeIn();
                    });
                } else {
                    $('#add_discount_message').fadeOut(function(){
                        $(this).html('Discount added').fadeIn();
                    });
                    
                    setTimeout(function() {
                        $('#add_discount_modal').modal('hide');
                        
                        var new_class = $(result.new_row).filter('tr').attr('class');
                        var old_row = $('.'+new_class);                                                                        
                        
                        if (old_row.length) {
                            old_row.replaceWith(result.new_row);
                        } else 
                            $('#discounts_table tbody').append(result.new_row);
                         
                            
                        $('.'+new_class+' .delete-discount').click(function(e) {
                            e.preventDefault();
                            link = $(this);
                            
                            $.post($(this).val(),function(data) {
                                if (data.ACK == 1) {
                                    link.closest('tr').fadeOut()
                                }
                            },'JSON');
                        });
                        
                        $('.'+new_class+' .edit-discount').click(function(e) {
                            e.preventDefault();
            
                            $.post($(this).val(),function(item) {
                                for (var name in item) {
                                    $('#'+name).val(item[name]);                                                            
                                }
                                $('#action').change();
                                $('#add_discount_modal').modal('show');
                            },'JSON');                        
                        });
                    },1000);
                }
            },'json'); 
        });
        
        $('.delete-discount').click(function(e) {
            e.preventDefault();
            link = $(this);
            
            $.post($(this).val(),function(data) {
                if (data.ACK == 1) {
                    link.closest('tr').fadeOut();
                }
            },'JSON');
        });
        
        
        $('.edit-discount').click(function(e) {
            e.preventDefault();
            
            $.post($(this).val(),function(item) {
                //Modifiers
                if (item.modifiers) {                                        
                    //TODO make this dynamic                
                    if (item.modifiers.free_shipping) {
                        $('#free_shipping').attr('checked','checked');
                    }                    
                    
                    if (item.modifiers.item_percent_all) {
                        $('#item_percent_all').attr('checked','checked');
                    }
                    
                    delete item.modifiers;
                }
                
                for (var name in item) {
                    $('#'+name).val(item[name]);                                                            
                }                
                
                $('#action').change();
                $('#add_discount_modal').modal('show');
            },'JSON');                        
        });
        
        $('#save_discount').click(function(e) {
            e.preventDefault();
            
            $('#create_update_discount').submit();
        });
    });
</script>
