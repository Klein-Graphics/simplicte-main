<div class="sc_close_link">[x]</div>
<h3>Please verify your cart and shipping/billing details.</h3>
<div class="sc_verify_cart_message_area">
<?php foreach ($messages as $message) : ?>
    <div class="sc_verify_cart_message"><?=$message?></div>
<?php endforeach ?>
</div>
<?php if (!$this->Cart->is_empty($cart)) : ?>
<form action="<?=sc_ajax('checkout/verify_cart')?>" method="POST" class="sc_verify_form">
    <div class="sc_discount_code_area">        
            <p>If you have a discount code, please enter it below:</p>
            <input type="text" name="discount" value="<?=(isset($_POST['discount'])?$_POST['discount']:'')?>" />
            <input type="submit" value="Submit" />        
    </div>
    <table class="sc_verify_cart_total_area">
        <tr><td>Subtotal:</td><td><?=number_format($order_totals['subtotal'],2)?></td></tr>
        <tr><td>Tax:</td><td><?=number_format($order_totals['taxrate'],2)?></td></tr>
            
        <?php if ($this->Shipping->shipping_enabled && $shipping_required) : ?>
        <tr>
                <?php if ($order_totals['shipping'] !== false) : ?>
            <td>Shipping:</td><td><?=number_format($order_totals['shipping'],2)?></td>
        </tr>
        <tr>
            <td></td><td><?=$this->Shipping->generate_shipping_dropdown()?></td>        
                         
                <?php else :?>
            
            <td>Shipping:</td><td><?=$this->Shipping->generate_shipping_dropdown()?></td>
            
                <?php endif ?>
         </tr>                
        <?php endif ?>

         <tr><td>Total:</td><td><?=number_format($order_totals['total'],2)?></td></tr>                  

    </table>
    <?php if ($this->Shipping->shipping_enabled && $shipping_required && is_numeric($order_totals['shipping'])) : ?> 
    <input type="button" class="sc_verify_cart_submit" value="Continue To Payment" />
    <?php endif ?>
</form>
<div class="sc_verify_cart_shipbill_details">
    <div>
        <h3>Shipping: <sub><a href="<?=sc_ajax('get_customer_details')?>" class="sc_edit_details" title="Edit">Edit</a></sub></h3>
        <?=$transaction->shipping_info()?>
    </div>
    <div>
        <h3>Billing: <sub><a href="<?=sc_ajax('get_customer_details')?>" class="sc_edit_details" title="Edit">Edit</a></sub></h3>
        <?=$transaction->billing_info()?>        
    </div>
</div>
<?php endif ?>

<script type="text/javascript">
    $(document).ready(function() { 
        page_display.cart.refresh();
               
        $(".sc_verify_form").submit(function(e) {    
            e.preventDefault();
            
            page_display.checkout.load($(this).attr('action'),$(this).serializeArray());            
        });                 
        
        $(".sc_verify_cart_submit").click(function(e) {
            e.preventDefault();
            
            page_display.checkout.load(sc_location('/ajax/checkout/payment'),$(".sc_verify_form").serializeArray());
        });
        
        $(".sc_shipping_method_select").change(function() {
            $(".sc_verify_form").submit();    
        });   
        
        $(".sc_edit_details").click(function(e) {
            e.preventDefault();
            
            page_display.checkout.load($(this).attr('href'));
        });            
    });
</script>


