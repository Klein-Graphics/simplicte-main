<?php
/**
 * Gateways Library
 *
 * This library handles the loading and managment of the various payment gateway
 * drivers.
 *
 * @package Checkout
 */

namespace Library;
/**
 * The Gateways Library Class
 *
 * @package Checkout
 */
class Gateways extends \SC_Library {
    
    /** 
     * Construct
     *
     * Loads the primary Gateway_Driver class and run the initilization method
     *
     * @return null
     */
    function __construct() {
        parent::__construct();
        
        require_once('core/libraries/gateway_drivers/Gateway_Driver.php');        
        
        $this->Drivers = new \stdClass;
        $this->initialize_drivers();
    }
    
    /**
     * Initialize Drivers
     *
     * Loads the Payment Drivers specificed by the "paymentmethods" configuration.
     * returns whether or not the initialization was completely successful
     *
     * @return bool
     */
    function initialize_drivers() {
        $drivers = $this->SC->Config->get_setting('paymentmethods');
        
        $drivers = explode('|',$drivers);
        
        $success = TRUE;
        foreach ($drivers as &$driver) {
            
            $success = min(call_user_func_array(array($this,'load_driver'),explode(',',$driver)),$success);
        }
        
        return $success;
    }    
    
    /**
     * Load Driver
     * 
     * Loads an individual payment driver
     *
     * @return bool Returns whether or not the driver it exists
     *
     * @param int $method The name of the driver
     * @param int $name The human readable description of the driver I.E "Paypal"
     */
    function load_driver($method,$name=FALSE) {        
            
        $method = ucfirst($method);
        $file = "core/libraries/gateway_drivers/$method.php";

        if (!file_exists($file)) {
            trigger_error("Gateway Driver '$method' does not exist");
            return FALSE;
        }
        
        $namespaced_driver = "\\Gateway_Driver\\$method";                
        
        include_once $file;
        $this->Drivers->$method = new $namespaced_driver($name);
        
        return TRUE;                                                
                                                   
    }  
    
    /**
     * Number of Gateways
     *
     * Returns the number of gateways available
     
     * @return int
     */
    function number_of_gateways() {
        return count((array) $this->Drivers);
    }    
    
    /**
     * Generate Gateway Dropdown
     *
     * Generates the html code for the selection box that the user will be
     * presented at checkout.
     *
     * @return string
     */
    function generate_gateway_dropdown() {
    
        $output = '<select name="payment_gateway" class="sc_payment_gateway_select">
                    <option disabled="disabled" selected="selected">Select a payment method...</option>';
        
        foreach ($this->Drivers as $method => $driver) {
            $output .= "<option value=\"$method\">{$driver->name}</option>";
        }
        
        $output .= "</select>";
        
        return $output;
    }
    
}
