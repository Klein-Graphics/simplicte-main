<?php
/**
 * Fedex Shipping Rate
 *
 * @package Checkout\Shipping Drivers
 * 2IqGgorgkI597vYe
 */
namespace Shipping_Driver;

/**
 * FedEx Shipping Rate Driver
 *
 * @package Checkout\Shipping Drivers
 */ 
class FedEx extends \SC_Shipping_Driver {
    
    /**
     * The Human Readable Name
     */
    public static $name = 'FedEx';
    
    /**
     * Shipping codes
     */
    public static $shipping_codes = array(
            'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => 'FedEx Europe First Class International Priority',
            'FEDEX_1_DAY' => 'FedEx 1 Day',
            'FEDEX_2_DAY' => 'FedEx 2 Day',
            'FEDEX_2_DAY_AM' => 'Fedex 2 Day Morning',
            'FEDEX_EXPRESS_SAVER' => 'FeDex Express Saver',
            'FEDEX_GROUND' => 'FedEx Ground',
            'FEDEX_HOME_DELIVERY' => 'FedEx Home Delivery',
            'FIRST_OVERNIGHT' => 'FedEx First Overnight',
            'INTERNATIONAL_ECONOMY' => 'FedEx International Economy',
            'INTERNATIONAL_FIRST' => 'FedEx International First',
            'INTERNATIONAL_PRIORITY' => 'FedEx International Priority',
            'PRIORITY_OVERNIGHT' => 'FedEx Priority Overnight',
            'STANDARD_OVERNIGHT' => 'FedEx Standard Overnight'
    ); 
        
    function api_call($data) {
        $soap = new SoapClient('core/includes/RateService_v14.wsdl',array('trace' => 1));
        
        if (!$this->SC->Config->get_detail('storelive')) {
            $soap->__setLocation('https://wsbeta.fedex.com:443/web-services');
        }
        
        $data['WebAuthenticationDetail'] = array(
            'UserCredential' =>array(
	            'Key' => $this->SC->Config->get_detail('fedexkey'), 
	            'Password' => $this->SC->Config->get_detail('fedexpassword')
            )
        );         
        
        $data['ClientDetail'] = array(
	        'AccountNumber' => $this->SC->Config->get_detail('fedexaccountnumber'), 
	        'MeterNumber' => $this->SC->Config->get_detail('fedexmeternumber')
        );
        
        $data['Version'] = array(
            'ServiceId' => 'crs', 
            'Major' => '14', 
            'Intermediate' => '0', 
            'Minor' => '0'
        );                
          
        return $soap->getRates($data);     
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
        
        function error_handler($response) { //Guess.            
        }
        
        $data = array(
            'RequestedShipment' => array(
                'ServiceType' => $details['service'],
                'PackagingType' => 'YOUR_PACKAGING',
                'Shipper' => array(
                    'Address' => array(
			            'PostalCode' => $details['from_zip'],
			            'CountryCode' => 'US'
                    )
                ),
                'Recipient' => array(
                    'Address' => array(
			            'PostalCode' => $details['to_zip'],
			            'CountryCode' => $details['to_country']
                    )
                ),
                'RequestedPackageLineItems' => array (
                    'GroupPackageCount' => 1,
		            'Weight' => array(
			            'Value' => $details['weight'],
			            'Units' => 'LB'
		            )
	            )                        
                
            )
        
        ));
        
        if (isset($details['length'] || $details['width'] || $details['height'])) {
            $details['length'] = isset($details['length']) ? $details['length'] : '';
            $details['width'] = isset($details['width']) ? $details['width'] : '';
            $details['height'] = isset($details['height']) ? $details['height'] : '';
            
            $data['RequestedShipment']['RequestedPackageLineItems']['Dimensions'] = array(
			    'Length' => $details['length'],
			    'Width' => $details['width'],
			    'Height' => $details['height'],
			    'Units' => 'IN'
		    )
        }
        
        $response = $this->api_call($data);
        
        if ($response->HighestSeverity == 'ERROR') {
            $errors = array();
            foreach($response->Notifications as $notification) {
                if ($notification->Severity == 'ERROR')
                    $errors[] = $notification['Message'];
            }
            
            return array(FALSE,implode('<br>',$errors));
        }
        
        return array(TRUE,$resp->RateReplyDetails->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount);
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
    function get_rate_from_transaction($transaction,$service) {
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


