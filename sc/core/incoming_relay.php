<?php

/**
 * Simplecart2 Relay File
 *
 * This file is the final destination of the data coming from the payment gateway. 
 * Payment drivers should be setup to translate data from the gateway result into
 * here. The file expects the following post data:
 *
 * **transaction* Transaction Number
 * **method* The method by which the transaction was settled
 * **hash* The security hash, containing the transaction number and the store's seed,
 * seperated by a space and md5'd;
 * **status_code* Boolean value of whether or not the transaction was a success
 * **status_text* If the transaction was unsuccessful, why it was unsuccessful
 *
 * @package Checkout
 */

require 'init.php';

$CONFIG['DUMP_SESSION'] = FALSE;

$SC->load_library(array('Transactions','Cart','Stock','Session'));

$messages = array();

//Get all post data and make sure that the posted key is right

$transaction = $_POST['transaction'];

$transaction = (object) $SC->Transactions->get_transaction($transaction,'*','ordernumber')->to_array();

$transaction->items = $SC->Cart->explode_cart($transaction->items);

$their_hash = $_POST['hash'];
$my_hash = md5("{$transaction->ordernumber} {$CONFIG['SEED']}");

if ($their_hash != $my_hash) {
    die(json_encode(array(
        'transaction' => $_POST['transaction'],
        'status_code' => 0,
        'status_text' => 'Invalid security hash'
    )));
}

if (!$_POST['status_code']) {
    die(json_encode(array(
        'transaction' => $_POST['transaction'],
        'status_code' => 0,
        'status_text' => $_POST['status_text']
    )));
}

$messages[] = 'Transaction Successful!';

//Change transaction status to settled and update the payment type
$SC->Transactions->update_transaction($transaction->id,array(
    'status' => 'settled' ,
    'paytype' => $_POST['method']
));

//Verify stocking was correct
if ($transaction->status != 'pending') {
    //The items were never taken out of stock so that needs to be done.
    $SC->Stock->pull_cart($transaction->items);
}

if ($understocked_items = $SC->Stock->verify_stock(0,$transaction->items)) {
    //TODO: MESSAGE STORE OWNER   
}

//Reset session
$SC->Session->new_transaction();

//Perform any external item script hooks
require_once 'includes/Curl.php';
foreach ($transaction->items as $item) {    
    if ($script = $SC->Items->item_flag($item['id'],'extscript')) {
        $script = $script[1];
        
        $curl = new \Curl();
        
        $result = $curl->post(
            sc_location("external_scripts/$script"),
            $SC->Transactions->get_transaction($transaction->id)->to_array()
        );
        
        if ($result) {
            $messages[] = $result;
        }        
    }
}

//Send emails
//TODO: MESSAGE CUSTOMER AND OWNER

$messages = implode('/',$messages);


echo json_encode(array(
        'transaction' => $transaction->ordernumber,
        'status_code' => 1,
        'status_text' => $messages,
    ));




