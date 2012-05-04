<?php 
/**
 * Clears the customers's cart.
 *
 * @package Transactions
 */

global $SC;
$SC->load_library(array('Cart','Session'));

$SC->Cart->clear_cart($SC->Session->get_open_transaction());

$SC->load_ajax('view_cart');
