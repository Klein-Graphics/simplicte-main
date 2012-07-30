<?php
/**
 * Shipping Driver
 *
 * @package Checkout\Shipping Drivers
 */

/**
 * Shipping Driver Parent Class
 *
 * @package Checkout\Shipping Drivers
 */
class SC_Shipping_Driver {
    /**
     * Enabled methods
     */
    
    public $enabled_methods; 
     
    /**
     * Construct
     *
     * @return null
     */    
    function __construct() {
        global $SC;
        $this->SC = $SC;                
    }
    
    function get_shipping_codes() {
        return array_intersect_key(self::$shipping_codes,array_fill_keys($this->enabled_methods,'foo'));
        
    }
}
