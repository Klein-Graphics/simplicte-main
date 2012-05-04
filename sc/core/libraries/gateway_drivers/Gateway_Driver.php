<?php
/**
 * Gateway Driver
 *
 * @package Checkout\Gateway Drivers
 */

/**
 * Checkout Driver Parent Class
 *
 * @package Checkout\Gateway Drivers
 */
class SC_Gateway_Driver {
    /**
     * Construct
     *
     * @return null
     *
     * @param string $name Overwrites the default human readable name
     */
    function __construct($name=FALSE) {
        global $SC;
        $this->SC = $SC;
        if ($name) {
            $this->name = $name;
        }
    }
}
