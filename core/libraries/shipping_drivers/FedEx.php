<?php
/**
 * Fedex Shipping Rate
 *
 * @package Checkout\Shipping Drivers
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
            'FIRST CLASS' => 'USPS First Class',
            'PRIORITY' => 'USPS Priority',
            'EXPRESS' => 'USPS Express',
            'EXPRESS SH' => 'USPS Express SH',
            'EXPRESS HFP' => 'USPS Express Hold For Pickup',
            'PARCEL' => 'USPS Parcel',
            'MEDIA' => 'USPS Media',
            'LIBRARY' => 'USPS Library',
            'ALL' => 'USPS All',
            'ONLINE' => 'USPS Online',
            '12' => 'USPS USPS GXG Envelopes',
            '1' => 'USPS Express Mail International',
            '26' => 'USPS Express Mail International Flat Rate Boxes',
            '2' => 'USPS Priority Mail International',
            '11' => 'USPS Priority Mail International Large Flat Rate Box',
            '9' => 'USPS Priority Mail International Medium FLat Rate Box',
            '16' => 'USPS Priority Mail International Small Flat Rate Box',
            '24' => 'USPS Priority Mail International DVD Flat Rate Priced Box',
            '25' => 'USPS Priority Mail International Large Video Flat Rate Priced Box',
            '15' => 'USPS First Class Package International Service'
    ); 
        
    function api_call($data,$int=FALSE) {
        $this->xmlreq = new \DOMDocument();
        $curl = new \Curl();        
        
        $api = ($int ? 'IntlRateV2' : 'RateV4');
        
        $root = $this->xmlreq->appendChild(
                    $this->xmlreq->createElement("{$api}Request"));
        
        $root->appendChild(
            $this->xmlreq->createAttribute('USERID'))->appendChild(
                $this->xmlreq->createTextNode($this->SC->Config->get_setting('uspsuserid')));                              
        
        $root = $this->proccess_data($data,$root);        
        
        $this->xmlresp = new \DOMDocument;                
        
        $this->xmlresp->loadXML($resp = $curl->get(
            '56.0.34.43/ShippingAPI.dll',
//            'http://stg-secure.shippingapis.com/',
            array(
                'API' => "{$api}",
                'XML' => $this->xmlreq->saveXML()
            ),
            $info
        ));                                      
        
        return $this->xmlresp;       
               
    }
    
    private function proccess_data($nodes,$root) {                            
        foreach($nodes as $node_name => $node) {
            if (!is_array($node)) {
                $node_data = $node;
                $node = array();
                $node['data'] = $node_data;
            } else if (!isset($node['data'])) {
                $node['data'] = '';
            }             
            $attach_node = $this->xmlreq->createElement($node_name,$node['data']); 
            if (isset($node['attributes'])) {
                foreach ($node['attributes'] as $attr_name => $attr) {
                    $attach_node->appendChild(
                        $this->xmlreq->createAttribute($attr_name))->appendChild(
                            $this->xmlreq->createTextNode($attr));    
                }
            }
            
            if (isset($node['children'])) {
                $attach_node = $this->proccess_data($node['children'],$attach_node);          
            }
            
            $root->appendChild($attach_node);                                                                                     
        }
        
        return $root;
    }  
    
    private function translate_country($code) {
        $countries = array(
            "US" => "United States",
            "AF" => "Afghanistan",
            "AX" => "Åland Islands",
            "AL" => "Albania",
            "DZ" => "Algeria",
            "AS" => "American Samoa",
            "AD" => "Andorra",
            "AO" => "Angola",
            "AI" => "Anguilla",
            "AQ" => "Antarctica",
            "AG" => "Antigua and Barbuda",
            "AR" => "Argentina",
            "AM" => "Armenia",
            "AW" => "Aruba",
            "AU" => "Australia",
            "AT" => "Austria",
            "AZ" => "Azerbaijan",
            "BS" => "Bahamas",
            "BH" => "Bahrain",
            "BD" => "Bangladesh",
            "BB" => "Barbados",
            "BY" => "Belarus",
            "BE" => "Belgium",
            "BZ" => "Belize",
            "BJ" => "Benin",
            "BM" => "Bermuda",
            "BT" => "Bhutan",
            "BO" => "Bolivia",
            "BA" => "Bosnia and Herzegovina",
            "BW" => "Botswana",
            "BV" => "Bouvet Island",
            "BR" => "Brazil",
            "IO" => "British Indian Ocean Territory",
            "BN" => "Brunei Darussalam",
            "BG" => "Bulgaria",
            "BF" => "Burkina Faso",
            "BI" => "Burundi",
            "KH" => "Cambodia",
            "CM" => "Cameroon",
            "CA" => "Canada",
            "CV" => "Cape Verde",
            "KY" => "Cayman Islands",
            "CF" => "Central African Republic",
            "TD" => "Chad",
            "CL" => "Chile",
            "CN" => "China",
            "CX" => "Christmas Island",
            "CC" => "Cocos (Keeling) Islands",
            "CO" => "Colombia",
            "KM" => "Comoros",
            "CG" => "Congo",
            "CD" => "Congo, The Democratic Republic of The",
            "CK" => "Cook Islands",
            "CR" => "Costa Rica",
            "CI" => "Cote D'ivoire",
            "HR" => "Croatia",
            "CU" => "Cuba",
            "CY" => "Cyprus",
            "CZ" => "Czech Republic",
            "DK" => "Denmark",
            "DJ" => "Djibouti",
            "DM" => "Dominica",
            "DO" => "Dominican Republic",
            "EC" => "Ecuador",
            "EG" => "Egypt",
            "SV" => "El Salvador",
            "GQ" => "Equatorial Guinea",
            "ER" => "Eritrea",
            "EE" => "Estonia",
            "ET" => "Ethiopia",
            "FK" => "Falkland Islands (Malvinas)",
            "FO" => "Faroe Islands",
            "FJ" => "Fiji",
            "FI" => "Finland",
            "FR" => "France",
            "GF" => "French Guiana",
            "PF" => "French Polynesia",
            "TF" => "French Southern Territories",
            "GA" => "Gabon",
            "GM" => "Gambia",
            "GE" => "Georgia",
            "DE" => "Germany",
            "GH" => "Ghana",
            "GI" => "Gibraltar",
            "GR" => "Greece",
            "GL" => "Greenland",
            "GD" => "Grenada",
            "GP" => "Guadeloupe",
            "GU" => "Guam",
            "GT" => "Guatemala",
            "GG" => "Guernsey",
            "GN" => "Guinea",
            "GW" => "Guinea-bissau",
            "GY" => "Guyana",
            "HT" => "Haiti",
            "HM" => "Heard Island and Mcdonald Islands",
            "VA" => "Holy See (Vatican City State)",
            "HN" => "Honduras",
            "HK" => "Hong Kong",
            "HU" => "Hungary",
            "IS" => "Iceland",
            "IN" => "India",
            "ID" => "Indonesia",
            "IR" => "Iran, Islamic Republic of",
            "IQ" => "Iraq",
            "IE" => "Ireland",
            "IM" => "Isle of Man",
            "IL" => "Israel",
            "IT" => "Italy",
            "JM" => "Jamaica",
            "JP" => "Japan",
            "JE" => "Jersey",
            "JO" => "Jordan",
            "KZ" => "Kazakhstan",
            "KE" => "Kenya",
            "KI" => "Kiribati",
            "KP" => "Korea, Democratic People's Republic of",
            "KR" => "Korea, Republic of",
            "KW" => "Kuwait",
            "KG" => "Kyrgyzstan",
            "LA" => "Lao People's Democratic Republic",
            "LV" => "Latvia",
            "LB" => "Lebanon",
            "LS" => "Lesotho",
            "LR" => "Liberia",
            "LY" => "Libyan Arab Jamahiriya",
            "LI" => "Liechtenstein",
            "LT" => "Lithuania",
            "LU" => "Luxembourg",
            "MO" => "Macao",
            "MK" => "Macedonia, The Former Yugoslav Republic of",
            "MG" => "Madagascar",
            "MW" => "Malawi",
            "MY" => "Malaysia",
            "MV" => "Maldives",
            "ML" => "Mali",
            "MT" => "Malta",
            "MH" => "Marshall Islands",
            "MQ" => "Martinique",
            "MR" => "Mauritania",
            "MU" => "Mauritius",
            "YT" => "Mayotte",
            "MX" => "Mexico",
            "FM" => "Micronesia, Federated States of",
            "MD" => "Moldova, Republic of",
            "MC" => "Monaco",
            "MN" => "Mongolia",
            "ME" => "Montenegro",
            "MS" => "Montserrat",
            "MA" => "Morocco",
            "MZ" => "Mozambique",
            "MM" => "Myanmar",
            "NA" => "Namibia",
            "NR" => "Nauru",
            "NP" => "Nepal",
            "NL" => "Netherlands",
            "AN" => "Netherlands Antilles",
            "NC" => "New Caledonia",
            "NZ" => "New Zealand",
            "NI" => "Nicaragua",
            "NE" => "Niger",
            "NG" => "Nigeria",
            "NU" => "Niue",
            "NF" => "Norfolk Island",
            "MP" => "Northern Mariana Islands",
            "NO" => "Norway",
            "OM" => "Oman",
            "PK" => "Pakistan",
            "PW" => "Palau",
            "PS" => "Palestinian Territory, Occupied",
            "PA" => "Panama",
            "PG" => "Papua New Guinea",
            "PY" => "Paraguay",
            "PE" => "Peru",
            "PH" => "Philippines",
            "PN" => "Pitcairn",
            "PL" => "Poland",
            "PT" => "Portugal",
            "PR" => "Puerto Rico",
            "QA" => "Qatar",
            "RE" => "Reunion",
            "RO" => "Romania",
            "RU" => "Russian Federation",
            "RW" => "Rwanda",
            "SH" => "Saint Helena",
            "KN" => "Saint Kitts and Nevis",
            "LC" => "Saint Lucia",
            "PM" => "Saint Pierre and Miquelon",
            "VC" => "Saint Vincent and The Grenadines",
            "WS" => "Samoa",
            "SM" => "San Marino",
            "ST" => "Sao Tome and Principe",
            "SA" => "Saudi Arabia",
            "SN" => "Senegal",
            "RS" => "Serbia",
            "SC" => "Seychelles",
            "SL" => "Sierra Leone",
            "SG" => "Singapore",
            "SK" => "Slovakia",
            "SI" => "Slovenia",
            "SB" => "Solomon Islands",
            "SO" => "Somalia",
            "ZA" => "South Africa",
            "GS" => "South Georgia and The South Sandwich Islands",
            "ES" => "Spain",
            "LK" => "Sri Lanka",
            "SD" => "Sudan",
            "SR" => "Suriname",
            "SJ" => "Svalbard and Jan Mayen",
            "SZ" => "Swaziland",
            "SE" => "Sweden",
            "CH" => "Switzerland",
            "SY" => "Syrian Arab Republic",
            "TW" => "Taiwan, Province of China",
            "TJ" => "Tajikistan",
            "TZ" => "Tanzania, United Republic of",
            "TH" => "Thailand",
            "TL" => "Timor-leste",
            "TG" => "Togo",
            "TK" => "Tokelau",
            "TO" => "Tonga",
            "TT" => "Trinidad and Tobago",
            "TN" => "Tunisia",
            "TR" => "Turkey",
            "TM" => "Turkmenistan",
            "TC" => "Turks and Caicos Islands",
            "TV" => "Tuvalu",
            "UG" => "Uganda",
            "UA" => "Ukraine",
            "AE" => "United Arab Emirates",
            "GB" => "United Kingdom",
            "UM" => "United States Minor Outlying Islands",
            "UY" => "Uruguay",
            "UZ" => "Uzbekistan",
            "VU" => "Vanuatu",
            "VE" => "Venezuela",
            "VN" => "Viet Nam",
            "VG" => "Virgin Islands, British",
            "VI" => "Virgin Islands, U.S.",
            "WF" => "Wallis and Futuna",
            "EH" => "Western Sahara",
            "YE" => "Yemen",
            "ZM" => "Zambia",
            "ZW" => "Zimbabwe"   
        );        
        
        return $countries[$code];
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
        
        $details['length'] = isset($details['length']) ? $details['length'] : '';
        $details['width'] = isset($details['width']) ? $details['width'] : '';
        $details['height'] = isset($details['height']) ? $details['height'] : '';
        
        function error_handler($response) { //Guess.
            $errors = $response->getElementsByTagName('Error');
            if ($errors->length) {
                $error_output = '';
                foreach ($errors as $error) {
                    foreach ($error->childNodes as $child) {
                        if ($child->nodeName == 'Description') {
                            $error_output .= $child->nodeValue.'<br />';
                            break;
                        }
                    }
                }
                return $error_output;
            }
            
            return FALSE;
        }
        
        //Is it international?
        if (is_numeric($details['service'])) { //International
            $response = $this->api_call(array(
                'Package' => array(
                    'attributes'=>array('ID'=>time()),
                    'children' =>
                        array(
                            'Pounds' => $details['weight'],
                            'Ounces' => '0',
                            'MailType' => 'Package',
                            'ValueOfContents' => '',
                            'Country' => $this->translate_country($details['to_country']),
                            'Container' => '',            
                            'Size' => 'Regular',
                            'Width' => $details['width'],
                            'Length' => $details['length'],
                            'Height' => $details['height'],
                            'Girth' => '',
                            'CommercialFlag' => 'Y'))                
            ),TRUE);
            
            $errors = error_handler($response);
            
            if ($errors) {
                return array(FALSE,$errors);
            }
            
            $services = $response->getElementsByTagName('Service');            
            
            foreach($services as $service) {
                if ($service->attributes->getNamedItem('ID')->nodeValue == $details['service']) {
                    foreach ($service->childNodes as $node) {
                        if ($node->nodeName == "CommercialPostage") {
                            return array(TRUE,$node->nodeValue);
                        }
                    }
                    return array(FALSE,'Service is not available for selected country');
                }
            }
        } else { //Domestic
            $response = $this->api_call(array(
                'Package' => array(
                    'attributes'=>array('ID'=>time()),
                    'children' =>
                        array(
                            'Service' => $details['service'].' COMMERCIAL',
                            'ZipOrigination' => $details['from_zip'],
                            'ZipDestination' => $details['to_zip'],                            
                            'Pounds' => $details['weight'],
                            'Ounces' => '0',
                            'Container' => '', 
                            'Size' => 'Regular'))                
            ),FALSE);
            
            $errors = error_handler($response);
            
            if ($errors) {
                return array(FALSE,$errors);
            }
            
            $price = $response->getElementsByTagName('CommercialRate');
            $price = $price->item(0)->nodeValue;
            
            return array(TRUE,$price);
            
        }
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


