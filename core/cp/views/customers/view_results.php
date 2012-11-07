<table class="table table-bordered table-striped">
    <thead>
        <tr><th colspan="9999"><a href="<?=sc_cp('Customers')?>" title="New Search"><i class="icon-search"></i> New search</a></th></tr>
        <tr><th>Customer ID#</th><th>Email</th><th>Shipping Address</th><th>Billing Address</th></tr>
    </thead>
    <tbody>
<?php foreach($customers as $c) : ?>
        <tr>
            <td><a href="<?=sc_cp('Customers/'.$c->custid)?>" title="View customer"><?=$c->custid?></a></td>
            <td><?=$c->email?></td>
            <td>
                <?=$c->ship_fullname?><br />
                <?=$c->ship_streetaddress.' '.$c->ship_full_apt?><br />
                <?=$c->ship_city_state?> <?=$c->ship_postalcode?><br />
                <?=$c->ship_country?><br /><br />
                <?=$c->ship_phone?>                                            
            </td>
            <td>
                <?=$c->bill_fullname?><br />
                <?=$c->bill_streetaddress.' '.$c->bill_full_apt?><br />
                <?=$c->bill_city_state?> <?=$c->bill_postalcode?><br />
                <?=$c->bill_country?><br /><br />
                <?=$c->bill_phone?>                                            
            </td>
<?php endforeach ?>
    </tbody>
</table>   
