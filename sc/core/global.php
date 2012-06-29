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
    
    return sc_location("core/assets/$type/$name");
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
     * SC CP
     *
     * A helper to generate a url for a CP request
     *
     * @return string
     *
     * @param string $name The name of the request
     * @param string|string $args An array or string of arguments to pass to the script     
     */
    function sc_cp($name,$args=FALSE) {

        global $CONFIG;        

        if ($args !== FALSE) {
          if (is_array($args)) {
            $args = implode('/',$args);
          }
          $args = '/'.ltrim($args,'/');
        }
              
        if ($append = $name.$args) {
            $append = trim($name.$args,'/').'/';
        }
        
        

        return sc_location("cp/$append");
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
    
    /**
     * Truncate String
     *
     * Truncates a string
     *
     * @return string
     *
     * @param string $string The string to truncate
     * @param int $limit The amount of characters to reduce to
     * @param string $break Where to break the strings at
     * @param string $pad What to pad the string with
     *
     * @copyright Original PHP code by Chirp Internet: www.chirp.com.au
     */
    function str_trunc($string, $limit, $break=" ", $pad="...") {
        $limit -= strlen($pad);
        // return with no change if string is shorter than $limit
        if(strlen($string) <= $limit) return $string;

        $string = substr($string, 0, $limit);
        if(false !== ($breakpoint = strrpos($string, $break))) {
            $string = substr($string, 0, $breakpoint);
        }

        return $string . $pad;
    }    
    
    /**
     * HTML Truncate String
     *
     * Truncates a string ignoring html
     *
     * @return string
     *
     * @param string $string The string to truncate
     * @param int $limit The amount of characters to reduce to
     * @param string $break Where to break the strings at
     * @param string $pad What to pad the string with
     *
     * @copyright Original PHP code by Chirp Internet: www.chirp.com.au
     */
     function html_str_trunc($string, $limit, $break=" ", $pad="...") {
        $string_to_count = strip_tags($string);
        // return with no change if string is shorter than $limit
        if(strlen($string_to_count) <= $limit) return $string;
        
        $count = 0;
        $do_count = TRUE;
        $output_string = '';
        foreach (str_split($string) as $key => $char) {
            //Add the character
            $output_string .= $char;                        
            
            if ($char == '<') {    
                $do_count = FALSE;
            }
            
            $count += $do_count;
            
            if ($char == '>') {
                $do_count = TRUE;
            }            
            
            if ($count == $limit) {
                break;
            }            
        } 

        if(false !== ($breakpoint = strrpos($output_string, $break))) {
            $output_string = substr($output_string, 0, $breakpoint);
        }

        return $output_string . $pad;
    }  
    
    /** 
     * Is Ajax
     *
     * @copyright snipplr
     */    
    function is_ajax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
    }
    
    /**
     *
     */
    function encrypt($str){
        global $CONFIG;
        $key = $CONFIG['SEED'];
        $result='';
        for($i=0; $i<strlen($str); $i++) {
            $char = substr($str, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)+ord($keychar));
            $result.=$char;
        }
        return base64_encode($result);
    }


    function decrypt($str){
        global $CONFIG;
        $str = base64_decode($str);
        $result = '';
        $key = $CONFIG['SEED'];
        for($i=0; $i<strlen($str); $i++) {
            $char = substr($str, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)-ord($keychar));
            $result.=$char;
        }
        return $result;
    }
    
    /**
     * Get post
     */
    function get_post($key,$default=FALSE) {
        return isset($_POST[$key]) ?
                $_POST[$key] : $default;
    }
    
    /**
     * Flatten array
     *
     * Does what it says on the tin
     */
    function array_flatten(array $array) {
        $return = array();
        array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
        return $return;
    }
    
    /** 
     * Call extention hook
     * 
     * Calls extention hooks if any exists    
     *
     * @param string[] $hook
     *
     * 
     */
    function call_hook($hook,$data=array()) {
        global $SC;                
        
        //Call any possible pre-ajax controller hooks
        $qual_hook = '$SC->hooks';                
        
        foreach ($hook as $level) {
            $qual_hook .= "['$level']";
        }                
        
        if (eval("return isset($qual_hook);")) {                
            eval("call_user_func_array($qual_hook,\$data);");
        }   
    }    
