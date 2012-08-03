<?php

/**
 * Resets customer password
 *
 * @package Account
 */
 
$this->load_library(array('Validation'));

$this->Validation->add_rule('sc_email','Email','required|email');

$customer = \Model\Customer::first(array(
                'conditions' => array('email = ? AND passwordmd5',$_POST['sc_email'])
            ));

$validation = $this->Validation->do_validation();

if (! $customer) {
    $validation = FALSE;
    $this->Validation->messages[] = 'No account with this email address exists';
}

if (! $validation) {
    die(json_encode(array(
        'do_this'=>'display_error',
        'message'=>$this->Validation->get_messages()
    )));
}

//generate new password

$password = rand_string(6);

$customer->passwordmd5 = md5($password);


$sent = $this->Messaging->message_customer(
            $customer->custid,
            '<h2>'.$this->Config->get_setting('storename').'</h2>
            Your password has been reset, please login to the store using the password below. After logging in you
            may change your password.<br /><br />
                New password: <strong>'.$password.'</strong>',
            'Password reset'
            );
            
if ($sent) {
    echo json_encode(array(
            'do_this'=>'display_good',
            'message'=>'Password reset. Please check your email'
         ));
    $customer->save();     
} else {
    echo json_encode(array(
            'do_this'=>'display_error',
            'message'=>'There was an issue. Try again later.'
         ));
}
            


            


