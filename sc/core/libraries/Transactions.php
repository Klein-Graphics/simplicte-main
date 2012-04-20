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
    

            return $transaction->save();

          
        }
        
        function delete_transaction($id) {
            
            $transaction = \Model\Transaction::find($id);
            
            $transaction->delete();
            
            return TRUE;   
            
        }

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
