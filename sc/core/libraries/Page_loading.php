<?php

  //----------------------
  //Page Loading Functions
  //----------------------
  //
  // These functions handle the loading of simplecart onto the user's page
  //
  
  namespace Library;
  
  class Page_loading extends \SC_Library {
  
    function __construct() {
      parent::__construct();
      $this->js = array();
      
      $this->load_drivers();
      
    }
  
    function replace_tag($input,$tag,$replacement=NULL,$function=NULL) {  
      //Find the tag in the page
      if (preg_match_all("/\[\[($tag(\|[^(\]\])]+)*)\]\]/i",$input,$matches)) {
        if ($replacement === NULL && $function !== NULL) {
          $replacement = array();
          foreach($matches[1] as $key => $match) {
            $match = explode('|',$match);
            
            $replacement[$key] = $function($match);
          }
        }
        
        return str_replace($matches[0],$replacement,$input);
      
      }
      
      return $input;
    
    }
    
    function replace_button($input,$tag,$button) {
      
      $readable = str_replace('_',' ',ucfirst($button));
    
      return $this->replace_tag($input,$tag,'<a href="'.sc_ajax($button).'" title="'.$readable.'" class="sc_'.$button.'"><img src="'.sc_asset('button',$button).'" alt="'.$readable.'" /></a>');
    }
    
    function replace_details($input) {
      
      //Replace "name" tags. This is for backwards compatibility of the depreciated tag [[name]]
      $input = $this->replace_tag($input,'name',NULL,function($args) {
        
        if (strpos($args[1],'i--') === FALSE) { //This is simply an db id
          $item = \Model\Item::find($args[1]);
          
          return $item->name;
        } else {
          $args[1] = substr($args[1],3);
          
          $item = \Model\Item::find('first',array('conditions' => array('number = ?',$args[1])));
          
          return $item->name;        
        }
        
        //If we get here something went super wrong, throw an error;
        trigger_error('Something went horrendously wrong. Abandon ship.',E_USER_ERROR);
        
      });
      
      //Replace "desc" tags. This is for backwards compatibility of the depreciated tag [[desc]]
      $input = $this->replace_tag($input,'desc',NULL,function($args) {
      
        if (strpos($args[1],'i--') === FALSE) { //This is simply an db id
          $item = \Model\Item::find($args[1]);
          
          return $item->description;
        } else {
          $args[1] = substr($args[1],3);
          
          $item = \Model\Item::find('first',array('conditions' => array('number = ?',$args[1])));
          
          return $item->description;        
        }  
        
        //If we get here something went super wrong, throw an error;
        trigger_error('Something went horrendously wrong. Abandon ship.',E_USER_ERROR);
      
      });
      
      //Replace "detail" tags.    
      $input = $this->replace_tag($input,'detail',NULL,function($args) {
      
        if (strpos($args[2],'i--') === FALSE) { //This is simply an db id
          $item = \Model\Item::find($args[2]);
          
          return $item->$args[1];
        } else {
          $args[2] = substr($args[2],3);
          
          $item = \Model\Item::find('first',array('conditions' => array('number = ?',$args[2])));
          
          return $item->$args[1];        
        } 
        
        //If we get here something went super wrong, throw an error;
        trigger_error('Something went horrendously wrong. Abandon ship.',E_USER_ERROR);
         
      });
      
      return $input;
    
    } 
    
    function load_drivers() {
      $cart_driver = $this->SC->Config->get_setting('cart_driver');
      $checkout_driver = $this->SC->Config->get_setting('checkout_driver');      
      
      include 'core/libraries/cart_drivers/Cart_Driver.php';
      include 'core/libraries/checkout_drivers/Checkout_Driver.php';
      include "core/libraries/cart_drivers/$cart_driver.php";
      include "core/libraries/checkout_drivers/$checkout_driver.php";
      
      $namespaced_cart_driver = '\Cart_Driver\\'.$cart_driver;
      $namespaced_checkout_driver = '\Checkout_Driver\\'.$checkout_driver;
      
      $this->cart_driver = new $namespaced_cart_driver;
      $this->checkout_driver = new $namespaced_checkout_driver;      
            
    }
    
    function add_javascript($src,$script='') {
      if (is_array($src)) {
        $this->js[] = $src;
        return TRUE;
      }
      
      $this->js[] = array($src,$script);
    }  
    
    function insert_javascript($input) {
      $output = '';
      
      if ( ! count($this->js)) {
        return $input;
      }
      
      foreach ($this->js as $js) {
        $output .= "<script type='text/javascript' ".(($js[0])?"src='{$js[0]}'":"").">{$js[1]}</script>".PHP_EOL;
      }
      $output .= '</head>'.PHP_EOL;
      
      
      return str_ireplace('</head>',$output,$input);
       
    }    
    
  }
