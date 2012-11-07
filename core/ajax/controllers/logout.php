<?php 

/**
 * Logs out the customer
 *
 * @package Account
 */    
    
global $SC;

$SC->load_library('Session');

$SC->Session->logout_customer();

$back = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : site_url());
header( 'Location: '.$back );
