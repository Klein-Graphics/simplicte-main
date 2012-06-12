<?php

/**
 * Validates and processes updating the customer's account details
 *
 * @package Account
 */

$this->load_library(array('Customer','Validation','Session','Transactions'));

$required_fields = array(
        array(
            'ship_firstname',            
            'ship_lastname',
            'ship_streetaddress',
            'ship_city',
            'ship_state',
            'ship_postalcode',
            'ship_phone',
            'bill_firstname',            
            'bill_lastname',
            'bill_streetaddress',
            'bill_city',
            'bill_state',
            'bill_postalcode',
            'bill_phone'
        ),
        array(
            'Shipping First Name',   
            'Shipping Last Name',
            'Shipping Street Address',   
            'Shipping City',
            'Shipping State',
            'Shipping Postal Code',
            'Shipping Phone',
            'Billing First Name',   
            'Billing Last Name',
            'Billing Street Address',   
            'Billing City',
            'Billing State',
            'Billing Postal Code',
            'Billing Phone'
        )
    );
    
$this->Validation->add_rule($required_fields[0],$required_fields[1],'required');

if (!$this->Validation->do_validation()) {
    exit(json_encode(array(
        'do_this' => 'display_error',
        'message' => $this->Validation->get_messages()        
    )));               
}

unset($_POST['sc_copy_information']);

$new_customer = FALSE;

if (isset($_POST['new_customer'])) {
    $new_customer = TRUE;
    unset($_POST['new_customer']);
}

$this->Customer->update_customer($this->Session->get_user(),$_POST);
$this->Transactions->associate_customer($this->Session->get_open_transaction(),$this->Session->get_user());

echo json_encode(array(
        'do_this' => ($new_customer) ? 'refresh' : 'display_good',
        'message' => 'Information updated'
    ));
