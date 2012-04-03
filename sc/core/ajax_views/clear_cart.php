<?php 
    global $SC;
    $SC->load_library(array('Cart','Session'));
    
    $SC->Cart->clear_cart($SC->Session->get_open_transaction());
    
    $SC->load_ajax_view('view_cart');
