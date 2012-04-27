<?php

/**
 * Customer Library
 *
 * This Library contains handles the CRUD of the customer database
 *
 * @package Account
 */

namespace Library;
/** 
 * The Customer library class
 *
 * @package Account
 */
class Customer extends \SC_Library {

    /** 
     * Get Customer
     *
     * Searches and returns a customer information
     *
     * @return mixed
     *
     * @param string $search The value to search for
     * @param string $return_col The column(s) you want returned, seperated by ','
     * @param string $search_col The column that should be searched for $search
     */
    function get_customer($search,$return_col='*',$search_col='id') {

        if (is_array($return_col)) {
            $return_col = implode(',',$return_col);
        }

        $customer = \Model\Customer::find(array(
            'conditions' => array("$search_col = ?",$search),
            'select' => $return_col
        ));

        return db_return($customer,$return_col);

    } 

    /**
     * Create Customer
     *
     * Creates a new customer
     *
     * @return int The customer's DB id
     *
     * @param string|array $cust_id The human-readable customer ID, or an array containing data
     * to pass to the new customer
     * @param array $data Data to pass to the new customer
     */
    function create_customer($cust_id,$data=FALSE) {
        if (is_array($cust_id) && ! $data) {
            $data = $cust_id;
        } else {
            $data['custid'] = $cust_id;
        }

        $customer = \Model\Customer::create($data);

        return $customer->id;

    }

    /**
     * Update Customer
     *
     * Updates a customer
     *
     * @return null
     * 
     * @param int $id The customer's DB id
     * @param array $data The data to update
     */
    function update_customer($id,$data) {
        $customer = \Model\Customer::find($id);


        foreach($data as $key => $value) {
            $customer->$key = $value;
        }

            $customer->save();
    } 
}
