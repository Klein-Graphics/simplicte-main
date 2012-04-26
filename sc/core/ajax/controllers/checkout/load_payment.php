<?php
/**
 * Loads the gateway driver's payment area
 *
 * @package checkout
 */    

$this->load_library('Gateways');

echo ($this->Config->get_setting('storelive'))
    ? $this->Gateways->Drivers->$_POST['method']->load()
    : $this->Gateways->Drivers->$_POST['method']->load_test();        
