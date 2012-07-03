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
    public $hidden_pages = array('do_search');        
    
    function search_customers() {
        $this->SC->CP->load_view('customers/search');            
    }   
    
    function do_search() {
        //Scrape out the non-address fields
        $sql['query'] = array();
        $sql['data'] = '';
        
        if (isset($_POST['custid']) && $_POST['custid']) {
            $sql['query'][] .= '`custid` LIKE ?';
            $sql['data'][] = "{$_POST['custid']}%";
            unset($_POST['custid']);
        }
        
        if (isset($_POST['email']) && $_POST['email']) {
            $sql['query'][] .= '`email` = ?';
            $sql['data'][] = $_POST['email'];
            unset($_POST['email']);
        }
        
        $glue = isset($_POST['match_all']) ? ' AND ' : ' OR ';
        $prefix = $_POST['search_type'];
        
        unset($_POST['match_all']);
        unset($_POST['search_type']);
        
        foreach ($_POST as $key => $field) {
            if ($field) {
                $sql['query'][] .= "`$prefix$key` = ?";
                $sql['data'][] = $field;            
            }
        }
        
        $sql['query'] = implode($glue,$sql['query']);
        
        $this->SC->CP->load_view('customers/view_results',array(
            'customers'=>\Model\Customer::all(array('conditions'=>array_merge((array) $sql['query'],$sql['data'])))
        ));      
        
    }    
    
    function __catch($customer) {
        $this->SC->CP->load_view('header');
        $this->SC->CP->load_view('menu',array(
            'this_module' => 'Transactions',
            'modules' => $this->SC->CP->get_modules()
        ));
        $this->SC->CP->load_view('module_menu',array(
            'methods' => $this->visible_methods
        ));
        $customer = \Model\Customer::find_by_custid($customer);
        $this->SC->CP->load_view('customers/single',array(
            'c' => $customer
        ));
        $this->SC->CP->load_view('footer');
    }
}
