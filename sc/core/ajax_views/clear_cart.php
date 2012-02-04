<?php 
  namespace View;
  function clear_cart() {
    global $SC;
    $SC->load_library(array('Cart','Session'));
    
    $SC->Cart->clear_cart($SC->Session->transaction);
    
    $SC->load_ajax_view('view_cart');
    
  }
?>
