<?php

  /**
   * Cart Library
   *
   * This libary handles the modification and display of the user's cart
   *
   * @package Transactions
   */
  
  namespace Library;
  /**
   * The Cart library class
   *
   * @package Transactions
   */
  class Cart extends \SC_Library {
    
    /**
     * Required libraries
     *
     * @see Transactions\Transactions
     * @see Items\Items
     */ 
    public static $required_libraries = array(
      'Transactions',
      'Items'  
    );
  
    /**
     * Explode Cart
     * 
     * This function takes input pertaining to a cart and returns an 
     * exploded array of that cart. Each item in the cart will contain the
     * following keys: 'id', 'options', 'quantity', 'price'. Options will be an
     * array of options each containing 'id', 'quantity' and 'price' keys.
     *
     * @return array
     *
     * @param int|string|array $cart If $cart is numeric, SC will look up the
     * transaction ID in the database and return the items string. If it's a
     * string, SC will assume that it's the items string and explode it. If it's
     * an array, the function will simply return that same array.
     *
     */
    function explode_cart($cart) { 
                 
      if (is_array($cart)) {
        return $cart;
      }            
    
      if (is_numeric($cart)) { //Is this an ID or the cart itself?
        $transaction = \Model\Transaction::find($cart);
      
        $cart = $transaction->items;        
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
    
    /**
     * Implode Cart
     * 
     * This function takes an exploded cart array and implodes it back to a
     * string, possibly updating the cart in the database
     *
     * @return string|bool
     * 
     * @param array $cart An array of cart items
     * @param int $update The ID of the transaction to update. If set, the
     * function will return that it was successfully updated, other wise it
     * will just return the cart string
     */
    function implode_cart($cart,$update=NULL) {
      
      foreach($cart as &$item) {                    
        $item = $this->implode_item($item);
      }
      
      $cart = implode('||',$cart);
      
      if ($update) {      
        $this->SC->Transactions->update_transaction($update,array('items'=>$cart));            
      } 
      
      return $cart;      
    
    }
    
    /**
     * Explode Item
     *
     * This function takes an item string and explodes it into an array
     *
     * @return string
     *
     * @param array $item The item array
     */
    function explode_item($item) {
    
      if (is_array($item)) {
        return $item;
      }
    
      $item = explode('|',$item);
        
      rename_key($item,array('id','options','quantity','price'));
      
      $item['options'] = $this->explode_options($item['options']);      
      
      return $item;
    }
    
    /**
     * Implode Item
     * 
     * This function takes an item array and implodes it into a string
     *
     * @return array
     *
     * @param string $item The item string
     */
     
    function implode_item($item) {
    
      if (is_string($item)) {
        return $item;
      }
      
      if ($item['quantity'] <= 0) {
        return '';
      }
    
      $item['options'] = $this->implode_options($item['options']);
        
      if (!isset($item['price'])) {
        $item['price'] = $this->SC->Items->item_price($item['id']);
      }
      
      $item = implode('|',array($item['id'],$item['options'],$item['quantity'],$item['price']));
      
      return $item;
    
    }
    
    /**
     * Explode Options
     * 
     * This function takes an option string from an item and explodes it into
     * an array of Option arrays
     *
     * @return array
     *
     * @param string $option_string The option string
     */    
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
    
    /**
     * Implode Options
     * 
     * This function takes an array of option arrays and implodes it into a string
     *
     * @return string
     *
     * @param array $option_array The option array
     */ 
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
    
    /**
     * Verify Options
     * 
     * Verifies an array of options so that the price of each option is correct
     * 
     * @return array
     * 
     * @param array $option_array The option array to verify
     */
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
    
    /**
     * Check If Item In Cart
     *
     * Checks to see if a specific item it contained in the cart.
     *
     * @return int The key of the item if found, returns boolean false if not found
     *
     * @param mixed $cart A valid cart or transaction ID
     * @param mixed $check_item A valid item to check
     */
    function item_in_cart($cart,$check_item) {
        $cart = $this->explode_cart($cart);
        $check_item = $this->explode_item($check_item);
        $check_item['quantity'] = 1;
        $check_item = $this->implode_item($check_item);        
        
        foreach ($cart as $key => $item) {
            $item['quantity'] = 1;
            if ($check_item == $this->implode_item($item)) {
                return $key;
            }
        }
        
        return FALSE;
    }
    
    /**
     * Add Item to Cart
     *
     * Adds an item to the specified cart.
     *
     * @return array|int Returns either the modified cart, the imploded cart, 
     * or that the update was successful
     *
     * @param array|int|string $cart Either the cart array, cart string, or the 
     * ID of the transaction
     * @param array|int $item Either the item id, or an item array
     * @param string $options An option string or option array
     * @param int $qty The quanitity of the item
     * @param bool $implode Whether or not to implode the cart. If $cart is a 
     * string or numeric, then it defaults to true, otherwise it defaults to false
     * @param bool $update Whether or not to update the cart. If $cart is numeric,
     * then it defaults to that number, otherwise it defaults to false 
     *
     */
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
            
      if (($the_line = $this->item_in_cart($cart,$item)) !== FALSE) {
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
    
    /**
     * Remove Item from Cart
     *
     * Removes an item from the specified cart
     *
     * @return array|int Returns either the modified cart, the imploded cart, 
     * or that the update was successful. If no line is supplied, it empties
     * the cart.
     *
     * @param array|int|string $cart Either the cart array, cart string, or the 
     * ID of the transaction
     * @param array|int|string $line Either the line number to remove an array
     * containing multiple lines to remove, or an item string to search for and remove.
     * @param bool $implode Whether or not to implode the cart. If $cart is a 
     * string or numeric, then it defaults to true, otherwise it defaults to false
     * @param bool $update Whether or not to update the cart. If $cart is numeric,
     * then it defaults to that number, otherwise it defaults to false 
     */ 
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
      
      if ($line === FALSE) {
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
    
    /**
     * Clear Cart
     *
     * Clears the cart. An alias for Libaray\Cart::remove_item($cart);
     *
     * @return array|int Returns either an empty array (the emptied cart) or TRUE 
     * if the cart was updated
     *
     * @param array|int|string A valid cart or transaction ID
     */
    function clear_cart($cart) {
      return $this->remove_item($cart);
    }
    
    /**
     * Item count
     *
     * Counts how many items are in a cart.
     *
     * @return float
     *
     * @param array|int|string $cart A valid cart or transaction ID
     */    
    function item_count($cart) {
      $cart = $this->explode_cart($cart);
      
      return count($cart);      
    }
    
    /**
     * Subtotal
     * 
     * Calculates the subtotal of the cart
     *
     * @return float
     *
     * @param array|int|string $cart A valid cart or transaction ID
     */    
    function subtotal($cart) {
      $cart = $this->explode_cart($cart);
      
      $subtotal = 0;
      
      foreach ($cart as $item) {
        $item_total = $item['price'];
        
        foreach ($item['options'] as $option) {
          $option_total = $option['price'];
          $option_total *= $option['quantity'];
          
          $item_total += $option_total; 
        }
        
        $item_total *= $item['quantity'];
        
        $subtotal += $item_total;
      }
      
      return $subtotal;
              
    }
    
    /**
     * Line Total
     *
     * Calculates the total of a specific line or line in a cart
     *
     * @return float
     *
     * @param array|int|string $line Either a valid item, or a specific line number 
     * in a cart
     * @param array|int|string $cart A valid cart or transaction ID
     */
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
    
    /**
     * Weigh Cart
     *
     * Walks through a cart and calculates the weight
     *
     * @return float
     *
     * @param array|int|string $cart A valid cart or transaction ID
     */
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
    
    /**
     * Shipping Required
     *
     * Checks if items are all digitial or shipping may be required
     *
     * @return BOOL
     *
     * @param array|int|string $cart A valid cart or transaction ID
     */
    function shipping_required($cart) {
        $cart = $this->explode_cart($cart);        
        
        foreach ($cart as $item) {
            if (!$this->SC->Items->item_flag($item['id'],'digital')) {
                return TRUE;
            }
        }
        
        return FALSE;
    }
    
    /**
     * Calculate Tax
     *
     * Calulates the tax of the cart
     *
     * @return float
     *
     * @param array|int|string $cart A valid cart or transaction ID
     */
    function calculate_tax($cart) {
        $cart = $this->explode_cart($cart);
        
        $tax = 0;
        
        foreach ($cart as $item) {
            $tax += ($this->SC->Items->item_flag($item['id'],'notax'))
                ? 0
                : round($this->line_total($item) * $this->SC->Config->get_setting('salestax'),2);                                                
        }
            
        return $tax;
    }
    
    /**
     * Is Cart Empty?
     *
     * Checks if a cart is empty
     *
     * @return bool
     *
     * @param array|int|string $cart A valid cart or transaction ID
     */    
    function is_empty($cart) {
        $cart = $this->explode_cart($cart);
        
        return empty($cart);
    }
    
    /**
     * Calculate Total
     *
     * Calculates the grand total for a transaction, including shipping and any discounts.
     * 
     * @return array Returns an array containing the following information: 'subtotal',
     * 'shipping', 'taxrate', 'items', 'discount', 'total', 'messages'
     *
     * @param object $transaction The transaction object
     * @param string $shipping_method The shipping method
     * @param string $discount_code The discount code
     */
    function calculate_total($transaction,$shipping_method=FALSE,$discount_code=FALSE) {        
        $this->SC->load_library(array(
            'Shipping',
            'Discounts',
        ));   
        
        $cart = $this->explode_cart($transaction->items);
    
              
    
        $messages = array();
        
        if ($discount_code) {
            list($messages[],$cart) = $this->SC->Discounts->run_item_discount($discount_code,$cart);
        }
        
        //Calculate shipping

        $shipping = ($shipping_method) ? 0 : FALSE;

        $shipping_required = $this->shipping_required($cart);

        if (!$this->SC->Shipping->shipping_enabled && $shipping_required) {
            die ('<span style="color:red; background:white"> Items require shipping, however the store owner 
                  has not enabled it. The they will be notified</span>');
            
            $this->SC->Messaging->message_store('A customer has attempted to purchase an item requiring shipping, 
            but you have no shipping methods available! <a href="'.sc_cp('Settings/shipping').'" title="Control 
            Panel">Remedy this by navigating to your control panel settings</a> and enabling shipping.',
            'URGENT MESSAGE FROM YOUR eSTORE!');
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
        
        if ($discount_code && $this->SC->Discounts->modifier_isset($discount_code,'free_shipping')) {
            //Check if they have free shipping
            $shipping = 0;
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
	
    /**
     * Calculate Soft Total
     *
     * Uses information from the transaction database to calculate a transaction total
     *
     * @return int
     *
     * @param int|object $transaction A transaction DB object or a transaction id
     */
    function calculate_soft_total($transaction) {
        if (is_numeric($transaction)) {
            $transaction = $this->SC->Transactions->get_transcation($transcation);
        }        
        
        return round( 
            $this->subtotal($transaction->items) 
            - $transaction->discount 
            + $transaction->shipping 
            + $transaction->taxrate,2);
    }

}
  
  
  
  
