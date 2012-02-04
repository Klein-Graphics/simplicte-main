<?php

  //Simple cart configuration
  
  //Database
  //-----------
  $CONFIG['DATABASE'] = array(
    'development' => 'mysql://root:@localhost/simplecart2',
    'production' => ''
  );
  
  /*Location
   * If these values are not set, SC will attempt to figure them out 
   */
  
  $CONFIG['SC_LOCATION'] = ''; //The name of the folder containing Simplecart
  /**
   * The url, starting at server root and up to the root of your website. 
   * If you don't enter it, or it's wrong, init.php will attempt to fix it
   * and replace its value here, but please fill it in. Every time the code to 
   * determine this value is executed, a kitten is killed. Please think of the 
   * kittens.
   */ 
  $CONFIG['URL'] = '/dockets/simplecart2';
  
  //Error Handling and Debugging
  //---------------
  $CONFIG['SHOW_ERRORS'] = TRUE;
  $CONFIG['LOG_ERRORS'] = FALSE;
  $CONFIG['LOG_FILE_LOCATION'] = '../error.log';
  $CONFIG['SHOW_ERROR_CONTEXT'] = FALSE;
  
  $CONFIG['DUMP_SESSION'] = TRUE;
  
  $CONFIG['ERROR_LEVELS'] = array(
    'Error',
    'Warning',
    'Parse',
    'Notice',
    'Core_Error',
    'Core_Warning',
    'Compile_Error',
    'Compile_Warning',
    'User_Error',
    'User_Warning',
    'User_Notice',
    'Strict',
    'Recoverable_Error',
    'Deprecated',
    'User_Deprecated',
    'All'
  );
  
