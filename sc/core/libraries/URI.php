<?php
  
  namespace Library;

  class URI extends \SC_Library {
  
    function __construct() {

      parent::__construct();

      //Initialize query string      
      $this->initialize_query_string();
      
      
    }
    
    function initialize_query_string() {
    
        $query = $_SERVER['QUERY_STRING'];      
        $query = preg_replace('/^\//','',$query);            

        $query = explode('/',$query);

        $this->view = array_shift($query);

        $this->request = $query;

        return array($this->view,$this->request);
      
    }
    
    function get_view() {
      return $this->view;
    }
    
    function get_request($part=FALSE) {
      if ($part) {
        return $this->request[$part];
      } else {
        return $this->request;
      }
    }
  
  }
