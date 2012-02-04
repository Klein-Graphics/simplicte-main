<?php

  // This is the main script that loads specific ajax pseudo-views
  
  require_once 'init.php';
  $CONFIG['DUMP_SESSION'] = FALSE;
  
  $SC->load_library('URI');
  
  //Load requested view and pass data to it
  $SC->load_ajax_view($SC->URI->get_view(),$SC->URI->get_request());
  
