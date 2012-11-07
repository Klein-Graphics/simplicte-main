<?php
/**
 * Validates and processes account creation
 *
 * @package Account
 */

$this->load_library(array('Validation','Customer','Session'));

$this->Validation->add_rule('sc_register_email','Email','required|email');    

//Validate the data
$validation = $this->Validation->do_validation();    

if ($check_cust = $this->Customer->get_customer($_POST['sc_register_email'],'*','email')) { 
    if (strpos($check_cust->custid,'temp') === FALSE) {    
        $validation = FALSE;
        
        $this->Validation->messages[] = 'Another account with this email address exists.';
    }
}

if (!$validation) {
    exit(json_encode(array(
        'do_this' => 'display_error',
        'message' => $this->Validation->get_messages()
    )));                   
}


$this->Customer->update_customer($this->Session->get_user(),array(
    'email' => $_POST['sc_register_email']
));    

echo json_encode(array(
    'do_this' => 'load',
    'location' => sc_ajax('get_customer_details'),
    'data' => array(
        'new_customer' => 1 
    )
)); 
