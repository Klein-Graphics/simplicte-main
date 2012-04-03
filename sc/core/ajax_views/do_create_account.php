<?php
    //Remove the temp stamp from the customer ID
    $this->load_library(array('Validation','Customer','Session'));
    
    $this->Validation->add_rule('sc_register_email','Email','required|email');
    $this->Validation->add_rule('sc_register_password','Password','required|match:sc_confirm_password'); 
    $this->Validation->add_rule('sc_confirm_password','Confirm','required');         
    
    //Validate the data
    $validation = $this->Validation->do_validation();    
    
    if ($this->Customer->get_customer($_POST['sc_register_email'],'*','email')) {
        $validation = FALSE;
        
        $this->Validation->messages[] = 'Another account with this email address exists';
    }
    
    if (!$validation) {
        exit(json_encode(array(
            'do_this' => 'display_error',
            'message' => $this->Validation->get_messages()
        )));                   
    }
    
    $_SESSION['customer_id'] = str_replace('temp','',$this->Session->get_customer());
    
    $this->Customer->update_customer($this->Session->get_user(),array(
        'custid' => $this->Session->get_customer(),
        'email' => $_POST['sc_register_email'],
        'passwordmd5' => $_POST['sc_register_password']
    ));    
    
    echo json_encode(array(
        'do_this' => 'load',
        'location' => sc_ajax('get_customer_details'),
        'data' => array(
            'new_customer' => 1
        )
    ));
    
