<?php
/**
 * Initilization script
 *
 * @package Core
 */
$start_time = microtime(true);

//Make sure that this script's cwd is SC's root
$sc_dir = dirname(__DIR__);
chdir($sc_dir);

//load constants
require 'const.php';

//Load config file.
require 'config.php';

if (! $CONFIG['SC_LOCATION']) {
  $CONFIG['SC_LOCATION'] = substr($sc_dir,strrpos($sc_dir,'/')+1);        
}

if (! $CONFIG['URL'] || ($CONFIG['URL'] && !file_exists("{$_SERVER['DOCUMENT_ROOT']}/{$CONFIG['URL']}/{$CONFIG['SC_LOCATION']}/config.php"))) {  
  
  //Do something complicated to get the url:
  
  //Drop a marker file in into our main folder, and drop a unique value in it
  $marker_name = ".scmarker-".time();
  $r_marker_file = fopen('../'.$marker_name,'w');
  fclose($r_marker_file);
  
  /**
   * Recursive Search
   *
   * Starting at webroot, recursively read up the file system until the marker is found
   *
   * @return bool|string
   *
   * @param string $directory The directory to enter
   */
  function recursive_search($directory) {
    global $marker_name;
    global $cur_dir;   
      
    $cur_dir = $cur_dir.$directory.'/';
    
    $inside = scandir($cur_dir);    
    foreach ($inside as $this_file) {
      if ($this_file == '.' || $this_file == '..') {
        continue;
      }
      if (is_dir($cur_dir.$this_file)) {
        if ($found_marker = recursive_search($this_file)) {
          return $found_marker;
        }
        continue;
      }
            
      if ($this_file == $marker_name) {
        unlink($cur_dir.$marker_name);
        return $cur_dir.$marker_name;
        
      }
    }
    
    $cur_dir = dirname($cur_dir).'/';    
    
    return False;  
    
  }
  
  $site_url = recursive_search($_SERVER['DOCUMENT_ROOT']) or die('Couldn\'t find it. We\'ve failed, Jim');;
  $site_url = dirname($site_url);
  
  $site_url = str_replace($_SERVER['DOCUMENT_ROOT'],'',$site_url);
  
  $CONFIG['URL'] = $site_url;
  
  $config_file = file_get_contents('config.php');
  $config_file = preg_replace('/\$CONFIG\[(\'|")URL(\'|")\][^('.PHP_EOL.')]*'.PHP_EOL.'/',"\$CONFIG['URL'] = '$site_url';".PHP_EOL,$config_file);
  $r_config_file = fopen('config.php','w');
  fwrite($r_config_file,$config_file);
   
}




//---------------
// Error Handling
//---------------
$ERRORS = array();

require 'core/debug/error.php';

//-----------
// Database
//-----------
require 'core/includes/activerecord/ActiveRecord.php';

/**
 * Closure
 *
 * @ignore
 */
ActiveRecord\Config::initialize(function($cfg) {
  
  global $CONFIG;

  $cfg->set_model_directory('core/models');
  $cfg->set_connections($CONFIG['DATABASE']);
  
});

//Load Global Functions

require 'core/global.php';

//Initialize main SC classes

require 'core/sc.php';

$SC = new SC;

//Load config class

$SC->load_library('Config');

/*
 * Set time zone
 */
date_default_timezone_set($SC->Config->get_setting('timezone'));



