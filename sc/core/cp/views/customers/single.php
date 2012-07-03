<div id="single_customer">
    <h1><?=$c->ship_fullname?></h1>
    <h3>      
        <a href="<?=sc_cp('Customers/'.$c->custid)?>" title="View order"><?=$c->custid?></a>        
    </h3>
    <div>Customer since <?=$c->join_date?></div>
    <div class="row">
        <div class="span4">
            <h4>Shipping</h4>
            <?=$c->shipping_info()?>
        </div>
        <div class="span4">
            <h4>Billing</h4>
            <?=$c->billing_info()?>
        </div>
    </div>    
    <table class="table table-striped table-bordered">
    
    </table>      
</div><!--#single_transaction-->
