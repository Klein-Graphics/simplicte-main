<?php

/**
 * Local Pickup Shipping Rate
 *
 * @package Checkout\Shipping Drivers
 */
 
namespace Shipping_Driver;

/**
 * Local Pickup Shipping Rate Driver
 *
 * @package Checkout\Shipping Drivers
 */
 
class Pickup extends \SC_Shipping_Driver {
    
    /**
     * Human Readable Name
     */         
    public $name = 'Local Pickup';    
    
    /**
     * Shipping codes
     */
    public $shipping_codes = array(
            'lp' => 'Local Pickup'
        ); 
    
    function get_rate() {
        return 0;
    }
    
    function get_rate_from_cart() {
        return 0;
    }
    
    function get_rate_from_transaction() {
        return 0;
    }
}
