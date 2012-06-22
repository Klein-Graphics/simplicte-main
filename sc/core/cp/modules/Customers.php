<?php

/** 
 * Customers Control Panel Module
 *
 * The module responsable for managing customers
 *
 * @package Control Panel
 */
namespace CP_Module;

/**
 * Customers Control Panel Module Class
 *
 * @package Control Panel
 */
class Customers extends \SC_CP_Module {
    public static $readable_name = "Customers";
    public static $icon = "user";
    
    function search_customers() {
        $this->SC->CP->load_view('customers/search');            
    }   
    
    function _do_search() {
    
    }    
    
    function __catch($customer) {
    
    }
}
