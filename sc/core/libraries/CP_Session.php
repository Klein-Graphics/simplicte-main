<?php

/**
 * Control Panel Session Library
 * 
 * Handles the session for the control panel, which must be kept seperate from the
 * session for the store itself
 *
 * @package Control Panel
 */
namespace Library;

/**
 * Control Panel Session Library Class
 *
 * @package Control Panel
 */ 


class CP_Session extends \SC_Library {

    function __construct() {
        global $CONFIG;
        parent::__construct();

        session_name("PHPSESSID_CP");        
        $cookie_params = session_get_cookie_params();
        session_set_cookie_params($cookie_params['lifetime'],$CONFIG['URL'].'/sc/cp');
               
        session_start();        
    } 
    
    /**
     * Get Magic Method
     *
     * Allows the object to return session variables as object properties
     *
     * @return mixed
     *
     * @param string $name The name of the variable
     */
    function __get($name) {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }        
        
        //Complain
        $trace = debug_backtrace();
        trigger_error("Invalid session variable: <strong>$name</strong>, called at {$trace[0]['line']} in {$trace[0]['file']}",E_USER_WARNING);
    }
    
    /**
     * Logged In
     *
     * Checks to see if the current user is logged in
     *
     * @return bool|int Returns false if not, the ID if they are
     */     
    function logged_in() {
         return (isset($_SESSION['user_id']));
    }

}
