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
     * Construct
     *
     * @return null
     */
    function __construct() {
        global $SC;
        $this->SC = $SC;
    }
}
