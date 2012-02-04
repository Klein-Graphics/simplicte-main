<?php
  
  namespace Library;

  class Session extends \SC_Library {
  
    public static $required_libraries = array('Customer','Transactions');
    
    function __construct() {
      parent::__construct();
    
      session_start();    
      $this->user = $this->get_user();
      $this->customer = $this->get_customer();
      $this->transaction = $this->get_open_transaction();
      
      foreach ($_SESSION as $key => $value) {
        if (!isset($this->$key)) {
          $this->$key = $value;
        }
      }
            
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
      if ($transaction = $this->SC->Transactions->get_transaction($this->customer,'id','custid')) {
        $_SESSION['transaction_id'] = $transaction;
        return $transaction;
      }
      
      //Still no? Lets start a new one
      $_SESSION['transaction_id'] = $this->SC->Transactions->create_transaction($this->customer);
      
      return $_SESSION['transaction_id'];      
    }
    
  }
