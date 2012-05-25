<?php
/**
 * Control Panel Controller
 *
 * @package Control Panel
 * @todo
 */
 
require_once 'init.php';

$SC->load_library(array('CP','CP_Session'));

better_print_r($SC->CP->get_modules());
