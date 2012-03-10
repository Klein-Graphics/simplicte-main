<?php

  namespace Library;
  
  class Customer extends \SC_Library {
    
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
    
    function create_customer($cust_id,$data=FALSE) {
      if (is_array($cust_id) && ! $data) {
        $data = $cust_id;
      } else {
        $data['custid'] = $cust_id;
      }
      
      $customer = \Model\Customer::create($data);
      
      return $customer->id;
      
    } 
    
    function get_cart($cust_id) {
    
    }
    
  }
