<?php

/**
 * Simplecart2 In-Parsing File
 *
 * This file must be included in any page where SC's display parsing needs to 
 * happen. It must be included *before* any output from the page. 
 *
 * @package Parsing
 *
 * @see Core/page_out.php
 */
try {
$page_cwd = getcwd();
    require 'init.php'; //Initialize Simplecart
    $sc_cwd = getcwd();

    $SC->load_library('Session'); //Get sessions ready
    $SC->load_library('Page_loading'); //Pageloading library
    } catch (ActiveRecord\DatabaseException $e) {
    //DB is broken, tell simplecart to abandon ship.
    define(SC_STOP,true);
     
}

if (!defined('SC_STOP')) {
    define('SC_STOP',false);
}

//Begin output buffering
ob_start();

chdir($page_cwd);

    
