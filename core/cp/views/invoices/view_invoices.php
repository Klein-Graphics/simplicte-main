<?php $statuses = array('created','sent','viewed','settled','fulfilled','refunded'); ?>
<div class="modal hide" id="confirm_modal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h2>Are you sure?</h2>
    </div>
    <div class="modal-body">
        This will destroy this invoice and any information associated with it!
    </div>
    <div class="modal-footer">
        <?=ajax_loader()?>
        <a href="#" class="btn btn-danger confirm-delete">Delete it</a>
        <a href="#" class="btn" data-dismiss="modal">Never mind</a>
    </div>
</div>
<form action="#filter" method="post">
<input type="submit" style="visibility: hidden; height: 0; margin: 0; padding: 0;" />
<table class="table table-bordered table-striped">
    <thead>
        <tr><td colspan="999">   
            <div id="invoice_navigation" class="left">         
                <strong>
                    Showing invoices 
                    <?=(($page-1)*$count + 1)?>-<?=($count == 'a') ? $total_invoices : min((($page-1)*$count + $count),$total_invoices)?>
                    of
                    <?=$total_invoices?>.
                </strong><br />
<?php if ($total_pages > 1) : ?>                                
                <a href="<?=sc_cp('Invoicing/view_invoices/1/'.$count)?>" title="First">&lt;&lt;</a>
    <?php if ($page > 2 && $page < $total_pages-1) : ?>
                    ...
    <?php elseif ($total_pages >= 3 & $page == $total_pages ) : ?>
                <a href="<?=sc_cp('Invoicing/view_invoices/'.($page+2).'/'.$count)?>" title="<?=$page-2?>"><?=$page-2?></a> 
    <?php endif?>
    <?php if ($page > 1) : ?>
                <a href="<?=sc_cp('Invoicing/view_invoices/'.($page-1).'/'.$count)?>" title="<?=$page-1?>"><?=$page-1?></a>
    <?php endif ?>
                <?=$page?>
    <?php if ($page < $total_pages) : ?>
                <a href="<?=sc_cp('Invoicing/view_invoices/'.($page+1).'/'.$count)?>" title="<?=$page+1?>"><?=$page+1?></a>       
    <?php endif ?>
    <?php if ($page < $total_pages-1 && $page > 1) : ?>
                ...
    <?php elseif ($total_pages >= 3 & $page == 1 ) : ?>
                <a href="<?=sc_cp('Invoicing/view_invoices/'.($page+2).'/'.$count)?>" title="<?=$page+2?>"><?=$page+2?></a>    
    <?php endif ?>
                <a href="<?=sc_cp('Invoicing/view_invoices/'.$total_pages.'/'.$count)?>" title="Last">&gt;&gt;</a>            
<?php endif ?>                
            </div>  
            <select id="count_selection" class="span3">
                <option value="10">Show 10 a page</option>
                <option value="20">Show 20 a page</option>
                <option value="30">Show 30 a page</option>
                <option value="50">Show 50 a page</option>
                <option value="100">Show 100 a page</option>
                <option value="a">Show all</option>
            </select> 
            <a href="#" class="show-filters"><i class="icon-filter"></i><span>Show filters</span></a>
            <a href="#" class="apply-filters"><i class="icon-refresh"></i> Apply all filters</a>
        </td></tr>        
        <td>Date</td><td>Number</td><td>Customer ID</td><td>Shipping</td><td>Billing</td><td>Status</td><td>Total Lines</td><td></td>            
        <tr id="search_filters">
            <td id="date_filters">
                <div class="input-prepend">
                    <label for="from_date" class="add-on">From:</label><input 
                           type="text" 
                           class="search_filter span1" 
                           id="from_date" 
                           name="from_date" 
                           value="<?=get_post('from_date',date('n/d/y',time()-60*60*24*30))?>" />
                </div>
                <div class="input-prepend">
                    <label for="to_date" class="add-on">To:</label><input 
                           type="text" 
                           class="search_filter span1" 
                           id="to_date" 
                           name="to_date" 
                           value="<?=get_post('to_date',date('n/d/y'))?>" />
                </div>
                
            </td>
            <td>
                <input 
                    type="text" 
                    class="search_filter span2" 
                    name="ordernumber" 
                    value="<?=get_post('ordernumber')?>" 
                    placeholder="&quot;*&quot; is wildcard" />
            </td>
            <td>
                <input 
                    type="text" 
                    class="search_filter span2" 
                    name="custid" 
                    value="<?=get_post('custid')?>" 
                    placeholder="&quot;*&quot; is wildcard"/>
            </td>
            <td>
                <textarea class="search_filter span2" name="ship_info" rows="6"><?=get_post('ship_info')?></textarea>
            </td>
            <td>
                <textarea class="search_filter span2" name="bill_info" rows="6"><?=get_post('bill_info')?></textarea>
            </td>
            <td>
<?php foreach ($statuses as $status) : ?>
                <label for="<?=$status?>" class="checkbox">
                    <input 
                        type="checkbox" 
                        class="search_filter" 
                        id="<?=$status?>" 
                        name="<?=$status?>"
                        <?=get_post($status,'') ? 'checked="checked"' : '' ?> /><?=ucfirst($status)?>
                </label>
<?php endforeach ?>                
            </td>
            <td>
            </td>
            <td>
            </td>           
        </tr>
    </thead>
    <tbody>
<?php foreach ($invoices as $k => $t) : ?>
        <tr>
            <td><?=$t->invoice_date?></td>
            <td>
                <a href="<?=sc_cp('Invoicing/view/'.$t->invoicenumber)?>" title="View invoice"><?=$t->invoicenumber?></a>
            </td>
            <td>
                <a href="<?=sc_cp('Customers/'.$t->custid)?>" title="View customer"><?=$t->custid?></a>
            </td>
            <td>
                <?=$t->shipping_info()?>
            </td>
            <td>
                <?=$t->billing_info()?>
            </td>
            <td class="modify-status">
    <?php if (array_search($t->status,$statuses) !== FALSE) : ?>
                <a href="<?=sc_cp('Invoicing/modify_status/'.$t->id)?>" title="Modify status" ><?=ucfirst($t->status)?></a>
                <select class="span2">
        <?php foreach($statuses as $status) : ?>
                    <option value="<?=$status?>" <?=($status == $t->status) ? 'selected="selected"' : ''?>>
                        <?=ucfirst($status)?>
                    </option>
        <?php endforeach ?>    
                </select>
                <?=ajax_loader()?>
    <?php else : ?>
                <?=ucfirst($t->status)?>
    <?php endif ?>
            </td>         
            <td>
            </td>  
            <td>
                <a href="<?=sc_cp('Invoicing/delete_invoice/'.$t->id)?>" title="Delete Invoice" class="delete-invoice">
                    <i class="icon-trash"></i>
                </a>
                <a href="<?=sc_cp('Invoicing/edit/'.$t->invoicenumber)?>" title="Edit invoice">
                    <i class="icon-pencil"></i>
                </a>    
            </td>     
        </tr>  
<?php endforeach ?>
    </tbody>
</table>
<form>
<script type="text/javascript">
    $(document).ready(function(){
        //Modify status
        $('.modify-status').each(function() {
            var form, loader, link, dropdown;           
            link = $(this).find('a');
            dropdown = $(this).find('select');
            loader = $(this).find('.cp-ajax-loader').hide();
            
            link.click(function(e) {
                e.preventDefault();
                
                $(this).hide();
                dropdown.show();
            });
            
            dropdown
                .hide()
                .change(function(e) {                    
                    var the_new_status = $(this).val();
                    loader.show();                    
                    $.post(link.attr('href'),{new_status: the_new_status},function(data){                        
                        loader.hide();
                        if (data == 'ok') {
                            link.show().html(the_new_status.slice(0,1).toUpperCase()+the_new_status.slice(1));
                            dropdown.hide();        
                        } else {
                            alert(data);
                        }   
                    },'html');
                });

        });
        //Remove invoice   
        $('.delete-invoice').click(function(e) {
            var delete_url = $(this).attr('href');
            var row = $(this).closest('tr');
            var loader = $('#confirm_modal .cp-ajax-loader').hide();
            e.preventDefault();
            
            $('#confirm_modal').modal('show');
            $('#confirm_modal .confirm-delete').unbind('click').click(function(e) {
                e.preventDefault();
                loader.show();
                    
                $.get(delete_url,function(data) {
                    loader.hide();
                    $('#confirm_modal').modal('hide');
                    if (data == 'ok') {    
                        row.fadeOut();    
                    } else {
                        alert(data);
                    }
                },'html');
            });
        });    
        
        //Filters and such
        $('#count_selection')
            .change(function(){
                window.location='<?=sc_cp('Invoicing/view_invoices/'.$page)?>'+$(this).val();
            })
            .children('[value="<?=$count?>"]').first().attr('selected','selected');
        
        if (window.location.hash != '#filter') {
            $('#search_filters, .apply-filters').hide();
        } else {
            $('.show-filters').children('span').html('Hide filters');    
        }
        
        $('.show-filters').click(function(e) {
            e.preventDefault();
            
            if ($('#search_filters').is(':visible')) {
                $('#search_filters, .apply-filters').hide();
                $(this).children('span').html('Show filters');
            } else {
                $('#search_filters, .apply-filters').show();
                $(this).children('span').html('Hide filters');
            }
        });
        
        $('.apply-filters').click(function(e) {
            e.preventDefault();
            
            $('form').submit();
        });
    });
</script>
