<?php
/**
 * PayPal Pay flow link
 *
 * @see https://cms.paypal.com/cms_content/US/en_US/files/developer/PP_PayflowLink_Guide.pdf
 *
 * @package Checkout\Gateway Drivers
 */
namespace Gateway_Driver;

/**
 * Authorize.net Server Integration Method Driver Class
 *
 * @package Checkout\Gateway Drivers
 */
class Paypal_Payflow_Link extends \SC_Gateway_Driver {        
    
    public static $default_name = 'Credit Card / PayPal';
        
    /**
     * Construct
     *
     * Assigns the human readable name, grabs the transaction and customer
     * details
     *
     * @return null
     *
     * @param string $name The human readable name
     */
    function __construct($name=NULL) {
    
        parent::__construct($name);
    
<<<<<<< Updated upstream
        $t_id = $this->SC->Session->get_open_transaction();
        
        $this->t = \Model\Transaction::find($t_id);
        $this->c = \Model\Customer::find_by_custid($this->t->custid);
=======
        $t_id = $this->SC->Session->get_open_transaction();        
        
        $this->t = \Model\Transaction::find($t_id);
        $this->c = \Model\Customer::find_by_custid($this->t->custid);
        
        $this->securetoken_id = $this->t->ordernumber.time();        
        
        $this->live = $this->SC->Config->get_setting('store_live');
>>>>>>> Stashed changes
    }
    
    /**
     * Load
     *
     * Returns the form to be inserted onto the page
     *
     * @return string
     */
    function load() {
        
<<<<<<< Updated upstream
        $output = '<form action="https://payflowlink.paypal.com" method="POST">';
        
        $output .= $this->generate_elements();
        
        $output .= "
Click below to be taken to the payment processor's secure webform. You will leave this site.<br />
<input type=\"submit\" value=\"Continue To Processor\" />
</form>            
";
        
        return $output;
=======
>>>>>>> Stashed changes
    }          

    /**
     * Load
     *
     * Returns the form to be inserted onto the page, using the test details
     *
     * @return string
     */
    function load_test() {
<<<<<<< Updated upstream
    
        $output = '<form action="https://payflowlink.paypal.com" method="POST">';
    
        $output .= $this->generate_elements();
        
        $output .= '<input type="submit" value="Run Test" /></form>';
        
        return $output;
    }
    
    /**
     * Relay
     *
     * Translates gateway-specific information to simplecart's
     * incoming relay script. Like all relay scripts, simplecart hasn't been
     * initiated yet, so this can only use php functions and SC's global
     * functions. This function is also called statically, so it cannot use
     * the "this" keyword
     *
     * @return Bool whether or not the transaction was successful
     */
    static function relay() {
        global $CONFIG;                    
        
        $curl = new \Curl();
        
        $transaction_result = array(
            'transaction' => $_POST['x_invoice_num'],
            'method' => $_POST['x_card_type'].' ending in '.$_POST['x_account_number'],
            'hash' => md5("{$_POST['x_invoice_num']} {$CONFIG['SEED']}"),
            'status_code' => ($_POST['x_response_code'] == 1),
            'status_text' => $_POST['x_response_reason_text'],
        );
        
        return $curl->post(INCOMING_RELAY_URL,$transaction_result,$info);     
    }  
    
    /**
     * Generate Elements
     * 
     * Generates the form elements to be inserted into the page
     *
     * @return string     
     */
    function generate_elements() {         
    
        $timestamp = time();
       
        $inputs = array(
			'LOGIN' => 'todo',
			'PARTNER' => 'todo',
            'CURRENCY' => $this->SC->Config->get_setting('currency'), //TODO Translate the price into USD no matter what
            //Required Fields
            'TYPE' => 'S',
            'AMOUNT' => number_format($this->SC->Cart->calculate_soft_total($this->t),2,'.',''),
            'INVOICE' => $this->t->ordernumber,
            'x_relay_response' => 'TRUE',
            //Order information
            'DESCRIPTION' => 'Purchase from '.$this->SC->Config->get_setting('storename'),
            'TAX' => $this->t->taxrate,
            'SHIPAMOUNT' => number_format($this->t->shipping,2,'.',''),
            //Customer Information
            'EMAIL' => $this->customer->email,
            'NAME' => $this->t->bill_fullname,
            'ADDRESS' => $this->t->bill_streetaddress,
            'CITY' => $this->t->bill_city,
            'STATE' => $this->t->bill_state,
            'ZIP' => $this->t->bill_postalcode,
            'COUNTRY' => $this->t->bill_country,
            'PHONE' => $this->t->bill_phone,                
            'CUSTID' => $this->t->custid,
            'NAMETOSHIP' => $this->t->ship_fullname,
            'ADDRESSTOSHIP' => $this->t->ship_streetaddress,
            'CITYTOSHIP' => $this->t->ship_city,
            'STATETOSHIP' => $this->t->ship_state,
            'ZIPTOSHIP' => $this->t->ship_postalcode,
            'COUNTRYTOSHIP' => $this->t->ship_country,
            //Form HTML
            //Return URLS
            'x_cancel_url' => sc_location('cancel.php'),
            'x_relay_url' =>  $this->outgoing_relay_url,
            //Additional Fields
            'x_test_request' => ($this->SC->Config->get_setting('store_live'))
                                ? 'FALSE'
                                : 'TRUE',                                                                
        );    
        
        //Items 
        /*
        $items = $this->SC->Cart->explode_cart($this->t->items);
        
        if (count($items)>30) {
            $addl_items = array_slice($items,29);
            $addl_items = $this->SC->Cart->subtotal($addl_items);
            $items = array_slice($items,0,29);
        }
        
        $inputs['x_line_item'] = array(); 
        
        foreach ($items as $num => $item) {
            $num++;
            $inputs['x_line_item'][] = 
                "item$num<|>".str_trunc($this->SC->Items->item_name($item['id']),31).
                "<|><|>{$item['quantity']}<|>".$item['price']."<|>"
                .(($this->SC->Items->item_flag($item['id'],'notax')) ? 'FALSE' : 'TRUE');
        }
        
        if (isset($addl_items)) {
            $inputs['x_line_item'][] = "item30<|>Additional Items<|><|>1<|>$addl_items<|>TRUE";
        }
        
        $output = '';*/
        
        //Customer's ip
        
        $inputs['x_customer_ip'] = $_SERVER['REMOTE_ADDR'];
        
        
        foreach ($inputs as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $value2) {
                    if ($value2!==FALSE) {
                        $output .= "<input type=\"hidden\" name=\"$name\" value=\"$value2\" />";
                    }
                }
                continue;
            }
            if ($value!==FALSE) {
                $output .= "<input type=\"hidden\" name=\"$name\" value=\"$value\" />";
            }
        }           
        
        return $output;
    
    }        
=======
        $this->generate_transaction();
    }
    
    /**
     * Generate transaction
     *
     * Sets up the information and token that will be CURL'd to paypal's gateway
     * server.
     *
     *     
     */
    function generate_transaction() {
        $data = array(
            //User parameters
            'USER' => $this->SC->Config->get_setting('payflowuser'),
            'VENDOR' => $this->SC->Config->get_setting('payflowvendor'),
            'PARTNER' => $this->SC->Config->get_setting('payflowpartner'),
            'PWD' => $this->SC->Config->get_setting('payflowpwd'),
            //Transaction parameters
            'INVNUM' => $this->t->ordernumber,           
            'TRXTYPE' => 'S',
            'CREATESECURETOKEN' => 'Y',
            'SECURETOKENID' => $this->securetoken_id,
            'CURRENCY' => $this->SC->Config->get_setting('currency'),
            'ORDERDESC' => 'Purchase from '.$this->SC->Config->get_setting('storename'),
            //Hosted Pages Params
            'CANCELURL' => sc_location('cancel.php'),
            'CSCREQUIRED' => 'TRUE',
            'DISABLERECEIPT' => 'TRUE',
            'ERRORURL' => sc_location('cancel.php'),
            'RETURNURL' => sc_location('receipt/'.$this->t->ordernumber),
            'SILENTPOSTURL' => $this->outgoing_relay_url,
            'URLMETHOD' => 'POST',
            //Hosted Pages Layout
            'PAGECOLLAPSEBGCOLOR' => $this->SC->Config->get_setting('payflowpagecollapsebgcolor'),
            'PAGECOLLAPSETEXTCOLOR' => $this->SC->Config->get_setting('payflowpagecollapsetextcolor'),
            'PAGEBUTTONBGCOLOR' => $this->SC->Config->get_setting('payflowpagebuttonbgcolor'),
            'PAGEBUTTONTEXTCOLOR' => $this->SC->Config->get_setting('payflowpagebuttontextcolor'),
            'LABELTEXTCOLOR' => $this->SC->Config->get_setting('labeltextcolor'),
            //Transaction amounts
            'ITEMAMT' => $this->t->subtotal,
            'TAXAMT' => $this->t->taxrate,
            'FREIGHTAMT' => $this->t->shipping,
            'DISCOUNT' => $this->t->discount,
            'AMT' => $this->t->total,
            //Customer information
            'CUSTIP' => $_SERVER['REMOTE_ADDR'],
            'EMAIL' => $this->c->email,
            'BILLTOFIRSTNAME' => $this->t->bill_firstname,
            'BILLTOLASTNAME' => $this->t->bill_lastname,
            'BILLTOSTREET' => $this->t->bill_streetaddress,
            'BILLTOCITY' => $this->t->bill_city,
            'BILLTOSTATE' => $this->t->bill_state,
            'BILLTOZIP' => $this->t->bill_postalcode,
            'BILLTOCOUNTRY' => $this->t->bill_country,
            'SHIPTOFIRSTNAME' => $this->t->ship_firstname,
            'SHIPTOLASTNAME' => $this->t->ship_lastname,
            'SHIPTOSTREET' => $this->t->ship_streetaddress,
            'SHIPTOCITY' => $this->t->ship_city,
            'SHIPTOSTATE' => $this->t->ship_state,
            'SHIPTOZIP' => $this->t->ship_postalcode,
            'SHIPTOCOUNTRY' => $this->t->ship_country
        );  
        
        //Items
        $items = $this->SC->Cart->explode_cart($this->t->items);
        
        $billable_states = $this->SC->Config->get_setting('taxstates');
        $billable_states = explode(',',$billable_states);
        $tax = 0;
        if (array_search(strtolower($this->t->ship_state),array_to_lower($billable_states))!==FALSE) {
            $tax = 1;    
        }
        
        foreach ($items as $key => $item) {
            $item['data'] = \Model\Item::find($item['id']);
            $data["L_NAME$key"] = str_trunc($item['data']->name,127);
            $options_string = '';            
            foreach ($item['options'] as $option) {
                $options_string .= \Model\Itemoption::find($option['id'])->name.'. ';
            }   
            $data["L_DESC$key"] = str_trunc($options_string,127);
            if ($item['price']) {
                $data["L_AMT$key"] = number_format($item['price'],2,'.','');
            } else {
                $data["L_AMT$key"] = number_format(0.01,2,'.','');
                $data["AMT"] = number_format($data["AMT"]+0.01,2,'.','');
                $data["DISCOUNT"] += 0.01;
            }
            $data["L_SKU$key"] = $item['data']->number;
            $data["L_QTY$key"] = $item['quantity'];
            $data["L_TAXAMT$key"] = number_format(round($item['price']
                                                     *$this->SC->Config->get_setting('salestax')
                                                     *(! $this->SC->Items->item_flag($item['id'],'notax')),2)*$tax,2,'.','');   
        }
        
        $curl = new \Curl();
        
        better_print_r($this->paypal_api_call($data));
        
        
    }
    
    function paypal_api_call($data) {
        $curl = new \Curl();
        
        $url = ($this->live) ?
            'https://payflowlink.paypal.com' : 'https://pilot-payflowpro.paypal.com';
        
        $result = $curl->post($url,$data,$info);        
        
        //Parse the result
        return self::parse_response($result);
    } 
    
    static function parse_response($result) {
        $result = explode('&',htmlspecialchars_decode($result));       
        
        foreach ($result as $key => $value) {
            unset($result[$key]);
            list($key,$value) = explode('=',$value);            
            $result[$key] = $value;            
        }                

        return $result;
    }
    
    /**
     * Relay
     *
     * Translates gateway-specific information to simplecart's
     * incoming relay script. Like all relay scripts, simplecart hasn't been
     * initiated yet, so this can only use php functions and SC's global
     * functions. This function is also called statically, so it cannot use
     * the "this" keyword
     *
     * @return Bool whether or not the transaction was successful
     */
    static function relay() {
        global $CONFIG;                    
           
    }      
>>>>>>> Stashed changes
    
}
