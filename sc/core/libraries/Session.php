<?php
  
  namespace Library;

  class Session extends \SC_Library {
  
    public static $required_libraries = array('Customer','Transactions');
    
    function __construct() {
      parent::__construct();
    
      session_start();   
      
      $this->initialize();                 
            
    }   
    
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
    
    function initialize() {
        //Make sure everything has been setup
        $this->get_user();
        $this->get_customer();
        $this->get_open_transaction();     
    }
    
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
      
      //Guess not, make them a temporary one and create a database entry for them
      $_SESSION['customer_id'] = 'temp'.date('ymdHis').rand(1000,9999); 
      $_SESSION['user_id'] = $this->SC->Customer->create_customer($_SESSION['customer_id']);
      
      return $_SESSION['user_id'];                  
    }
    
    function get_customer() {
      if (isset($_SESSION['customer_id'])) {
        return $_SESSION['customer_id'];
      }
      
      //Do they have a user ID?
      if (isset($_SESSION['user_id'])) {
        $_SESSION['customer_id'] = $this->SC->Customer->get_customer($_SESSION['user_id'],'custid');
        return $_SESSION['customer_id'];
      } 
      
      //Guess not, make them a temporary one and create a database entry for them
      $_SESSION['customer_id'] = 'temp'.date('ymdHis').rand(1000,9999); 
      $_SESSION['user_id'] = $this->SC->Customer->create_customer($_SESSION['customer_id']);
      
      return $_SESSION['customer_id'];
    }
    
    function get_open_transaction() {
      //Do they have an open transaction?
      if (isset($_SESSION['transaction_id'])) {
        return $_SESSION['transaction_id'];
      }
      
      //No? Any pending transactions? Get the first one.
      if ($transaction = $this->SC->Transactions->get_transaction($this->get_customer(),'id','custid')) {
        $_SESSION['transaction_id'] = $transaction;
        return $transaction;
      }
      
      //Still no? Lets start a new one
      $_SESSION['transaction_id'] = $this->SC->Transactions->create_transaction($this->get_customer());
      
      return $_SESSION['transaction_id'];      
    }
    
    function has_account() {
        //Using the customer's current customer number. Do they have an account?
        $customer = $this->get_customer();
        
        if (strpos($customer,'temp')===0) {
            return FALSE;
        } else {
            return TRUE;
        }        
        
    }
    
    function login_customer($post_data) {
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

        /*
         * Alrighty then, we're good, if this cart exists and is empty dump it and run get_open_transaction
         */
        $_SESSION['customer_id'] = $customer->custid;
        $_SESSION['user_id'] = $customer->id;
        
        if (!isset($post_data['sc_remember_me'])) {
            session_set_cookie_params(0);
            session_regenerate_id();
        }
        
        if ($this->transaction) {
            $cart = $this->SC->Transactions->get_transaction($this->transaction,'items');
            if (!$cart) {
                $this->SC->Transactions->delete_transaction($this->transaction);
                unset($_SESSION['transaction_id']);
            }
        }
               
        $this->SC->Transactions->associate_customer($this->get_open_transaction(),$customer->id); 
        
        return true;                                               
        
    }
    
    function logout_customer() {        
        session_destroy();
        setcookie('PHPSESSID','');       
    }
    
  }
