<?php

/**
 * Debugging file that clears the current session
 *
 * @package Debug
 */
 
session_start();
$key = $_GET['key'];

if ($key) {

unset($_SESSION[$key]);

} else {

session_destroy();

}

header( 'Location: '.$_SERVER['HTTP_REFERER'] ) ;
