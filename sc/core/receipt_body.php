
<div id="sc_receipt_body">
    <div id="sc_tidbits">
        <div id="date">Order Date: <?=$t->order_date?></div>
        <div id="invoicenumber">Invoice #<?=$t->ordernumber?></div>
    </div><!--#sc_tidbits-->
    <div id="sc_customer_info">
        <div id="sc_shipping_info">
            <h4>Shipping</h4>
            <?=$t->ship_firstname?> <?=$t->ship_initial?> <?=$t->ship_lastname?><br />
            <?=$t->ship_streetaddress?> <?=($t->ship_apt) ? "#{$t->ship_apt}" : ''?><br />
            <?=$t->ship_city?>, <?=$t->ship_state?> <?=$t->ship_postalcode?><br />
            <?=$t->ship_country?><br /><br />
            <?=$t->ship_phone?>                
        </div><!--#sc_shipping_info-->
        <div id="sc_billing_info">
            <h4>Billing</h4>
            <?=$t->bill_firstname?> <?=$t->bill_initial?> <?=$t->bill_lastname?><br />
            <?=$t->bill_streetaddress?> <?=($t->bill_apt) ? "#{$t->bill_apt}" : ''?><br />
            <?=$t->bill_city?>, <?=$t->ship_state?> <?=$t->bill_postalcode?><br />
            <?=$t->bill_country?><br /><br />
            <?=$t->bill_phone?>  
        </div><!--#sc_billing_info-->
    </div><!--#sc_customer_info-->
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
    </table><!--#sc_receipt_items-->
    <table id="sc_receipt_totals">
        <tr><td>Sub-Total:</td><td><?=$SC->Cart->subtotal($cart)?></td></tr>
<?php if ($t->discount) : ?>
        <tr><td>Discount:</td><td><?=$t->discount?></td></tr>
<?php endif ?>
<?php if ($t->shipping) : ?>
        <tr><td>Shipping:</td><td><?=$t->shipping?></td></tr>
<?php endif ?>
        <tr><td>Tax</td><td><?=$t->taxrate?></td><tr>
        <tr><td>Total</td><td><?=$SC->Cart->calculate_soft_total($t)?></td></tr>
    </table><!--#sc_receipt_totals-->
</div><!--#sc_receipt_body-->

