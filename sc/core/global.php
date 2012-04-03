<?php

  //------------
  //Simplecart Global Functions
  //------------
  
  function rename_key(&$array,$oldkey,$newkey=NULL) {
  
    if (is_array($oldkey)) {
      $newkeys = $oldkey;
      unset($oldkey);      
      foreach ($newkeys as $oldkey => $newkey) {
        rename_key($array,$oldkey,$newkey);
      }
      return TRUE;
    }
  
    if (!isset($array[$newkey])) {
      $array[$newkey] = $array[$oldkey];
      unset($array[$oldkey]);
      
      return TRUE;
    }
    
    return FALSE;
  }
  
  function better_print_r($array) {
    echo str_replace("  ","&nbsp;",nl2br(htmlspecialchars(print_r($array,true))));
  }
  
  function better_printr($array) {
    better_print_r($array);
  }
  
  function db_return($obj,$return_cols) {
    if ($obj) {    
        if ($return_cols=='*' or strpos($return_cols,',')) {
          return $obj;
        } else {
          if (is_object($obj)) {
            return $obj->$return_cols;
          } elseif (is_array($obj)) {
            return $obj[$return_cols];
          }
        }
    }
    
    return FALSE;
  }
  
  function required_libraries($library) {
    $class_vars = get_class_vars($library);        
    return (isset($class_vars['required_libraries'])) ? $class_vars['required_libraries'] : array();    
  }
  
  function site_url($addl='') {
    
    global $CONFIG;
    
    $addl = ltrim($addl,'/');
    
    return "http".(isset($_SERVER['https']) && $_SERVER['https'] != 'off')."://{$_SERVER['SERVER_NAME']}{$CONFIG['URL']}/$addl";
  
  }
  
  function sc_location($addl='') {
    
    global $CONFIG;
    
    $addl = ltrim($addl,'/');    
    return site_url("{$CONFIG['SC_LOCATION']}/$addl");
  }
  
  function sc_asset($type,$name) {
  
    global $CONFIG;
    global $SC;
    
    if ($type != 'img' && $type != 'button') {
      $name = preg_replace("/\.$type$/",'',$name).".$type";
    }
    
    if ($type == 'button') {      
      $name = $SC->Config->get_setting('buttons_folder')."/$name.gif";
    }
    
    return sc_location("assets/$type/$name");
  }
  
  function sc_ajax($name,$args=FALSE) {
  
    global $CONFIG;
    
    if ($args !== FALSE) {
      if (is_array($args)) {
        $args = implode('/',$args);
      }
      $args = '/'.ltrim($args,'/');
    }
    
    return sc_location("ajax/$name$args");
  }
  
    function is_multi($array) {
        return (count($array) != count($array, 1));
    }
