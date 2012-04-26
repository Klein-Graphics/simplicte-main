<?php

  //----------------------
  // Cart Library
  //----------------------
  //
  // This libary handles the modification and display of the user's cart
  //
  
  namespace Library;
  
  class Cart extends \SC_Library {
     
    public static $required_libraries = array(
      'Transactions',
      'Items'  
    );
  
    function explode_cart($id) {    
    
      if (is_array($id)) {
        return $id;
      }
    
      if (is_numeric($id)) { //Is this an ID or the cart itself?
        $transaction = \Model\Transaction::find($id);
      
        $cart = $transaction->items;
        
      } else {
        $cart = $id;
      }
      
      if ( ! $cart) {
        return array();
      }
      
      $cart = explode('||',$cart);
      
      foreach ($cart as &$item) {
        $item = $this->explode_item($item); 
        
      }
      
      return $cart;
        
    }
    
    function implode_cart($cart,$update=FALSE) {
      
      foreach($cart as &$item) {                    
        $item = $this->implode_item($item);
      }
      
      $cart = implode('||',$cart);
      
      if ($update) {      
        $this->SC->Transactions->update_transaction($update,array('items'=>$cart));
        
        return TRUE;             
      } 
      
      return $cart;      
    
    }
    
    function explode_item($item) {
    
      if (is_array($item)) {
        return $item;
      }
    
      $item = explode('|',$item);
        
      rename_key($item,array('id','options','quantity','price'));
      
      $item['options'] = $this->explode_options($item['options']);      
      
      return $item;
    }
    
    function implode_item($item) {
    
      if (is_string($item)) {
        return $item;
      }
    
      $item['options'] = $this->implode_options($item['options']);
        
      if (!isset($item['price'])) {
        $item['price'] = $this->SC->Items->item_price($item['id']);
      }
      
      $item = implode('|',array($item['id'],$item['options'],$item['quantity'],$item['price']));
      
      return $item;
    
    }
    
    
    
    function explode_options($option_string) {
    
      if (!$option_string) {
        return array();
      }
    
      $option_array = explode(',',$option_string);
        
      foreach($option_array as &$option) {
      
        $option = explode('x',$option);
        $keys = array('id','quantity');
        
        if (isset($option[2])) {
          $keys[2] = 'price';
        }        
        
        rename_key($option,$keys);       
      }
      
      return $option_array;
      
    }
    
    function implode_options($option_array) {
    
      if (!$option_array) {
        return 0;
      }
      
      if (is_string($option_array)) {
        return $option_array;
      }
    
      $option_array = $this->verify_options($option_array);
      
      foreach ($option_array as &$option) {
          $option = implode('x',array($option['id'],$option['quantity'],$option['price']));
      }
      
      $option_string = implode(',',$option_array);
      
      return $option_string;        
    }
    
    function verify_options($option_array) {    
    
      if (!$option_array) {
        return 0;
      }         
    
      foreach ($option_array as &$option) {
        if (!isset($option['price'])) {
          $option['price'] = $this->SC->Items->option_price($option['id']);
        }
      }      
      
      return $option_array;
    }
    
    function item_in_cart($cart,$check_item) {
        $cart = $this->explode_cart($cart);
        $check_item = $this->implode_item($check_item);        
        
        foreach ($cart as $key => $item) {
            if ($check_item == $this->implode_item($item)) {
                return $key;
            }
        }
    }
    
    function add_item($cart,$item,$options='0',$qty=1,$implode=NULL,$update=NULL) {
    
      if (!is_array($cart)) {          
        if (!is_numeric($cart)) {
          $implode = ($implode==NULL) ? TRUE : $implode;
          $update = ($update==NULL) ? FALSE : $update;
        } else {
          $implode = ($implode==NULL) ? TRUE : $implode;
          $update = ($update==NULL) ? $cart : $update;
        }
        
        $cart = $this->explode_cart($cart);
      } else {
        $implode = ($implode==NULL) ? FALSE : $implode;
        $update = ($update==NULL) ? FALSE : $update; 
      }
    
      if (is_array($item)) {
        if (isset($item['implode'])) {
          $implode = $item['implode'];
          unset($item['implode']);
        }
        
        if (isset($item['update'])) {
          $update = $item['update'];
          unset($item['update']);
        }
        
        if (! isset($item['options'])) {
          $item['options'] = 0;
        }        
        
        if (! isset($item['quantity'])) {
          $item['quantity'] = 1;
        }        
        
      } else {                                   
        $item = array(
          'id' => $item,
          'options' => $options,
          'quantity' => $qty   
        );
      
      }
      
      if (!isset($item['price'])) {
        $item['price'] = $this->SC->Items->item_price($item['id']);
      }
                  
      if (!is_array($item['options'])) {
        $item['options'] = $this->explode_options($item['options']);        
      }
        
      $item['options'] = $this->verify_options($item['options']);
            
      if ($the_line = $this->item_in_cart($cart,$item)) {
        $cart[$the_line]['quantity'] += $item['quantity'];
      } else {
            
        $cart[] = $item;
      }
      
      if ($implode) {
        return $this->implode_cart($cart,$update);
      } else {
        return $cart;
      }
      
    }
    
    function remove_item($cart,$line=FALSE,$implode=NULL,$update=NULL) {
      
      if (!is_array($cart)) {          
        if (!is_numeric($cart)) {
          $implode = ($implode==NULL) ? TRUE : $implode;
          $update = ($update==NULL) ? FALSE : $update;
        } else {
          $implode = ($implode==NULL) ? TRUE : $implode;
          $update = ($update==NULL) ? $cart : $update;
        }
        
        $cart = $this->explode_cart($cart);
      } else {
        $implode = ($implode==NULL) ? FALSE : $implode;
        $update = ($update==NULL) ? FALSE : $update; 
      } 
      
      if ($line == FALSE) {
        $cart = array();
      } elseif(is_numeric($line)) {
        unset($cart[$line]);
      } elseif(is_array($line)) {
        foreach($line as $this_line) {
          unset($cart[$this_line]);
        }
      } else {
        foreach($cart as &$item) {
          if ($line == implode_item($item)) {
            unset($item);
          }
        }
      }
      
      if ($implode) {
        return $this->implode_cart($cart,$update);
      } else {
        return $cart;
      }
      
    }
    
    function clear_cart($cart) {
      return $this->remove_item($cart);
    }
    
    function item_count($cart) {
      $cart = $this->explode_cart($cart);
      
      return count($cart);
      
    }
    
    function subtotal($cart) {
      $cart = $this->explode_cart($cart);
      
      $subtotal = 0;
      
      foreach ($cart as $item) {
        $item_total = $this->SC->Items->item_price($item['id']);
        
        foreach ($item['options'] as $option) {
          $option_total = $this->SC->Items->option_price($option['id']);;
          $option_total *= $option['quantity'];
          
          $item_total += $option_total; 
        }
        
        $item_total *= $item['quantity'];
        
        $subtotal += $item_total;
      }
      
      return $subtotal;
              
    }
    
    function line_total($line,$cart = FALSE) {
        if ($cart) {
            if (!is_array($cart)) {          
                $cart = $this->explode_cart($cart);
            }        
            $line = $cart[$line];
        }

        
        $options_price = 0;
        
        foreach ($line['options'] as $option) {
            $options_price += $option['price']*$option['quantity'];
        }
        
        return ($line['price'] + $options_price) * $line['quantity'];
    }
    
    function weigh_cart($cart) {                
        $cart = $this->explode_cart($cart);
        
        $total_weight = 0;
        
        foreach ($cart as $item) {
            $item_weight = 0;
            foreach ($item['options'] as $option) {
                $item_weight += $this->SC->Items->get_option($option['id'],'weight');
            }   

            $item_weight += $this->SC->Items->get_item($item['id'],'weight');                       
                        
            $total_weight += $item_weight;
        }
        
        return $total_weight;
    }
    
    function shipping_required($cart) {
        $cart = $this->explode_cart($cart);        
        
        foreach ($cart as $item) {
            if (!$this->SC->Items->item_flag($item['id'],'digital')) {
                return TRUE;
            }
        }
        
        return FALSE;
    }
    
    function calculate_tax($cart) {
        $cart = $this->explode_cart($cart);
        
        $taxable = 0;
        
        foreach ($cart as $item) {
            $taxable += ($this->SC->Items->item_flag($item['id'],'notax'))
                ? 0
                : $this->line_total($item);                                                
        }
            
        return round($taxable * $this->SC->Config->get_setting('salestax'),2);
    }
    
    function is_empty($cart) {
        $cart = $this->explode_cart($cart);
        
        return empty($cart);
    }
    
    function calculate_total($transaction,$shipping_method=FALSE,$discount_code=FALSE) {
    
        $cart = $this->explode_cart($transaction->items);
    
        $this->SC->load_library(array(
            'Shipping',
            'Discounts'
        ));         
    
        $messages = array();
        
        if ($discount_code) {
            list($messages[],$cart) = $this->SC->Discounts->run_item_discount($discount_code,$cart);
        }
        
        //Calculate shipping

        $shipping = 0;

        $shipping_required = $this->shipping_required($cart);

        if (!$this->SC->Shipping->shipping_enabled && $shipping_required) {
            die ('<span style="color:red; background:white"> Items require shipping, however the store owner 
                  has not enabled it. The they will be notified</span>');
            
            /*
             * TODO: Add notification
             */
        }
        
        if ($shipping_method && $this->SC->Shipping->shipping_enabled && $shipping_required) {             
            list($ship_service,$ship_method) = explode('-',$shipping_method);
            list($ship_status,$ship_foo) = $this->SC->Shipping->Drivers->$ship_service->get_rate_from_cart(
                $this->SC->Config->get_setting('storeZipcode'),
                $transaction->ship_postalcode,
                $ship_method,
                $cart
                );
            if ($ship_status) {
                $shipping = $ship_foo;
            } else {
                $messages[] = $ship_foo;
            }
        }
        
        //Calculate tax
        $billable_states = $this->SC->Config->get_setting('taxstates');
        $billable_states = explode(',',$billable_states);

        $tax = 0;
        if (array_search(strtolower($transaction->ship_state),array_to_lower($billable_states))!==FALSE) {
            $tax = $this->calculate_tax($cart);    
        }
        
        //Calculate total

        $subtotal = $this->subtotal($transaction->items);            

        $total_discount = 0;
        if (isset($_POST['discount'])) {
            $total_discount = $this->SC->Discounts->run_total_discount($_POST['discount'],$subtotal);
        }               
        
        $subtotal -= $total_discount;
        
        return array(
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'taxrate' => $tax,
            'items' => $cart,
            'discount' => $total_discount,
            'total' => $subtotal + $shipping + $tax,
            'messages' => $messages
        );
        
        
    }

    function calculate_soft_total($transaction) {
        if (is_numeric($transaction)) {
            $transaction = $this->get_transcation($transcation);
        }
         
        return 
            $this->subtotal($transaction->items) 
            - $transaction->discount 
            + $transaction->shipping 
            + $transaction->taxrate;
    }
}
  
  
  
  
