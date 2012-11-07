<?php
/**
 * Session Library
 *
 * Handles the customer's PHP session, setting an getting details about who's
 * logged-in
 *
 * @package Session
 */
namespace Library;

/**
 * Session library class
 *
 * @package Session
 */
class Session extends \SC_Library {

    /**
     * This class requires the Customer and Transactions libraries
     */
    public static $required_libraries = array('Customer','Transactions');

    /**
     * Construct
     *
     * Starts the PHP session and initializes the class
     *
     * @return null
     */
    function __construct() {
        global $CONFIG;
        parent::__construct();

        
        $cookie_params = session_get_cookie_params();
        session_set_cookie_params($cookie_params['lifetime'],'/'.ltrim($CONFIG['URL'],'/'));
        if (isset($_GET[session_name()])) {
            session_id($_GET[session_name()]);
        }
        
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
        
        //An alias check        
        if (isset($_SESSION[$name.'_id'])) {
            return $_SESSION[$name.'_id'];
        }               
        
        //Complain
        $trace = debug_backtrace();
        trigger_error("Invalid session variable: <strong>$name</strong>, called at {$trace[0]['line']} in {$trace[0]['file']}",E_USER_WARNING);
    }

    /**
     * Initialize
     *
     * Initialize the various required session variables
     *
     * @return null
     */
    function initialize() {
        //Make sure everything has been setup
        $this->get_user();
        $this->get_customer();
        $this->get_open_transaction();     
    }

    /**
     * Force Get User
     *
     * Gets the current customer's DB id, creating a DB record if one doesn't exist
     *
     * @return int
     */
    function force_get_user() {
        //Do they have a user id?
        if ($user = $this->get_user()) {
            return $user;
        }

        //Guess not, make them a temporary one and create a database entry for them
        $_SESSION['customer_id'] = 'temp'.date('ymdHis').rand(1000,9999); 
        $_SESSION['user_id'] = $this->SC->Customer->create_customer($_SESSION['customer_id']);

        return $_SESSION['user_id'];                  
    }   
    
    /**
     * Get User
     *
     * Gets the current customer'd DB id, returning false if one doesn't exist
     *
     * @return int|bool
     */
    function get_user() {
        //Have they been designated a user id?
        if (isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }

        //Do they have a customer ID?
        if (isset($_SESSION['customer_id'])) {
            $_SESSION['user_id'] = $this->SC->Customer->get_customer($_SESSION['customer_id'],'id','custid');
            return $_SESSION['user_id'];
        }  
        
        //Guess not
        return FALSE;        
    } 

    /**
     * Force Get Customer
     *
     * Gets the current customer's human-readable customer number, creating a DB record
     * if one doesn't exist
     *
     * @return string
     */
    function force_get_customer() {
        //Do they have a customer number?
        if ($customer = $this->get_customer()){
            return $customer;
        }   

        //Guess not, make them a temporary one and create a database entry for them
        $_SESSION['customer_id'] = 'temp'.date('ymdHis').rand(1000,9999); 
        $_SESSION['user_id'] = $this->SC->Customer->create_customer($_SESSION['customer_id']);

        return $_SESSION['customer_id'];
    }
    
    /**
     *  Get Customer
     *
     * Gets the current customer's human-readable customer number, return false if
     * one dosn't exist
     *
     * @return string
     */
    function get_customer() {
        if (isset($_SESSION['customer_id'])) {
            return $_SESSION['customer_id'];
        }

        //Do they have a user ID?
        if (isset($_SESSION['user_id'])) {
            $_SESSION['customer_id'] = $this->SC->Customer->get_customer($_SESSION['user_id'],'custid');
            return $_SESSION['customer_id'];
        } 
        
        //Guess not
        return FALSE;    
    }

    /**
     * Force Get Open Transaction
     *
     * Gets the current customer's transaction DB id, creating a transaction if one
     * doesn't exist
     *
     * @return int
     */
    function force_get_open_transaction() {
        //Do they have an open transaction
        if ($transaction = $this->get_open_transaction()) {
            return $transaction;
        }

        //No? Lets start a new one
        $_SESSION['transaction_id'] = $this->SC->Transactions->create_transaction($this->force_get_customer());

        $this->SC->Transactions->associate_customer($_SESSION['transaction_id'],$this->force_get_user());

        return $_SESSION['transaction_id'];      
    }
    
    /**
     * Get Open Transaction
     *
     * Gets the current customer's transaction DB id, returning false if one doesn't exist
     *
     * @return int
     */
    function get_open_transaction() {
        //Do they have an open transaction?
        if (isset($_SESSION['transaction_id'])) {
            //Does this transaction actually exist?
            if (\Model\Transaction::count($_SESSION['transaction_id'])) {
                return $_SESSION['transaction_id'];
            } else {
                //Kill it
                unset($_SESSION['transaction_id']);
            }
        } 

        //No? Any pending or unassociated opened transactions? Get the first one.
        $customer = $this->get_customer();
        if ($customer && $transaction = \Model\Transaction::first(array(
                'conditions' => array(
                        'custid = ? AND status in(?) AND items != FALSE',
                        $customer,array('pending', 'opened')
                )        
           )))  {            
            $_SESSION['transaction_id'] = $transaction->id;
            return $transaction->id;
        }
        
        //Guess not
        return FALSE;
    }

    /**
     * New Transaction
     *
     * Creates and associates a new blank transaction for the customer, keeping
     * the old one in the database
     *
     * @return int
     */     
    function new_transaction() {
        $_SESSION['transaction_id'] = $this->SC->Transactions->create_transaction($this->force_get_customer());

        $this->SC->Transactions->associate_customer($_SESSION['transaction_id'],$this->get_user());

        return $_SESSION['transaction_id']; 
    }

    /**
     * Customer Has Account?
     *
     * Checks to see if the customer has an account, or is using a a temporary ID
     *
     * @return bool
     */
    function has_account() {
        //Using the customer's current customer number, do they have an account?
        $customer = $this->get_customer();
        if (!$customer || $customer && strpos($customer,'temp')===0) {            
            return FALSE;
        } else {
            return TRUE;
        }        
        
    }

    /**
     * Login Customer
     *
     * Validates and logs in a customer, associating a cart with them, or them with a cart
     *
     * @return bool
     *
     * @param array $post_data The data posted from the login form, should contain 'sc_login_email',
     * 'sc_login_password', and 'sc_login_remember_me.' Password should be MD5 hashed
     */
    function login_customer($post_data) {
        global $CONFIG;
        
        $customer = \Model\Customer::first(array(
            'conditions' => array('email = ? AND passwordmd5 != ""', $post_data['sc_login_email']),
            'select' => 'passwordmd5,custid,id'
        ));
        
        if (!$customer) {
            return FALSE;
        }       
        
        if ($post_data['sc_login_password'] !== $customer->passwordmd5) {
            return FALSE;
        }   

        /**
         * Alrighty then, we're good, if this cart exists and is empty dump it and run get_open_transaction
         */
        $_SESSION['customer_id'] = $customer->custid;
        $_SESSION['user_id'] = $customer->id;
        
        if (isset($post_data['sc_remember_me'])) {
            session_set_cookie_params(ONE_HUNDRED_YEARS,'/'.ltrim($CONFIG['URL'],'/'));
            session_regenerate_id();
        }
        
        if ($this->get_open_transaction()) {
            $cart = $this->SC->Transactions->get_transaction($this->transaction,'items');
            if (!$cart) {
                $this->SC->Transactions->delete_transaction($this->transaction);
                unset($_SESSION['transaction_id']);
            }
        }
               
        $this->SC->Transactions->associate_customer($this->get_open_transaction(),$customer->id); 
        
        return TRUE;                                               
        
    }

    /**
     * Logout Customer
     * 
     * Logs out the customer, destroying the session
     *
     * @return null
     */
    function logout_customer() {        
        session_destroy();
        setcookie('PHPSESSID','');       
    }

}
