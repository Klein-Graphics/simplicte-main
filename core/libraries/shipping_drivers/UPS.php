<?php
/**
 * UPS Shipping Rate
 *
 * @package Checkout\Shipping Drivers
 */
namespace Shipping_Driver;

/**
 * UPS Shipping Rate Driver
 *
 * @package Checkout\Shipping Drivers
 */ 
class UPS extends \SC_Shipping_Driver {
    
    /**
     * The Human Readable Name
     */
    public static $name = 'UPS';
    
    /**
     * Shipping codes
     */
    public static $shipping_codes = array(
            '01' => 'UPS Next Day Air',
            '02' => 'UPS Second Day Air',
            '03' => 'UPS Ground',
            '07' => 'UPS Worldwide Express',
            '08' => 'UPS Worldwide Expedited',
            '11' => 'UPS Standard International',
            '12' => 'UPS Three-Day Select',
            '13' => 'UPS Next Day Air Saver',
            '14' => 'UPS Next Day Air Early AM',
            '54' => 'UPS Worldwide Express Plus',
            '59' => 'UPS Second Day Air AM',
            '65' => 'UPS Saver'
        ); 

    /**
     * Construct
     *
     * Includes the upsRate API and loads the UPS login details
     *
     * @return null
     */
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
    function get_rate($details) {
        if (!$details['weight']) {
            return 0;
        }
        
        $details['length'] = isset($details['length']) ? $details['length'] : 0;
        $details['width'] = isset($details['width']) ? $details['width'] : 0;
        $details['height'] = isset($details['height']) ? $details['height'] : 0;
        
        return $this->upsRate->getRate(
            $details['from_zip'],
            $details['to_zip'],
            $details['to_country'],
            $details['service'],
            $details['length'],
            $details['width'],
            $details['height'],
            $details['weight']);
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
    function get_rate_from_cart($from_zip,$to_zip,$to_country,$service,$cart) {
        $this->SC->load_library('Cart');
        $weight = $this->SC->Cart->weigh_cart($cart);                                        
        
        $details = array(
            'from_zip' => $from_zip,
            'to_zip' => $to_zip,
            'to_country' => $to_country,
            'service' => $service,
            'weight' => $weight);
        
        $shipping_rate = $this->get_rate($details);                
        
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
        $rate = $this->get_rate_from_cart(
            $this->SC->Config->get_setting('storeZipcode'),
            $transaction->ship_postalcode,
            $transaction->ship_country,
            $service,
            $transaction->items);
        
        return $rate;
        
    }
}


