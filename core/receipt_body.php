<div id="sc_receipt_body">
    <table id="sc_tidbits">
        <tr id="date"><td>Order Date:</td><td><?=$t->order_date?></td></tr>
        <tr id="invoicenumber"><td>Order Number:	</td><td><?=$t->ordernumber?></td></tr>
    </table><!--#sc_tidbits-->
    <table id="sc_customer_information">
        <thead>
            <tr>
                <td><h3>Shipping Information</h3></td>
                <td><h3>Billing Information</h3></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?=$t->ship_firstname?> <?=$t->ship_initial?> <?=$t->ship_lastname?></td>
                <td><?=$t->bill_firstname?> <?=$t->bill_initial?> <?=$t->bill_lastname?>
            </tr>
            <tr>
                <td><?=$t->ship_streetaddress?> <?=($t->ship_apt)?'#'.$t->ship_apt:''?></td>
                <td><?=$t->bill_streetaddress?> <?=($t->bill_apt)?'#'.$t->bill_apt:''?></td>    
            </tr>
            <tr>
                <td><?=$t->ship_city?>, <?=$t->ship_state?> <?=$t->ship_postalcode?></td>
                <td><?=$t->bill_city?>, <?=$t->bill_state?> <?=$t->bill_postalcode?></td>
            <tr>
            <tr>
                 <td><?=$t->ship_country?></td>
                 <td><?=$t->bill_country?></td>
            </tr>
            <tr>
                <td><?=$t->ship_phone?></td>
                <td><?=$t->bill_phone?></td>     
            </tr>            
        </tbody>                                        
    </table>
    <table id="sc_receipt_items">
        <thead>
            <tr>
            <td>Name</td>
            <td>Options</td>
            <td>Qty</td>
            <td>Price</td>
            </tr>
        </thead>
        <tbody>
<?php foreach($cart as $key => $item) : ?>
            <tr>
                <td class="sc_receipt_item_name">
                    <?=$SC->Items->item_name($item['id'])?>
                </td>
                <td class="sc_receipt_item_options">
    <?php if ($item['options']) : ?> 
                    <ul>
        <?php foreach($item['options'] as $option) : ?>
                        <li>
                            <?=$SC->Items->option_name($option['id'])?>
                            <?=($option['quantity']>1) ? ' x '.$option['quantity'] : ''?>
                            <?=($option['price']) ? ' - $'.number_format($option['quantity']*$option['price'],2) : ''?>
                        </li>
        <?php endforeach ?>
                    </ul>
    <?php endif ?>
                </td>
                <td class="sc_receipt_item_quantity">
                    <?=$item['quantity']?>
                </td>
                <td class="sc_receipt_item_price">
                    $<?=number_format($SC->Cart->line_total($key,$cart),2)?>
                </td>
            </tr>                    
<?php endforeach ?> 
        </tbody>
        <tfoot>    
            <tr><td colspan="3">Sub-Total:</td><td>$<?=$SC->Cart->subtotal($cart)?></td></tr>
<?php if ($t->discount) : ?>
            <tr><td colspan="3">Discount:</td><td>$<?=$t->discount?></td></tr>
<?php endif ?>
<?php if ($t->shipping) : ?>
            <tr><td colspan="3">Shipping:</td><td>$<?=$t->shipping?></td></tr>
<?php endif ?>
            <tr><td colspan="3">Tax</td><td>$<?=$t->taxrate?></td><tr>
            <tr><td colspan="3">Total</td><td>$<?=$SC->Cart->calculate_soft_total($t)?></td></tr>
        </tfoot>
    </table><!--#sc_receipt_totals-->
</div><!--#sc_receipt_body-->

