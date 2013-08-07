<?php

  /**
   * AJAX request script
   *
   * This is the main script that loads specific ajax pseudo-views
   *
   * @package Ajax
   */ 
  
  require_once 'init.php';
  $CONFIG['DUMP_SESSION'] = FALSE;  

  //Load requested view and pass data to it
  $SC->load_ajax($SC->URI->get_request(),$SC->URI->get_data());
  
