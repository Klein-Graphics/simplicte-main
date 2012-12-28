<?php
/**
 * Paypal Express Cart
 *
 * @todo
 *
 * @package Checkout\Gateway Drivers
 */
namespace Gateway_Driver;
/**
 * Paypal Express Cart Driver Class
 *
 * @package Checkout\Gateway Drivers
 */
class Paypal_EC extends \SC_Gateway_Driver {

    public static $default_name = 'Paypal';
    
    function __construct($name=NULL) {
        parent::__construct($name);
                
        $this->t = \Model\Transaction::find($this->SC->Session->get_open_transaction());
        $this->c = \Model\Customer::find_by_custid($this->t->custid);
        
        $this->live = $this->SC->Config->get_setting('store_live');
    
    }
    
    function load($extra='') {
        $result = $this->set_express_checkout();
        
        if ($result['ACK'] == 'Success') {
            echo $this->generate_checkout_button($result['TOKEN']);
        } else {
            foreach ($result['errors'] as $error) {
                echo "{$error['L_SEVERITYCODE']}: {$error['L_ERRORCODE']} {$error['L_SHORTMESSAGE']}. {$error['L_LONGMESSAGE']} <br />";
            }  
        }
        
        echo $extra;        
    }
    
    function load_test() {
        echo $this->load('PAYPALTEST');
    }        
    
    static function relay($live,$user,$pwd,$sig) {  
        global $CONFIG;                        
                          
        $user = decrypt(urldecode($user));
        $pwd = decrypt(urldecode($pwd));
        $sig = decrypt(urldecode($sig));               
        
        $curl = new \Curl();
        
        $url = ($live) ?
            'https://api-3t.paypal.com/nvp' : 'https://api-3t.sandbox.paypal.com/nvp';                        
            
        $data = json_decode(decrypt($_POST['scd']),true);        
        
        
        $data_to_keep = array(
            'TOKEN','PAYERID','PAYMENTREQUEST_0_AMT','PAYMENTREQUEST_0_CURRENCYCODE'
        );        
        
        $t_id = isset($data['PAYMENTREQUEST_0_INVNUM']) ? $data['PAYMENTREQUEST_0_INVNUM'] : 0;   

        $data = array_intersect_key($data,array_fill_keys($data_to_keep,''));
        
        $data['METHOD'] = 'DoExpressCheckoutPayment';
        $data['USER'] = $user;
        $data['PWD'] = $pwd;
        $data['SIGNATURE'] = $sig;
        $data['VERSION'] = '88.0';
              
        $result = self::parse_response($curl->post($url,$data,$info));
        
        $result_errors = '';
        if (! $result_success = ($result['ACK'] == 'Success')) {
            foreach ($result['errors'] as $error) {
                    $result_errors .= "{$error['L_SEVERITYCODE']}: {$error['L_ERRORCODE']} {$error['L_SHORTMESSAGE']}. {$error['L_LONGMESSAGE']} <br />";
            }  
        } else {
            $result_errors = 'Transaction success';
        }             
        
        
        $transaction_result = array(
            'transaction' => $t_id,
            'method' => 'Paypal',
            'hash' => md5("{$t_id} {$CONFIG['SEED']}"),
            'status_code' => ($result['ACK'] == 'Success'),
            'status_text' => $result_errors
        );
        
        return $curl->post(INCOMING_RELAY_URL,$transaction_result,$info);
        
        
    }
    
    function paypal_api_call($data) {
        $curl = new \Curl();
        
        $url = ($this->live) ?
            'https://api-3t.paypal.com/nvp' : 'https://api-3t.sandbox.paypal.com/nvp';
        
        $result = $curl->post($url,$data,$info);        
        
        //Parse the result
        return self::parse_response($result);
    }      
    
    static function parse_response($result) {
        $result = explode('&',$result);       
        
        foreach ($result as $key => $value) {
            unset($result[$key]);
            list($key,$value) = explode('=',$value);
            if (strpos($key,'L_ERRORCODE') === 0) { 
                $key = $key[11];
                
                $result['errors'][$key]['L_ERRORCODE'] = $value;
                continue;
            }
            $result[$key] = urldecode($value);            
        }                
        
        if (isset($result['errors'])) {
            foreach ($result['errors'] as $key => &$error) {
                $error['L_SHORTMESSAGE'] = $result["L_SHORTMESSAGE$key"];
                unset ($result["L_SHORTMESSAGE$key"]);
                
                $error['L_LONGMESSAGE'] = $result["L_LONGMESSAGE$key"];
                unset ($result["L_LONGMESSAGE$key"]);
                
                $error['L_SEVERITYCODE'] = $result["L_SEVERITYCODE$key"];
                unset ($result["L_SEVERITYCODE$key"]);
            }
        }
        return $result;
    }
    
    private function set_express_checkout() {
    
        $data = array(
            'METHOD' => 'SetExpressCheckout',
            'VERSION' => '88.0',
            'SOLUTIONTYPE' => 'Sole',
            'USER' => $this->SC->Config->get_setting('paypaluser'),
            'PWD' => $this->SC->Config->get_setting('paypalpwd'),
            'SIGNATURE' => $this->SC->Config->get_setting('paypalsignature'),
            'PAYMENTREQUEST_0_AMT' => number_format($this->t->total,2,'.',''),
            'PAYMENTREQUEST_0_ITEMAMT' => number_format($this->t->subtotal - $this->t->discount,2,'.',''),
            'PAYMENTREQUEST_0_SHIPPINGAMT' => number_format($this->t->shipping,2,'.',''),
            'PAYMENTREQUEST_0_TAXAMT' => number_format($this->t->taxrate,2,'.',''),
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',  
            'PAYMENTREQUEST_0_INVNUM' => $this->t->ordernumber,            
            'RETURNURL' => sc_location('core/includes/paypal_confirm.php'),
            'CANCELURL' => sc_location('cancel'),
            'HDRIMG' => $this->SC->Config->get_setting('paypalhdrimg'),
            'HDRBORDERCOLOR' => $this->SC->Config->get_setting('paypalhdrbordercolor'),
            'PAYFLOWCOLOR' => $this->SC->Config->get_setting('paypalpayflowcolor'),
            'BRANDNAME' => $this->SC->Config->get_setting('storename'),
            'CUSTOMERSERVICENUMBER' => $this->SC->Config->get_setting('customerservicenumber'),
            //Customer info                                   
            'EMAIL' => $this->c->email,
            'PAYMENTREQUEST_0_SHIPTONAME' => $this->t->ship_fullname,
            'PAYMENTREQUEST_0_SHIPTOSTREET' => $this->t->ship_streetaddress,
            'PAYMENTREQUEST_0_SHIPTOSTREET2' => $this->t->ship_full_apt,            
            'PAYMENTREQUEST_0_SHIPTOSTATE' => $this->t->ship_state,
            'PAYMENTREQUEST_0_SHIPTOZIP' => $this->t->ship_postalcode,
            'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => $this->t->ship_country,
            'PAYMENTREQUEST_0_SHIPTOPHONENUM' => $this->t->ship_phone,            
        );                    
        
        if ($currency = $this->SC->Config->get_setting('currency')) {
            $data['PAYMENTREQUEST_0_CURRENCYCODE'] = $currency;
        }
        
        //Generate item table
        $items = $this->SC->Cart->explode_cart($this->t->items);
        
        $billable_states = $this->SC->Config->get_setting('taxstates');
        $billable_states = explode(',',$billable_states);
        $tax = 0;
        if (array_search(strtolower($this->t->ship_state),array_to_lower($billable_states))!==FALSE) {
            $tax = 1;    
        }
        
        foreach ($items as $key => $item) {
            $item['data'] = \Model\Item::find($item['id']);
            $data["L_PAYMENTREQUEST_0_NAME$key"] = str_trunc($item['data']->name,127);
            $options_string = '';            
            foreach ($item['options'] as $option) {
                $options_string .= \Model\Itemoption::find($option['id'])->name.'.';
            }   
            $data["L_PAYMENTREQUEST_0_DESC$key"] = str_trunc($options_string,127);
            $data["L_PAYMENTREQUEST_0_AMT$key"] = number_format($item['price'],2,'.','');
            $data["L_PAYMENTREQUEST_0_NUMBER$key"] = $item['data']->number;
            $data["L_PAYMENTREQUEST_0_QTY$key"] = $item['quantity'];
            $data["L_PAYMENTREQUEST_0_TAXAMT$key"] = number_format(round($item['price']
                                                     *$this->SC->Config->get_setting('salestax')
                                                     *(! $this->SC->Items->item_flag($item['id'],'notax')),2)*$tax,2,'.','');
            $data["L_PAYMENTREQUEST_0_ITEMWEIGHTVALUE$key"] = $item['data']->weight;                      
            $data["L_PAYMENTREQUEST_0_ITEMWEIGHTUNIT$key"] = 'lbs';                               
            $data["L_PAYMENTREQUEST_0_ITEMCATEGORY$key"] = ($this->SC->Items->item_flag($item['id'],'digital')) 
                                                            ? 'Digital'
                                                            : 'Physical';             
        }
        
        if ($this->t->discount) {
            $key++;
            $data["L_PAYMENTREQUEST_0_NAME$key"] = 'Discount';
            $data["L_PAYMENTREQUEST_0_AMT$key"] = -$this->t->discount;
            
        }      
        
        $result = $this->paypal_api_call($data);
        
        return $result;
                
    }
    
    private function generate_checkout_button($token) {           
        
        $url = "https://www.".(($this->live)?'':'sandbox.')."paypal.com/websc?cmd=_express-checkout&token=$token";
            
        return "<a href=\"$url\" title=\"Checkout With Paypal\">
                    <img 
                        src=\"https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif\" 
                        alt=\"Checkout With Paypal\" 
                    />
                </a>";                
    }
    
    
}
