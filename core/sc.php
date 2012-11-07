<?php

/**
 * Main Simplecart2 Include File
 * 
 * This file is included in the init script and contains the
 * main SC class and the main SC Library class. 
 *
 * @author Will Leonardi
 * 
 * @package Core 
 * @used-by Core/init.php
 *
 */
 
/**
 * The main SC class.
 *
 * The class includes loading methods for libraries and the
 * ajax psuedo-views    
 *
 * @package Core 
 *
 */     

class SC {
  
    /**
     * Records the loaded libraries
     */
    public $loaded_libraries = array();
    
    /**
     * Extentions & hooks
     */
    public $extensions = array();
    public $hooks = array(
        'page-in' => array(),
        'page-out' => array(),
        'db' => array()     
    );
    
    
    /**
     *  Get Magic Method
     *
     *  Automatically loads queried libraries
     */
     
    function __get($name) {
        if (isset($this->$name)) {
            return $this->$name;
        }
        
        //Try to load a library
        if ($this->load_library($name)) {
            return $this->$name;
        } else {
            return FALSE;
        }
        
    }

    /**
     * Loads a Library into SC
     *
     * This function loads a library into the main SC Object. If the 
     * library is already loaded, it is skipped. It will also load
     * additional libraries specified by the "required_libraries" 
     * property of the library;
     *
     * @return null
     *
     * @param string|string[] $libraries Either a string containing the
     * library name or an array of strings containing multiple libraries
     * to load.
     * 
     */
    function load_library($libraries) {
        if (!is_array($libraries)) {
            $libraries = array($libraries);
        }
        
        $success = TRUE;

        foreach ($libraries as $library) {
            if (array_search($library,$this->loaded_libraries) === FALSE) {

                $library = str_replace('.php','',ucfirst($library));

                if ($success = min(file_exists('core/libraries/'.$library.'.php'),$success)) {
                    require_once 'core/libraries/'.$library.'.php';

                    $namespaced_library = '\Library\\'.$library;                       

                    $this->load_library(required_libraries($namespaced_library));                 
                                       
                    $this->$library = new $namespaced_library;
                    $this->loaded_libraries[] = $library;
                }              
            }        
        }      
        
        return $success;
    }

    /**
     * Loads an ajax call
     *
     * Each ajax call can consist of up to 2 files, a controller and  
     * a view. Just like any MVC the call should be split into logic
     * and display. 
     * 
     * After a controller is loaded, an optional function will be 
     * called that is named after the ajax call. This function
     * will be passed any arguments provided by the URI. This function
     * can return any data to be passed to the view as an assoc array,
     * with the key being the name of the variable to passed. The
     * function should belong to the "Ajax" namespace
     *
     * Call files must be all lowercase. If the call doesn't exist a 
     * 404 page will be generated.
     *
     * @return null
     *
     * @param string $call The name of the ajax call. No extention
     * @param array $data An array of data to be sequentially passed to
     * the ajax call's possible function call.
     *
     * @see Core/ajax.php
     * 
     */
    function load_ajax($call,$data=array()) {                        
      if ($call) {
        
        //normalize view name
        $call = strtolower($call);                      
        
        
        
        //Call any possible pre-ajax-controller hooks
        call_hook(array('ajax_controller',$call,'before'),$data);
        
        //Search for the controller
        $controller = true;
        if (file_exists("core/ajax/controllers/$call.php")) {
            require_once("core/ajax/controllers/$call.php");
        } else if (file_exists("core/ajax/controllers/$call/index.php")) { //Folder
            require_once "core/ajax/controllers/$call/index.php";
        } else {
            $controller = false;  
        } 
        
                       
        //Execute the possible ajax function
        if (function_exists("\Ajax\\$call")) {
            $return_vars = call_user_func_array("\Ajax\\$call",$data);

            if (is_array($return_vars)) {
                foreach ($return_vars as $name => $value) {
                    if (!isset($$name)) {
                        $$name = $value;
                    }
                }
            }
        }
        
        //Call any possible post-ajax-controller hooks     
        call_hook(array('ajax_controller',$call,'after'),$data); 
        
        //Call any possible pre-ajax-view hooks   
        call_hook(array('ajax_view',$call,'before'),$data);
        
        //Search for the view
        $view = true;
        if (file_exists("core/ajax/views/$call.php")) {
            require_once "core/ajax/views/$call.php";
        } else if (file_exists("core/ajax/views/$call/index.php")) {
            require_once "core/ajax/views/$call/index.php";
        } else {
            $view = false;
        }
                
        //Call any possible post-ajax-view hooks
        call_hook(array('ajax_view',$call,'after'),$data);
        
        //If there was no controller and no view, load a 404;
        if (!$controller && !$view) {        
            header('HTTP/1.0 404 Not Found');
            echo('<h1>404</h1>Page not found');
            return false;              
        }
        
        
        
      }    
    }               
}

/**
* Simplecart's Library class
*
* This class simply contains adds the $SC property to each library which
* allows access to the global $SC object
*
* @package Core 
*/
class SC_Library {
    
    /**
     * Adds the $SC property
     *
     * @return null
     */
    function __construct() {
      global $SC;
      
      $this->SC = $SC;      
      
    }  
}
