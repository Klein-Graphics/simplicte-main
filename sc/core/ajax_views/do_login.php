<?php  
    namespace View;
    function do_login() {
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
        
        //Try to login the customer
        
        if (!$SC->Session->login_customer($_POST)) {
            exit(json_encode(array(
                'do_this' => 'display_error',
                'message' => 'Invalid username or password'
            )));
            
        }
        
        //Return the result
        
        echo json_encode(array(
            'do_this' => 'refresh'
        ));
        
    }
