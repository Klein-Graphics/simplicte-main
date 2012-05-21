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
$page_cwd = getcwd();
require 'init.php'; //Initialize Simplecart
$sc_cwd = getcwd();

$SC->load_library('Session'); //Get sessions ready
$SC->load_library('Page_loading'); //Pageloading library

//Begin output buffering
@ob_flush();
ob_start();
chdir($page_cwd);

