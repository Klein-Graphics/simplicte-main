<table class="table table-bordered table-striped">
    <thead>
        <td>Type</td><td>Number</td><td>Customer ID</td><td>Shipping</td><td>Billing</td><td>Status</td><td>Items</td><td>Tax</td><td>Shipping</td><td>Discount</td><td>Total</td>    
    </thead>
    <tbody>
<?php foreach ($transactions as $k => $t) : ?>
        <tr>    

            
            <td><?=ucfirst($t->transtype)?></td>
            <td><?=$t->ordernumber?></td>
            <td><?=$t->custid?></td>
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
                    <?=$this->SC->Items->item_name($item['id']).' - '.$item['price'].'x'.$item['quantity']?>
                    <ul>
        <?php foreach($item['options'] as $option) : ?>
                            <li><?=$this->SC->Items->option_name($option['id']).' - '.$option['price'].'x'.$option['quantity']?></li>
        <?php endforeach ?>
                    </ul>
                </li>
    <?php endforeach ?>
                </ul>
            </td>
            <td>
                <?=$t->taxrate?>
            </td>
            <td>
                <?=$t->shipping?>
            </td>
            <td>
                -<?=$t->discount?>
            </td>
            <td>
                <?=$this->SC->Cart->calculate_soft_total($t)?>
            </td>      
        </tr>  
<?php endforeach ?>
    </tbody>
</table>
