<?php 
/**
 * Checkout Driver
 *
 * @package Checkout\Checkout_Drivers
 */

/**
 * Checkout Driver Parent Class
 *
 * @package Checkout\Checkout_Drivers
 */
class SC_Checkout_Driver {
    /**
     * Construct
     *
     * @return null
     */  
    function __construct() {
        global $SC;
        $this->SC = $SC;
    }
}
