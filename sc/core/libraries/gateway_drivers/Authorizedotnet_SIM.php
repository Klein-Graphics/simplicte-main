<?php
/**
 * Authorize.net Server Integration Method
 *
 * @see http://www.authorize.net/support/SIM_guide.pdf
 *
 * @package Checkout\Gateway Drivers
 */
namespace Gateway_Driver;

/**
 * Authorize.net Server Integration Method Driver Class
 *
 * @package Checkout\Gateway Drivers
 */
class Authorizedotnet_SIM extends \SC_Gateway_Driver {        
    
    public static $default_name = 'Credit Card';
        
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
    
        $this->SC->load_library(array('Session','Cart','Customer'));
    
        $t_id = $this->SC->Session->get_open_transaction();
        
        $this->transaction = $this->SC->Transactions->get_transaction($t_id);
        $this->customer = $this->SC->Customer->get_customer(
            $this->transaction->custid,
            '*',
            'custid'
        );
    }
    
    /**
     * Load
     *
     * Returns the form to be inserted onto the page
     *
     * @return string
     */
    function load() {
        
        $output = '<form action="https://secure.authorize.net/gateway/transact.dll" method="POST">';
        
        $output .= $this->generate_elements();
        
        $output .= <<<HTML
Click below to be taken to the payment processor's secure webform. You will leave this site.<br />
<input type="submit" value="Continue To Processor" />
</form>            
HTML;
        
        return $output;
    }          

    /**
     * Load
     *
     * Returns the form to be inserted onto the page, using the test details
     *
     * @return string
     */
    function load_test() {
    
        $output = '<form action="https://test.authorize.net/gateway/transact.dll" method="POST">';
    
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
    
        require_once 'core/includes/Curl.php';  
        
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
     * Generate Fingerprint
     *
     * Generates Authorize.net's fingerprint
     *
     * @return string
     *
     * @param int $timestamp A unixtimestamp to generate the fingerprint 
     */
    function generate_fingerprint($timestamp) {
                
        //THESE MUST STAY IN THIS ORDER
        $fp['login_id'] = $this->SC->Config->get_setting('authorizedotnetid');
        $fp['sequence'] = $this->transaction->ordernumber;
        $fp['timestamp'] = $timestamp;
        $fp['total'] = $this->SC->Cart->calculate_soft_total($this->transaction);
        $fp['currency'] = $this->SC->Config->get_setting('currency');
        
        return hash_hmac("md5",implode('^',$fp),$this->SC->Config->get_setting('authorizedotnetkey'));
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
            //Fingerprint
            'x_fp_hash' => $this->generate_fingerprint($timestamp),
            'x_fp_sequence' => $this->transaction->ordernumber,
            'x_fp_timestamp' => $timestamp,
            'x_currency_code' => $this->SC->Config->get_setting('currency'),
            'x_login' => $this->SC->Config->get_setting('authorizedotnetid'),
            //Required Fields
            'x_type' => 'AUTH_CAPTURE',
            'x_amount' => $this->SC->Cart->calculate_soft_total($this->transaction),
            'x_show_form' => 'PAYMENT_FORM',
            'x_trans_id' => $this->transaction->ordernumber,
            'x_relay_response' => 'TRUE',
            'x_delim_data' => 'FALSE',
            //Order information
            'x_invoice_num' => $this->transaction->ordernumber,
            'x_description' => 'Purchase from '.$this->SC->Config->get_setting('storename'),
            'x_tax' => $this->transaction->taxrate,
            'x_freight' => $this->transaction->shipping,
            //Customer Information
            'x_email' => $this->customer->email,
            'x_email_customer' => 'FALSE',
            'x_first_name' => $this->transaction->bill_firstname,
            'x_last_name' => $this->transaction->bill_lastname,
            'x_address' => $this->transaction->bill_streetaddress,
            'x_city' => $this->transaction->bill_city,
            'x_state' => $this->transaction->bill_state,
            'x_zip' => $this->transaction->bill_postalcode,
            'x_country' => $this->transaction->bill_country,
            'x_phone' => $this->transaction->bill_phone,                
            'x_cust_id' => $this->transaction->custid,
            'x_ship_to_first_name' => $this->transaction->ship_firstname,
            'x_ship_to_last_name' => $this->transaction->ship_lastname,
            'x_ship_to_address' => $this->transaction->ship_streetaddress,
            'x_ship_to_city' => $this->transaction->ship_city,
            'x_ship_to_state' => $this->transaction->ship_state,
            'x_ship_to_zip' => $this->transaction->ship_postalcode,
            'x_ship_to_country' => $this->transaction->ship_country,
            //Form HTML
            'x_header_html_payment_form' => $this->SC->Config->get_setting('authorizedotnetheader'),
            'x_header2_html_payment_form' => $this->SC->Config->get_setting('authorizedotnetheader'),
            'x_footer_html_payment_form' => $this->SC->Config->get_setting('authorizedotnetfooter'),
            'x_footer2_html_payment_form' => $this->SC->Config->get_setting('authorizedotnetfooter'),                
            'x_color_background' => $this->SC->Config->get_setting('authorizedotnetbgcolor'),
            'x_color_link' => $this->SC->Config->get_setting('authorizedotnetlinkcolor'),
            'x_color_text' => $this->SC->Config->get_setting('authorizedotnettextcolor'),
            'x_logo_url' => $this->SC->Config->get_setting('authorizedotnetlogourl'),
            'x_background_url' => $this->SC->Config->get_setting('authorizedotnetbgurl'),
            //Return URLS
            'x_cancel_url' => sc_location('cancel.php'),
            'x_relay_url' =>  $this->outgoing_relay_url,
            //Additional Fields
            'x_version' => '3.1',
            'x_method' => 'CC',
            'x_test_request' => ($this->SC->Config->get_setting('store_live'))
                                ? 'FALSE'
                                : 'TRUE',                                                                
        );    
        
        //Items 
        
        $items = $this->SC->Cart->explode_cart($this->transaction->items);
        
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
                "<|><|>{$item['quantity']}<|>".$this->SC->Cart->line_total($item)."<|>"
                .(($this->SC->Items->item_flag($item['id'],'notax')) ? 'FALSE' : 'TRUE');
        }
        
        if (isset($addl_items)) {
            $inputs['x_line_item'][] = "item30<|>Additional Items<|><|>1<|>$addl_items<|>TRUE";
        }
        
        $output = '';
        
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
    
}
