<?php

    /**
     * @ignore
     *
     * @package foo
     */

$_POST = array(
 'x_invoice_num' => '1205080067',
 'x_card_type' => 'VISA',
 'x_account_number' => '1111',
 'x_response_code' => 1,
 'x_response_reason_text' => 'Success',
);  

include('core/includes/Curl.php');

$curl = new Curl();
echo $curl->post('http://localhost/dockets/carefast/site/sc/relay/Authorizedotnet_SIM/'.$_COOKIE['PHPSESSID'],$_POST);

/*better_print_r($SC->Transactions->get_transaction($transaction->id));

$SC->Transactions->update_transaction($transaction->id,array(
    'status' => 'pending' ,
    'paytype' => ''
));
*/
