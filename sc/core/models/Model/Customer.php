<?php 

    namespace Model;

    class Customer extends \SC_Model {
        function get_ship_fullname() {
            return  $this->ship_firstname.' '.
                    (($this->ship_initial) 
                        ? $this->ship_initial.' ' 
                        : ''
                    ).
                    $this->ship_lastname;
        }
        
        function get_bill_fullname() {
            return  $this->bill_firstname.' '.
                    (($this->bill_initial) 
                        ? $this->bill_initial.' ' 
                        : ''
                    ).
                    $this->bill_lastname;
        }
       
    }
