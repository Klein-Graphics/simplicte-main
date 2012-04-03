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
      $item = explode('|',$item);
        
      rename_key($item,array('id','options','quantity','price'));
      
      $item['options'] = $this->explode_options($item['options']);      
      
      return $item;
    }
    
    function implode_item($item) {
      $item['options'] = $this->implode_options($item['options']);
        
      if (!isset($item['price'])) {
        $item['price'] = $this->SC->Items->item_price($item['id']);
      }
      
      $item = implode('|',array($item['id'],$item['options'],$item['quantity'],$item['price']));
      
      return $item;
    
    }
    
    
    
    function explode_options($option_string) {
    
      if (!$option_string) {
        return 0;
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
      
      
      
      $cart[] = $item;
      
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
}
  
  
  
  
