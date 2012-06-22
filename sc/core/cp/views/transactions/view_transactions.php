<form action="#filter" method="post">
<input type="submit" style="visibility: hidden; height: 0; margin: 0; padding: 0;" />
<table class="table table-bordered table-striped">
    <thead>
        <tr><td colspan="999">   
            <div id="trans_navigation" class="left">         
                <strong>Showing transactions <?=(($page-1)*$count + 1)?>-<?=min((($page-1)*$count + $count),$total_transactions)?> of <?=$total_transactions?>. </strong><br />
                <a href="<?=sc_cp('Transactions/view_transactions/1/'.$count)?>" title="First">&lt;&lt;</a>
<?php if ($page > 2 && $page < $total_pages-1) : ?>
                    ...
<?php elseif ($total_pages >= 3 & $page == $total_pages ) : ?>
                <a href="<?=sc_cp('Transactions/view_transactions/'.($page+2).'/'.$count)?>" title="<?=$page-2?>"><?=$page-2?></a> 
<?php endif?>
<?php if ($page > 1) : ?>
                <a href="<?=sc_cp('Transactions/view_transactions/'.($page-1).'/'.$count)?>" title="<?=$page-1?>"><?=$page-1?></a>
<?php endif ?>
                <?=$page?>
<?php if ($page < $total_pages) : ?>
                <a href="<?=sc_cp('Transactions/view_transactions/'.($page+1).'/'.$count)?>" title="<?=$page+1?>"><?=$page+1?></a>       
<?php endif ?>
<?php if ($page < $total_pages-1 && $page > 1) : ?>
                ...
<?php elseif ($total_pages >= 3 & $page == 1 ) : ?>
                <a href="<?=sc_cp('Transactions/view_transactions/'.($page+2).'/'.$count)?>" title="<?=$page+2?>"><?=$page+2?></a>    
<?php endif ?>
                <a href="<?=sc_cp('Transactions/view_transactions/'.$total_pages.'/'.$count)?>" title="Last">&gt;&gt;</a>            
            </div>  
            <select id="count_selection" class="span3">
                <option value="10">Show 10 a page</option>
                <option value="20">Show 20 a page</option>
                <option value="30">Show 30 a page</option>
                <option value="50">Show 50 a page</option>
                <option value="100">Show 100 a page</option>
                <option value="<?=$total_transactions?>">Show all</option>
            </select> 
            <a href="#" class="show-filters"><i class="icon-filter"></i><span>Show filters</span></a>
            <a href="#" class="apply-filters"><i class="icon-refresh"></i> Apply all filters</a>
        </td></tr>        
        <td>Date</td><td>Number</td><td>Customer ID</td><td>Shipping</td><td>Billing</td><td>Status</td><td>Items</td><td>Subtotal</td><td>Tax</td><td>Shipping</td><td>Discount</td><td>Total</td>            
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
<?php $statuses = array('opened','pending','settled','fulfilled','returned');
foreach ($statuses as $status) : ?>
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
                <input 
                    type="text" 
                    class="search_filter span3" 
                    name="items" 
                    placeholder="Enter item/option number(s)/name(s)" 
                    value="<?=get_post('items')?>"
                    />
            </td>
            <td></td>
            <td></td>
            <td>
                <select name="shipping_provider" class="search_filter span2">
                    <option value=''>All</option>
<?php foreach($shipping_providers as $provider) : ?>
                    <option 
                        value="<?=$provider['code']?>"
                        <?=get_post('shipping_provider') == $provider['code'] ? 'selected="selected"' : ''?>
                    >
                        <?=$provider['name']?>
                    </option>
<?php endforeach ?>
                </select>
            </td><td>
            </td>
            <td></td>
        </tr>
    </thead>
    <tbody>
<?php foreach ($transactions as $k => $t) : ?>
        <tr>
            <td><?=$t->order_date?></td>
            <td>
                <a href="<?=sc_cp('Transactions/'.$t->ordernumber)?>" title="View order"><?=$t->ordernumber?></a>
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
            <td>
                <?=ucfirst($t->status)?>
            </td>
            <td>
                <ul>
    <?php foreach($items[$k] as $item) : ?>
                <li>
                    <?=$this->SC->Items->item_name($item['id']).' - $'.$item['price'].' x '.$item['quantity']?>
                    <ul>
        <?php foreach($item['options'] as $option) : ?>
                            <li><?=$this->SC->Items->option_name($option['id']).' - $'.$option['price'].' x '.$option['quantity']?></li>
        <?php endforeach ?>
                    </ul>
                </li>
    <?php endforeach ?>
                </ul>
            </td>
            <td>
                $<?=$t->subtotal?>
            </td>
            <td>
                $<?=$t->taxrate?>
            </td>
            <td>
                $<?=$t->shipping?><br>
                <em><?=$t->ship_name?></em>
            </td>
            <td>
                -$<?=$t->discount?>
            </td>
            <td>
                $<?=$this->SC->Cart->calculate_soft_total($t)?>
            </td>      
        </tr>  
<?php endforeach ?>
    </tbody>
</table>
<form>
<script type="text/javascript">
    $(document).ready(function(){
        $('#count_selection')
            .change(function(){
                window.location='<?=sc_cp('Transactions/view_transactions/'.$page)?>'+$(this).val();
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
