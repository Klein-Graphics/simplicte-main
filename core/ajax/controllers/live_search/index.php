<?php
/**
 * Live search Controller
 *
 * This is the initial livesearch script that loads a specified live search
 * 
 * @package Checkout
 */        
 
$data = $this->URI->get_data();

$this->load_ajax('live_search/'.array_shift($data),$data);
    
