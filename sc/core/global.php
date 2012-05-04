<?php

  /**
   * Simplecart Global Functions
   *
   * @package Core
   */
  
  /**
   * Rename Key
   *
   * Renames the keys of an array
   *
   * @return bool
   *
   * @param array $array A refrence to the array to rename
   * @param string|string $oldkey Either the old key to rename or an associative
   * array of old and new keys to rename
   * @param $newkey What to rename the key to
   */
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
  
  /**
   * Better Print R
   *
   * Does a print_r but processes it for displaying in HTML
   *
   * @return string
   *
   * @param mixed
   */
  function better_print_r($array) {
    echo str_replace("  ","&nbsp;",nl2br(htmlspecialchars(print_r($array,true))));
  }
  
  /**
   * Alias for better_print_r()
   *
   * @ignore
   */
  function better_printr($array) {
    better_print_r($array);
  }
  
  /**
   * DB Return
   *
   * Determines whether a DB call should return an array or a single string
   *
   * @return mixed
   *
   * @param array|object $obj The return value from the DB call
   * @param string $return_cols The request string
   */
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
  
  /**
   * Requred Libraries
   *
   * Gets the required libraries of a class
   *
   * @return array
   *
   * @param string $library The name of the library
   */
  function required_libraries($library) {
    $class_vars = get_class_vars($library);        
    return (isset($class_vars['required_libraries'])) ? $class_vars['required_libraries'] : array();    
  }
  
  /**
   * Site Url
   *
   * A helper to generate urls for the site
   *
   * @return string   
   *
   * @param string $addl What to add to the url   
   */
  function site_url($addl='') {
    
    global $CONFIG;
    
    $addl = ltrim($addl,'/');
    
    return "http".(isset($_SERVER['https']) && $_SERVER['https'] != 'off')."://{$_SERVER['SERVER_NAME']}{$CONFIG['URL']}/$addl";
  
  }
  
  /**
   * SC Location
   *
   * A helper to generate a url to the location of Simplecart
   *
   * @return string   
   *
   * @param string $addl What to add to the url   
   */
  function sc_location($addl='') {
    
    global $CONFIG;
    
    $addl = ltrim($addl,'/');    
    return site_url("{$CONFIG['SC_LOCATION']}/$addl");
  }
  
  /**
   * SC Asset
   *
   * A helper to generate a url to a specific asset
   *
   * @return string   
   *
   * @param string $type The type of the asset
   * @param string $name The name of the asset
   */
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
  
    /**
     * SC Ajax
     *
     * A helper to generate a url for an ajax request
     *
     * @return string
     *
     * @param string $name The name of the request
     * @param string|string[] $args An array or string of arguments to pass the script
     */
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
  
    /**
     * Is Multi?
     *
     * Checks to see if an array is multidimensional
     *
     * @return bool
     *
     * @param array $array The array to check
     */
    function is_multi($array) {
        return (count($array) != count($array, 1));
    }
    
    /**
     * Array To Lower
     *
     * Lowercases an array
     *
     * @return array
     * 
     * @param array $array The array to lowercase
     * @param int $round I don't know what this is for
     */
    function array_to_lower(array $array, $round = 0){ 
        return unserialize(strtolower(serialize($array))); 
    } 
