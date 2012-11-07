<?php
/**
 * Changes customer password
 *
 * @package Account
 */

$this->load_library(array('Validation','Session'));

$this->Validation->add_rule('sc_old_password','Current Password','required');
$this->Validation->add_rule('sc_new_password','New Password','required|match:sc_confirm_password'); 
$this->Validation->add_rule('sc_confirm_password','Confirm New Password','required');   

$validation = $this->Validation->do_validation();

$customer = \Model\Customer::find($this->Session->force_get_user());
if ($_POST['sc_old_password'] != $customer->passwordmd5) {
    $validation = FALSE;
        
    $this->Validation->messages[] = 'Current password is incorrect';
}

if (! $validation) {
    exit(json_encode(array(
        'do_this' => 'display_error',
        'message' => $this->Validation->get_messages()
    )));                   
}

//Change password
$customer->passwordmd5 = $_POST['sc_new_password'];
$customer->save();

echo json_encode(array(
    'do_this' => 'display_good',
    'message' => 'Password changed'
));
