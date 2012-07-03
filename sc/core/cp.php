<?php
/**
 * Control Panel Controller
 *
 * @package Control Panel
 */
 
define('SIMPLECART_IS_IN_CP',1); 
 
require_once 'init.php';

$SC->load_library(array('CP','CP_Session','URI'));

$module = ucfirst($SC->URI->get_request());
$data = $SC->URI->get_data();
$method = array_shift($data);

//Make sure there's a user logged in
if (!$SC->CP_Session->logged_in() && $module != 'Do_login' && $module != 'Do_logout') {
    $SC->CP->load_view('header');
    $SC->CP->load_view('login');
    exit;
}

//Maybe they're trying to login
if ($module == 'Do_login') {
    $CONFIG['DUMP_SESSION'] = FALSE;
    if ($SC->CP_Session->log_in($_POST)) {
        exit(json_encode(array('ACK'=>1)));
    } else {
        exit(json_encode(array('ACK'=>0)));
    }
}

//Maybe they're trying to logout
if ($module == 'Do_logout') {
    $SC->CP_Session->log_out();
    header('Location: '.sc_cp());
}

//Guess they're good, go home...
if (!$module || $module == 'Home') {
    $SC->CP->load_view('header');
    
    $SC->CP->load_view('home',array(
        'modules' => $SC->CP->get_modules(),
        'store_name' => $SC->Config->get_setting('storename')
    ));
    
    $SC->CP->load_view('footer');
    
    exit;
}

//...or load the requested module
include_once('core/cp/SC_CP_Module.php');
if ($SC->CP->module_exists($module)) {
    $ns_module = "CP_Module\\$module";
    $MOD = new $ns_module();    
    
    if (!$method) {
        $method = 'index';
    }
    
    //Make sure the method exists     
    if (method_exists($MOD,$method)) {
        $SC->CP->load_view('header');
        $SC->CP->load_view('menu',array(
            'this_module' => $module,
            'modules' => $SC->CP->get_modules()
        ));
        $MOD->_load_module_menu();
        call_user_func_array(array($MOD,$method),$data);
    } else if (method_exists($MOD,'_'.$method))  { //Private call
        $CONFIG['DUMP_SESSION'] = FALSE;
        call_user_func_array(array($MOD,'_'.$method),$data);
        exit;      
    } else if (method_exists($MOD,'__catch')) {
        call_user_func_Array(array($MOD,'__catch'),array($method)+$data);
        exit;
    }
    
}
$SC->CP->load_view('footer');

