<?php 
/**
 * Outgoing Relay
 *
 * Calls the relay function of a specific payment gateway
 *
 * @package Checkout
 */
 
require_once 'init.php';
$CONFIG['DUMP_SESSION'] = FALSE;

$SC->load_library(array('URI','Gateways'));

$method = $this->URI->get_request();
call_user_func_array(
    array($SC->Gateways->Drivers->$method,'relay'),
    $this->URL->get_data()
);


    

