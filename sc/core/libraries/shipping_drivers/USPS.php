<?php
/**
 * USPS Shipping Rate
 *
 * @package Checkout\Shipping Drivers
 */
namespace Shipping_Driver;

/**
 * USPS Shipping Rate Driver
 *
 * @package Checkout\Shipping Drivers
 */ 
class USPS extends \SC_Shipping_Driver {
    
    /**
     * The Human Readable Name
     */
    public $name = 'USPS';
    
    /**
     * Shipping codes
     */
    public $shipping_codes = array(
            'FIRST CLASS' => 'First Class',
            'FIRST CLASS COMMERCIAL' => 'First Class Commerical',
            'FIRST CLASS COMMERCIAL HFP COMMERCIAL' => 'First Class Hold For Pickup Commerical',
            'PRIORITY' => 'Priority',
            'PRIORITY COMMERICAL' => 'Priority Commercial',
            'PRIORITY HFP COMMERICAL' => 'Priority Hold For Pickup Commerical',
            'EXPRESS' => 'Express',
            'EXPRESS COMMERCIAL' => 'Express Commerical',
            'EXPRESS SH' => 'Express SH',
            'EXPRESS SH COMMERCIAL' => 'Express SH Commerical',
            'EXPRESS COMMERCIAL HFP COMMERCIAL' => 'First Class Hold For Pickup Commerical',
            'EXPRESS HFP' => 'Express Hold For Pickup',
            'EXPRESS HFP COMMERICAL' => 'Express Hold For Pickup Commerical',
            'PARCEL' => 'Parcel',
            'MEDIA' => 'Media',
            'LIBRARY' => 'Library',
            'ALL' => 'All',
            'ONLINE' => 'Online'            
    ); 
        
    function api_call($data) {
        $this->xmlreq = new \DOMDocument();
        $curl = new \Curl();
        
        $root = $this->xmlreq->appendChild(
                    $this->xmlreq->createElement("RateV4Request"));
        
        $root->appendChild(
            $this->xmlreq->createAttribute('USERID'))->appendChild(
                $this->xmlreq->createTextNode($this->SC->Config->get_setting('uspsuserid')));                              
        
        $root = $this->proccess_data($data,$root);
        
        echo $curl->get(
            'http://testing.shippingapis.com/ShippingAPITest.dll',
            array(
                'API' => 'RateV4',
                'XML' => $this->xmlreq->saveXML()
            ),
            $info
        );
        
        
               
    }
    
    private function proccess_data($nodes,$root) {                            
        foreach($nodes as $node_name => $node) {
            $attach_node = $this->xmlreq->createElement($node_name,$node['data']); 
            if ($node['attributes']) {
                foreach ($node['attributes'] as $attr_name => $attr) {
                    $attach_node->appendChild(
                        $this->xmlreq->createAttribute($attr_name))->appendChild(
                            $this->xmlreq->createTextNode($attr));    
                }
            }
            
            if ($node['children']) {
                $attach_node = $this->proccess_data($node['children'],$attach_node);          
            }
            
            $root->appendChild($attach_node);                                                                                     
        }
        
        return $root;
    }  

    /**
     * Get Rate
     *
     * Returns the shipping rate of a cart with specified parameters
     *
     * @return float
     *
     * @param string $from The from postal code
     * @param string $to The to postal code
     * @param string $service The service with which to base the rate on
     * @param float $weight The weight of the items which are being shipped in lbs
     * @param float $length The length of the items which are being shipped in inches
     * @param float $width The width of the items which are being shipping in inches
     * @param float $height The height of the items which are bieng shipping in inches
     
     */
    function get_rate($from,$to,$service,$weight,$length=0,$width=0,$height=0) {
        if (!$weight) {
            return 0;
        }
        return $this->upsRate->getRate($from,$to,$service,$length,$width,$height,$weight);
    }
    
    /**
     * Get Rate From Cart
     *
     * Returns the shipping rate of a specific cart
     *
     * @return float
     *
     * @param string $from The from postal code
     * @param string $to The to postal code
     * @param string $service The service with which to base the rate on
     * @param int|string|array $cart A valid cart
     */
    function get_rate_from_cart($from,$to,$service,$cart) {
        $this->SC->load_library('Cart');
        $weight = $this->SC->Cart->weigh_cart($cart);                                        
        
        $shipping_rate = $this->get_rate($from,$to,$service,$weight);                
        
        return $shipping_rate;
    }   
    
    /**
     * Get Rate From Transaction
     *
     * Returns the shipping rate of a specified transaction
     *
     * @return float
     *
     * @param int $transaction The transaction ID
     * @param string $service The service with which to base the rate on
     */
    function get_rate_from_transcation($transaction,$service) {
        $this->SC->load_library('Transaction');
        
        $transaction = $this->SC->Transaction->get_transaction($transaction);        
        $rate = $this->get_rate_from_cart($this->SC->Config->get_setting('storeZipcode'),$transaction->ship_postalcode,$service,$transaction->items);
        
        return $rate;
        
    }
}


