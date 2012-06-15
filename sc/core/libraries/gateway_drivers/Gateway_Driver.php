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
        $this->name = ($name) ? $name : self::$default_name;
        
        $class_name = explode('\\',get_class($this));       
        $class_name = $class_name[1]; 
        
        if (!session_id()) {
            session_start();
            $this->outgoing_relay_url = sc_location('relay/'.$class_name.'/'.session_id().'/');      
            session_write_close();
        } else {
            $this->outgoing_relay_url = sc_location('relay/'.$class_name.'/'.session_id().'/');    
        }        
    }            
}
