<?php 
    namespace View;
    function logout() {
    global $SC;
    
    $SC->load_library('Session');
    
    $SC->Session->logout_customer();
   
    
    $back = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : site_url());

    header( 'Location: '.$back );
    
}
