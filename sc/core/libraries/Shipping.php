<?php
/**
 * Shipping Library
 *
 * Handles loading shipping drivers and taring the cart
 *
 * @package Checkout
 */

namespace Library;

/**
 * Shipping Library Class
 *
 * @package Checkout
 */
class Shipping extends \SC_Library {
    
    /**
     * Construct
     *
     * Loads the drivers specified in the store configuration
     *
     * @return null
     */
    function __construct() {
        parent::__construct();
        
        require_once('core/libraries/shipping_drivers/Shipping_Driver.php');
        $shipping_drivers = $this->SC->Config->get_setting('shipping_drivers');
        
        $this->Drivers = new \stdClass;
        
        if ($shipping_drivers) {
            $this->shipping_drivers = array();
            $drivers = explode('|',$shipping_drivers);
            
            foreach ($drivers as $key => $driver) {
                $driver = explode(':',$driver);                
                $this->shipping_drivers[$driver[0]] = explode(',',$driver[1]);            
            }
            $this->shipping_enabled = TRUE;
            
            $this->load_driver($this->shipping_drivers);
        } else {
            $this->shipping_drivers = FALSE;
            $this->shipping_enabled = FALSE;
        }        
    }
    
    /**
     * Load Driver
     *
     * Loads a specific shipping driver
     *
     * @return bool Returns whether or not the driver(s) loaded
     *
     * @param string|string[] $driver Name of driver or an array where the 
     * keys are driver names and the values are arrays of allowed methods
     * @param string[] An array of avaiable methods.
     */
    function load_driver($driver,$methods=NULL) {
        
        if (is_array($driver)) {
            $good = TRUE;
            foreach ($driver as $driver_name => $driver_methods) {
                $good = min($good,$this->load_driver($driver_name,$driver_methods));                
            }
            
            return $good;
        }
    
        $driver = ucfirst($driver);
        
        if (!file_exists("core/libraries/shipping_drivers/$driver.php")) {
            trigger_error("Shipping driver '$driver' does not exist");
            return FALSE;
        }
        
        include_once "core/libraries/shipping_drivers/$driver.php"; 
        
        $namespaced_driver = "\Shipping_Driver\\$driver";
        
        $this->Drivers->$driver = new $namespaced_driver;
        
        $this->Drivers->$driver->enabled_methods = $methods;         
        
        return TRUE;
        
    }
    
    /**
     * Get Shipping Methods
     *
     * Returns a list of shipping methods
     *
     * @return array An array of methods where the key is the name of the service
     * and the value is an array of the actuall shipping options
     */
    function get_shipping_methods() {
        
        $shipping_methods = array();
        
        foreach ($this->Drivers as $key => $driver) {
            $shipping_methods[$key] = $driver->get_shipping_codes();
        }
        
        return $shipping_methods;
    }
    
    /**
     * Generate Shipping Dropdown
     *
     * Generates a dropdown menu of shipping methods
     *
     * return string
     */
    function generate_shipping_dropdown() {
        $shipping_methods = $this->get_shipping_methods();                
        
        $output = '<select name="shipping_method" class="sc_shipping_method_select">
                   <option disabled="disabled" selected="selected" value=0>Select a shipping method...</option>
        ';              
        
        foreach ($shipping_methods as $method_number => $shipping_method) {
            foreach ($shipping_method as $shipping_code => $shipping_code_name) {
                
                $output .= "<option 
                              value=\"$method_number-$shipping_code\"
                              ".((isset($_POST['shipping_method']) 
                                  && $_POST['shipping_method'] == "$method_number-$shipping_code") 
                                ? "selected=\"selected\""
                                : ""
                              )."
                              >$shipping_code_name</option>";
            }            
        }
        
        $output .= '</select>';
        
        return $output;
    }
    
}
