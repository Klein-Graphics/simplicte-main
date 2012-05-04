<?php
/**
 * Cart Driver
 *
 * @package Transactions\Cart_Drivers
 */

/**
 * Cart Driver Parent Class
 *
 * @package Transactions\Cart_Drivers
 */
class SC_Cart_Driver {
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
