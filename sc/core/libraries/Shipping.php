<?php

namespace Library;

class Shipping extends \SC_Library {
 
    function __construct() {
        parent::__construct();
        
        require_once('core/libraries/shipping_drivers/Shipping_Driver.php');
        $shipping_drivers = $this->SC->Config->get_setting('shipping_drivers');
        
        $this->Drivers = new \stdClass;
        
        if ($shipping_drivers) {
            $this->shipping_drivers = explode(',',$shipping_drivers);
            $this->shipping_enabled = TRUE;
            
            $this->load_driver($this->shipping_drivers);
        } else {
            $this->shipping_drivers = FALSE;
            $this->shipping_enabled = FALSE;
        }        
    }
    
    function load_driver($driver) {
        
        if (is_array($driver)) {
            $good = TRUE;
            foreach ($driver as $this_driver) {
                $good = min($good,$this->load_driver($this_driver));                
            }
            
            return $good;
        }
    
        $driver = ucfirst($driver);
        
        if (!file_exists("core/libraries/shipping_drivers/$driver.php")) {
            trigger_error("Shipping driver '$driver' does not exist");
            return false;
        }
        
        include_once "core/libraries/shipping_drivers/$driver.php"; 
        
        $namespaced_driver = "\Shipping_Driver\\$driver";
        
        $this->Drivers->$driver = new $namespaced_driver;
        
        return true;
        
    }
    
    function get_shipping_methods() {
        
        $shipping_methods = array();
        
        foreach ($this->Drivers as $key => $driver) {
            $shipping_methods[$key] = $driver->get_shipping_codes();
        }
        
        return $shipping_methods;
    }
    
    function generate_shipping_dropdown() {
        $shipping_methods = $this->get_shipping_methods();
        
        /*
         * TODO: Weed out disabled shipping methods
         */
        
        $output = '<select name="shipping_method" class="sc_shipping_method_select">
                   <option disabled="disabled" selected="selected">Select a shipping method...</option>
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
