<div class="modal hide" id="add_discount_modal">
    <div class="modal-header">
        <button class="close" data-dismiss="modal">&times;</button>
        <h3>Add Discount</h3>
    </div>
    <div class="modal-body">
        <form id="create_update_discount" class="form-horizontal" action="<?=sc_cp('Stock/update_discount')?>">
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
                <label class="control-label" for="discount_item">Item</label>
                <div class="controls">
                    <input type="text" id="discount_item" name="discount_item" placeholder="Start typing item name"/><input type="hidden" id="discount_item_num"/>
                </div>
            </div>
            <div class="control-group option-input">
                <label class="control-label" for="discount_value">Discount</label>
                <div class="controls">
                    <div class="input-prepend"><span class="add-on">%</span><input type="text" class="span2" id="discount_value" name="discount_value"/></div>          
                </div>
            </div>
            <div class="control-group option-input">
                <label class="control-label" for="buy_amount">X Amount</label>
                <div class="controls">
                    <input type="text" id="buy_amount" name="buy_amount" />
                </div>
            </div>
            <div class="control-group option-input">
                <label class="control-label" for="get_amount">Y Amount</label>
                <div class="controls">
                    <input type="text" id="get_amount" name="get_amount"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="expires">Expiry date</label>
                <div class="controls">
                    <input type="Text" id="expires" name="expires" placeholder="mm/dd/yyyy hh:mm"/>
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
                <button class="btn btn-mini btn-danger delete-discount" value="<?=sc_cp('Stock/delete_discount/'.$d->id)?>"><i class="icon-remove"></i></button>
                <button class="btn btn-mini btn-primary edit-discount" value="<?=sc_cp('Stock/edit_discount/'.$d->id)?>"><i class="icon-pencil"></i></button>
            </td>
        </tr>
<?php endforeach ?>        
    </tbody>
</table>
<script type="text/javascript" src="<?=sc_asset('js','jquery.liveSearch.js')?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".option-input").hide();
        $("#discount_item").liveSearch({url:"<?=sc_ajax('live_search/items/name/')?>"});
        $('#action').change(function() {
            
            $(".option-input").hide();
            
            switch ($(this).val()) {
                case "percentoff":
                    $("#discount_value")
                        .closest('.control-group').show()
                        .find('.add-on').html('%');     
                break;
                
                case "fixedoff":
                    $("#discount_value")
                        .closest('.control-group').show()
                        .find('.add-on').html('$');     
                break;

                case "itempercentoff":
                    $("#discount_value")
                        .closest('.control-group').show()
                        .find('.add-on').html('%'); 
                    $("#discount_item_num").closest('.control-group').show(); 
                break;
                
                case "itemfixedoff":   
                    $("#discount_value")
                        .closest('.control-group').show()
                        .find('.add-on').html('$');     
                    $("#discount_item_num").closest('.control-group').show();    
                break;

                case "bxgx":
                    $("#discount_item_num").closest('.control-group').show();      
                    $("#buy_amount").closest('.control-group').show();        
                    $("#get_amount").closest('.control-group').show();     
                break;
            }
        });
        
        $('#discount_item').liveSearch
        
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
                        $('#discounts_table tbody').append(result.new_row);
                    },1000);
                }
            },'json'); 
        });
        
        $('#save_discount').click(function(e) {
            e.preventDefault();
            
            $('#create_update_discount').submit();
        });
    });
</script>
