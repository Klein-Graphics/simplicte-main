<?php
/**
 * Checkout Controller
 *
 * This is the initial checkout script that loads whatever the first screen the 
 * customer needs to be on
 * 
 * @package Checkout
 */          
 
 $this->load_library(array('Session','Cart'));
 
 if ($second = implode('/',$this->URI->get_request())) {        
    $this->load_ajax('checkout/'.$second);
    return;
 }
 
 if ($this->Cart->is_empty($this->Session->get_open_transaction())) {
    header("HTTP/1.1 204 No Content");
    return;
 }
 
 if ($this->Session->has_account()) {
    $this->load_ajax('checkout/verify_cart');
    return;
 }
?>
