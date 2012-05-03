<?php

/**
 * Simplecart2 Relay File
 *
 * This file the final destination of the data coming from the payment gateway. 
 * Payment drivers should be setup to translate data from the gateway result into
 * here. The file expects the following post data:
 *
 * **transaction* Transaction Number
 * **hash* The security hash, containing the transaction number and the store's generated 
 *  security key, seperated by a space and md5'd;
 * **status_code* Boolean value of whether or not the transaction was a success
 * **status_text* If the transaction was unsuccessful, why it was unsuccessful
 *
 * @package Checkout
 */

require 'init.php';

$SC->load_library(array('Transaction'));

/**
 * Get all post data and make sure that the posted key is right
 */
 
$transaction = $_POST['transaction'];

$transaction = $SC->Transaction->get_transaction();

