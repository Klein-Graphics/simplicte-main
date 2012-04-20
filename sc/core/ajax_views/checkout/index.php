<?php
    /*
     * This is the initial checkout script that 
     * loads whatever the first screen the customer
     * needs to be on
     */       
     
     echo '<span class="sc_close_link">[x]</span>';
     
     $this->load_library(array('Session','Cart'));
     
     if ($second = implode('/',$this->URI->get_request())) {        
        $this->load_ajax_view('checkout/'.$second);
        exit;
     }
     
     if ($this->Cart->is_empty($this->Session->get_open_transaction())) {
        header("HTTP/1.1 204 No Content");
        exit;
     }
     
     if ($this->Session->has_account()) {
        $this->load_ajax_view('checkout/verify_cart');
        exit;
     }
     
     
     //If they're not signed in do this stuff
?>

<style type="text/css">
    #sc_checkout .sc_checkout_login .sc_close_link,
    #sc_checkout .sc_checkout_new_account .sc_close_link {
        display: none;
    }    
</style>

<div class="sc_checkout_login">
    If you have already created an account, login below:
    <?php $this->load_ajax_view('login');?>
</div><!--.sc_checkout_login-->
<div class="sc_checkout_new_account">
    If you don't have an account, you can create one now:
    <?php $this->load_ajax_view('create_account');?>
</div>
<div class="sc_checkout_no_register">
    <a href="<?=sc_ajax('enter_customer_details')?>">Continue without registering</a>
</div>
