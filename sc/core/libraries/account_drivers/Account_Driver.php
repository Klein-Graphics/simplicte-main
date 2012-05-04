<?php 
/**
 * Account Driver
 *
 * @package Account
 */
 
/**
 * Account Driver Parent Class
 *
 * @package Account\Drivers
 */
class SC_Account_Driver {
    
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
