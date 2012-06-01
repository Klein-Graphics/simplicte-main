<?php

/**
 * Transactions Library
 *
 * Handles the CRUD of Transactions 
 * 
 * @package Transactions
 */

namespace Library;

/**
 * Transactions Library Class
 *
 * @package Transactions 
 */
class Transactions extends \SC_Library {

    /**
     * Get Transaction
     *
     * Returns information about a specific transaction
     *
     * @return mixed If $return_col contains a comma or is a asterisk, then the entire object is return,
     * otherwise, just the single return value
     * 
     * @param int|string $search The value to search for
     * @param string $return_col A comma sperated list of columns to return or an asterisk
     * @param string $search_col The column to search for $search in
     */
    function get_transaction($search,$return_col='*',$search_col='id') {
        
        if (is_array($return_col)) {
            $return_col = implode(',',$return_col);
        }
      
        $transaction = \Model\Transaction::find(array(
            'select' => $return_col,
            'conditions' => array(
                "$search_col" => $search,
                "transtype" => 'order'
            )
        ));        

        return db_return($transaction,$return_col);
      
    }                

    /**
     * Create transaction
     *
     * Creates a transaction
     *
     * @return int
     *
     * @param string|array $custid Either the customer's ID or an array containing all the
     * transaction data including the 'custid' field
     * @param array $data Data to insert into the transaction
     */
    function create_transaction($custid,$data=FALSE) {
        if (is_array($custid) && ! $data) {
            $data = $custid;
        } else {
            $data['custid'] = $custid;
        }      

        $data['transtype'] = 'order';
        $data['status'] = 'opened';

        $transaction = \Model\Transaction::create($data);
        $this->generate_order_number($transaction->id,TRUE);

        return $transaction->id;
    }

    /**
     * Generate Order Number
     *
     * Generates an order number based on the transaction ID and the date
     *
     * @return string
     *
     * @param int $id The id of the order
     * @param bool $update Whether or not to update the order number in the database    
     */
    function generate_order_number($id,$update=FALSE) {
        $order_number = date('ymd').str_pad($id%10000,4,'0',STR_PAD_LEFT);

        if ($update) {
            $this->update_transaction($id,array('ordernumber'=>$order_number));
        }

        return $order_number;            
    }

    /**
     * Update Transaction
     *
     * Updates a transaction
     *
     * @return bool
     *
     * @param int $id The transaction's id
     * @param array $attributes An array of data to update
     */
    function update_transaction($id,$attributes) {
      
        $transaction = \Model\Transaction::find($id);                                             
        
        foreach ($attributes as $attribute => $value) {      
            $transaction->$attribute = $value;   
        }      


        return $transaction->save();

      
    }
    
    /**
     * Delete Transation
     *
     * Deletes a transaction
     *
     * @return bool
     *
     * @param int $id The transaction's id
     */
    function delete_transaction($id) {
        
        $transaction = \Model\Transaction::find($id);
        
        $transaction->delete();
        
        return TRUE;   
        
    }

    /**
     * Associate customer
     *
     * Copies customer data to a transaction
     *
     * @return null
     *
     * @param int $transaction The trasaction id
     * @param int $cust_id The customer's id
     */
    function associate_customer($transaction,$cust_id) {         
        
        $fields_to_copy = array(
            'custid',
            'ship_firstname',
            'ship_initial',
            'ship_lastname',
            'ship_streetaddress',
            'ship_apt',
            'ship_city',
            'ship_state',
            'ship_postalcode',
            'ship_country',
            'ship_phone',
            'bill_firstname',
            'bill_lastname',
            'bill_initial',
            'bill_streetaddress',
            'bill_apt',
            'bill_city',
            'bill_state',
            'bill_postalcode',
            'bill_country',
            'bill_phone'
        );
        
        $this->SC->load_library('Customer');
        
        $customer_details = $this->SC->Customer->get_customer($cust_id,$fields_to_copy);    
        
        $copy_details = array();
        
        foreach ($fields_to_copy as $field) {
            $copy_details[$field] = $customer_details->$field;
        }        
        
        $this->update_transaction($transaction,$copy_details);               
        
    }
    
    /**
     * Get Stale Transactions
     *
     * Returns transactions that have been sitting around pending for too long
     *
     * @return object[]
     *
     * @param int $date A unix timestamp 
     */
    function get_stale_transactions($date=NULL) {
        $date = (
            ($date!=NULL) 
                ? $date 
                : strtotime(($this->SC->Config->get_setting('cleanupRate')*-1)." minutes")
        );
        
        $orders = \Model\Transaction::all(array(
            'conditions' => 
                array('lastupdate < ? AND status = "pending"',$date),
            'select' => 'id' 
        ));             

        return $orders;                                                    
    }
    

}
