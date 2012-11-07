<?php 
/**
 * Validates and Processes the login
 *
 * @package Account
 */
 
global $SC;        
$SC->load_library(array('Validation','Session'));

$SC->Validation->add_rule('sc_login_email','Email','required|email');
$SC->Validation->add_rule('sc_login_password','Password','required');      

//Validate the data
$validation = $SC->Validation->do_validation();

if (!$validation) {
    exit(json_encode(array(
        'do_this' => 'display_error',
        'message' => $SC->Validation->get_messages()
    )));
               
}

//A little hack to make sure the password is MD5'd
if (strlen($_POST['sc_login_password']) != 32) {
    $_POST['sc_login_password'] = md5($_POST['sc_login_password']);    
}

//Try to login the customer
if (!$SC->Session->login_customer($_POST)) {
    exit(json_encode(array(
        'do_this' => 'display_error',
        'message' => 'Invalid username or password. <strong>Passwords are case senstive</strong>.'
    )));
    
}

//Make sure the customer has entered all required information
$customer = $SC->Customer->get_customer($SC->Session->get_user());

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
        exit(json_encode(array(
            'do_this' => 'load',
            'location' => sc_ajax('get_customer_details')
        )));
    }
}           


//Return the result

echo json_encode(array(
    'do_this' => 'refresh'
));
