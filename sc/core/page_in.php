<?php

//Initialize Simplecart
//---------------------
require 'init.php';

$SC->load_library('Session'); //Get sessions ready
$SC->load_library('Page_loading'); //Pageloading library

//Begin output buffering
ob_flush();
ob_start();

