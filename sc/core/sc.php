<?php

  // Main simplecart class
  class SC {
  
    function __construct() {
      $this->loaded_libraries = array();
    }
    
    function load_library($libraries) {
      if (!is_array($libraries)) {
        $libraries = array($libraries);
      }
      
      foreach ($libraries as $library) {
      
        if (array_search($library,$this->loaded_libraries) === FALSE) {
        
          $library = str_replace('.php','',ucfirst($library));
        
          require_once 'core/libraries/'.$library.'.php';
          
          $namespaced_library = '\Library\\'.$library;                       
          
          $this->load_library(required_libraries($namespaced_library));                 
                               
          $this->$library = new $namespaced_library;
          $this->loaded_libraries[] = $library;
          
        }        
      }      
    }
    
    function load_ajax_view($view,$data=array()) {                        
      if ($view) {
        
        //normalize view name
        $view = strtolower($view);
        
        if (file_exists("core/ajax_views/$view.php")) {
          require_once "core/ajax_views/$view.php";    
        } else if (file_exists("core/ajax_views/$view/index.php")) {
          require_once "core/ajax_views/$view/index.php"; 
        } else {
          header('HTTP/1.0 404 Not Found');
          echo('<h1>404</h1>Page not found');
          return false;
        }   
        
        if (function_exists("\View\\$view")) {
          call_user_func_array("\View\\$view",$data);
        }
        
        return true;
      }
    
    }       
  }
  
  //Main simplecart library class
  class SC_Library {
    
    function __construct() {
      global $SC;
      
      $this->SC = $SC;      
      
    }  
  }
