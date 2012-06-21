<div class="sc_close_link">[x]</div>
<div class="sc_verify_cart_message_area">
<?php foreach ($messages as $message) : ?>
    <div class="sc_verify_cart_message"><?=$message?></div>
<?php endforeach ?>
</div>
<?php if (!$this->Cart->is_empty($cart)) : ?>
<form action="<?=sc_ajax('checkout/verify_cart')?>" method="POST" class="sc_verify_form">
    <div class="sc_discount_code_area">        
            <p>If you have a discount code, please enter it here:</p>
            <input type="text" name="discount" value="<?=(isset($_POST['discount'])?$_POST['discount']:'')?>" />
            <input type="submit" value="Submit" />        
    </div>
    <table class="sc_verify_cart_total_area">
        <tr><td>Subtotal:</td><td><?=$order_totals['subtotal']?></td></tr>
        <tr><td>Tax:</td><td><?=$order_totals['taxrate']?></td></tr>
            
        <?php if ($this->Shipping->shipping_enabled && $shipping_required) : ?>
        <tr>
                <?php if ($order_totals['shipping'] !== false) : ?>
            <td>Shipping:</td><td><?=$order_totals['shipping']?></td>
        </tr>
        <tr>
            <td></td><td><?=$this->Shipping->generate_shipping_dropdown()?></td>        
                         
                <?php else :?>
            
            <td>Shipping:</td><td><?=$this->Shipping->generate_shipping_dropdown()?></td>
            
                <?php endif ?>
         </tr>                
        <?php endif ?>

         <tr><td>Total:</td><td><?=$order_totals['total']?></td></tr>                  

    </table>
    <?php if ($this->Shipping->shipping_enabled && $shipping_required && $order_totals['shipping'] !== false) : ?> 
    <input type="button" class="sc_verify_cart_submit" value="Continue To Payment" />
    <?php endif ?>

</form>

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
    });
</script>


