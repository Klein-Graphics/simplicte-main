<?php

  //----------------------
  //Transaction Functions
  //----------------------
  //
  // These functions handle the CRUD of Transactions
  //
  
  namespace Library;
  
  class Transactions extends \SC_Library {
  
    function get_transaction($search,$return_col='*',$search_col='id') {
      
      $transaction = \Model\Transaction::find(array(
        'select' => $return_col,
        'conditions' => array("$search_col = ?",$search)
      ));
      
      return db_return($transaction,$return_col);
      
    }
  
    function create_transaction($custid,$data=FALSE) {
      if (is_array($custid) && ! $data) {
        $data = $custid;
      } else {
        $data['custid'] = $custid;
      }      
      
      $transaction = \Model\Transaction::create($data);
      $this->generate_order_number($transaction->id,TRUE);
      
      return $transaction->id;
    }
    
    function generate_order_number($id,$update=FALSE) {
      $order_number = date('ymd').str_pad($id%10000,4,'0',STR_PAD_LEFT);
      
      if ($update) {
        $this->update_transaction($id,array('ordernumber'=>$order_number));
      }
      
      return $order_number;
      
      
    }
  
    function update_transaction($id,$attributes) {
      
      $transaction = \Model\Transaction::find($id);
      
      foreach ($attributes as $attribute => $value) {      
        $transaction->$attribute = $value;   
      }
      
      $transaction->save();
      
      return TRUE;
      
    }
    
    
  
  }
