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
 
 if ($second = implode('/',$this->URI->get_data())) {        
    $this->load_ajax('checkout/'.$second);
    exit;
 }
 
 if ($this->Cart->is_empty($this->Session->transaction)) {
    header("HTTP/1.1 204 No Content");
    exit;
 }
 
 if ($this->Session->has_account()) {
    //Verify that account information is complete first
    $customer = $this->Customer->get_customer($this->Session->get_user());

    $required_fields = array(
        'firstname',
        'lastname',
        'streetaddress',
        'city',
        'state',
        'postalcode',
        'country',
        'phone'
    );

    foreach ($required_fields as $required_field) {
        $ship_field = 'ship_'.$required_field;
        $bill_field = 'bill_'.$required_field;
        if (!$customer->$ship_field or !$customer->$bill_field) {
            $_POST['new_customer'] = TRUE;
            $this->load_ajax('get_customer_details');
            exit();
        }
    } 
    
    
    $this->load_ajax('checkout/verify_cart');
    exit;
 }
?>
