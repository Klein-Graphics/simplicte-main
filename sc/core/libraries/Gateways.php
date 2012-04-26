<?php

    namespace Library;
    class Gateways extends \SC_Library {
        
        function __construct() {
            parent::__construct();
            
            require_once('core/libraries/gateway_drivers/Gateway_Driver.php');
            
            $this->Drivers = new \stdClass;
            $this->initialize_drivers();
        }
    
        function initialize_drivers() {
            $drivers = $this->SC->Config->get_setting('paymentmethods');
            
            $drivers = explode('|',$drivers);
            
            $success = TRUE;
            foreach ($drivers as &$driver) {
                
                $success = min(call_user_func_array(array($this,'load_driver'),explode(',',$driver)),$success);
            }
            
            return $success;
        }    
        
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
