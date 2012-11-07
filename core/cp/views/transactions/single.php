<div id="single_transaction">
    <h1><?=ucfirst($t->status)?> Transaction</h1>
    <h3>      
        <a href="<?=sc_cp('Transactions/'.$t->ordernumber)?>" title="View order"><?=$t->ordernumber?></a>
        <span class="shrink"><?=$t->order_date?></span>
    </h3>
    <div class="row">
        <div class="span4">
            <h4>Shipping</h4>
            <?=$t->shipping_info()?>
        </div>
        <div class="span4">
            <h4>Billing</h4>
            <?=$t->billing_info()?>
        </div>
    </div>    
    <div class="row">
        <div class="span">
            <h4>Items</h4>
            <div class="row">
<?php foreach($c as $item) : 
    $i = \Model\Item::find($item['id']);
    ?>
                <div class="span">
                    <?=$i->number?> - <?=$i->name?> - $<?=$item['price']?> x <?=$item['quantity']?>
                    <div class="row">
    <?php foreach($item['options'] as $option) : 
        $o = \Model\Itemoption::find($option['id']);
        ?>
                        <div class="span">
                            <?=$o->code?><?=$o->name?> - $<?=$option['price']?> x <?=$option['quantity']?>
                        </div>
    <?php endforeach ?>
                    </div>
                </div>
<?php endforeach ?>
            </div>
        </div>
    </div> 
    <div class="row">
        <div class="span">
            <h4>Totals</h4>
            <div class="row">
                <div class="span2"><strong>Subtotal</strong></div>
                <div class="span2">$<?=$t->subtotal?></div>
            </div> 
            <div class="row">
                <div class="span2"><strong>Tax</strong></div>
                <div class="span2">$<?=$t->taxrate?></div>
            </div> 
            <div class="row">
                <div class="span2"><strong>Shipping (<?=$t->ship_name?>)</strong></div>
                <div class="span2">$<?=$t->shipping?></div>
            </div> 
            <div class="row"> 
                <div class="span2"><strong>Subtotal</strong></div>
                <div class="span2">-$<?=$t->discount?></div>
            </div> 
            <div class="row">
                <div class="span2"><strong>Total</strong></div>
                <div class="span2">$<?=$t->total?></div>
            </div>             
    </div>     
</div><!--#single_transaction-->
