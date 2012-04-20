<?php

namespace Shipping_Driver;

class UPS extends \SC_Shipping_Driver {

    public $name = 'UPS';

    function __construct() {
        parent::__construct();
        
        require_once('core/includes/upsRate.php');

        list(
            $access_number,
            $username,
            $password,
            $account
        ) = $this->SC->Config->get_setting(array(
            'upsAccessKey',
            'upsUsername',
            'upsPassword',
            'upsAccountNum'
        ));
        
        $this->upsRate = new \upsRate($access_number,$username,$password,$account);
    }
    
    function get_shipping_codes() {
    
        return array(
            '01' => 'UPS Next Day Air',
            '02' => 'UPS Second Day Air',
            '03' => 'UPS Ground',
            '07' => 'UPS Worldwide Express',
            '08' => 'UPS Worldwide Expedited',
            '11' => 'UPS Standard',
            '12' => 'UPS Three-Day Select',
            '13' => 'UPS Next Day Air Saver',
            '14' => 'UPS Next Day Air Early AM',
            '54' => 'UPS Worldwide Express Plus',
            '59' => 'UPS Second Day Air AM',
            '65' => 'UPS Saver'
        );   
    }
    
    function get_rate($from,$to,$service,$weight,$length=0,$width=0,$height=0) {
        if (!$weight) {
            return 0;
        }
        return $this->upsRate->getRate($from,$to,$service,$length,$width,$height,$weight);
    }
    
    function get_rate_from_cart($from,$to,$service,$cart) {
        $this->SC->load_library('Cart');
        $weight = $this->SC->Cart->weigh_cart($cart);                                        
        
        $shipping_rate = $this->get_rate($from,$to,$service,$weight);                
        
        return $shipping_rate;
    }   
    
    function get_rate_from_transcation($transaction,$service) {
        $this->SC->load_library('Transaction');
        
        $transaction = $this->SC->Transaction->get_transaction($transaction);
        
        $rate = $this->get_rate_from_cart($this->SC->Config->get_setting('storeZipcode'),$transaction->ship_postalcode,$service,$transaction->items);
        
        return $rate;
        
    }
}


