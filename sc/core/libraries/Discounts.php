<?php
/**
 * Discounts Library
 *
 * This library handles the CRUD and handling of discounts
 *
 * @package Checkout
 */    
namespace Library;
/**
 * Discounts Library Class
 *
 * @package Checkout
 */
class Discounts extends \SC_Library {
    
    /**
     * Get Discount
     *
     * Looks up the provided discount code and returns information about it
     *
     * @return array Returns an assoc array with the following possible keys: 
     * 'type', 'description', 'single', 'item', 'percent', 'amount', 'bamount',
     * 'gamount'
     *
     * @param string $code The discount code
     */
    function get_discount($code) {            
        $discount = \Model\Discount::first(array(
            'conditions' => array('code = ? AND (expires AND expires<'.time().')=FALSE',$code)
        ));	                        

        //('percentoff','fixedoff','bxgx','itempercentoff','itemfixedoff')
        if (!$discount) {
	        return FALSE;        
        }
        
        $return['type'] = $discount->action;
        $return['description'] = $discount->desc;

        switch ($discount->action) {
	        case 'itempercentoff':
		        $return['single'] = TRUE;
		        list($return['item'],$return['percent']) = explode('-',$discount->value); 
	        break;
	
	        case 'itemfixedoff':
		        $return['single'] = TRUE;
		        list($return['item'],$return['amount']) = explode('-',$discount->value);
	        break;
	
	        case 'bxgx':
		        $return['single'] = TRUE;
		        $discount_value = explode(',',$discount->value);			        
		        $return['item'] = $discount_value[0];
		        $return['bamount'] = $discount_value[1];
		        $return['gamount'] = $discount_value[2];
	        break;
	
	        case 'percentoff':
		        $return['single'] = FALSE;
		        $return['percent'] = $discount->value;
	        break;
	
	        case 'fixedoff':
		        $return['single'] = FALSE;
		        $return['amount'] = $discount->value;
	        break;
        }

        return $return;
    }
    
    /**
     * Create discount
     * @todo
     */
    function create_discount() {
    
    }
    
    /**
     * Delete discount
     * @todo
     */
    function delete_discount() {
    
    }   
    
    /**
     * Run Item Discount
     *
     * Calculates item-level discounts on a cart
     *
     * @return array Returns an array with index 0 being the description of the discount,
     * or any errors pertaining to the discount and index 1 being the modified cart
     *
     * @param string|array $discount Either a discount code, or discount array
     * @param string|int|array $cart A valid cart or transaction ID
     */
    function run_item_discount($discount,$cart) {
    
        $this->SC->load_library('Cart');
        
        if (!is_array($discount)) {
            $discount = $this->get_discount($discount);       
        }                        
        
        $cartArray = $this->SC->Cart->explode_cart($cart);          
        
        if (!$discount) {
            return array('Discount doesn\'t exist',$cartArray);
        }         
        
        if (!$discount['single']) {
            return array($discount['description'],$cartArray);
        }                        
        
        $discount_item = FALSE;

        foreach ($cartArray as $key => $item) {
	        if ($item['id'] == $discount['item']) {
		        $discount_item = $key;
		        break;
	        }
        }

        if ($discount_item !== FALSE) {
	        switch($discount['type']) {
		        case 'itempercentoff':
			        if ($cartArray[$discount_item]['quantity'] > 1) {
				        $cartArray[] = array(
				            'id'       => $cartArray[$discount_item]['id'],
				            'options'  => $cartArray[$discount_item]['options'],
				            'quantity' => $cartArray[$discount_item]['quantity'] -= 1,
				            'price'    => $cartArray[$discount_item]['price']
			            );
				        $cartArray[$discount_item]['quantity'] = 1;
			        }
			        $cartArray[$discount_item]['price'] *= (100 - $discount['percent'])*0.01;
		        break;
		
		        case 'itemfixedoff':
			        $cartArray[$discount_item]['price'] -= $discount['amount'];
		        break;
		
		        case 'bxgx':
		            
			        //create an array of all matching items, exploding the qtys
			        $matchingItems = array();
			        foreach($cartArray as $key => $item) {
				        if ($item['id'] == $discount['item']) {
                            $min_amount = $discount['bamount']+$discount['gamount'];
				            if ($item['quantity'] < $min_amount) {
				                return array("This offer requires a quanitity of at least {$min_amount} 
				                        to qualify.",$cartArray);
				            }
					        for ($i=0;$i<$item['quantity'];$i++) {
						        $matchItems[] = array(
						            'id'       => $item['id'],
						            'options'  => $item['options'],
						            'quantity' => 1,
						            'price'    => $item['price']
					            );
					        }
					        unset($cartArray[$key]);
				        }
			        }		
			
			        $pairs = floor( count($matchItems) / ($discount['bamount']+$discount['gamount']) );
			
			        $freeItems = $discount['gamount']*$pairs;
			
			        //Discount items
			        for ($i=0;$i<$freeItems;$i++) {
				        $matchItems[$i]['price'] = 0;
			        }
			
			        //Stack and return items to $cartArray
			
			        $returnToCartArray = array();
			
			        $dontdoit = FALSE;
			        foreach($matchItems as $matchedItem) {
				        foreach($returnToCartArray as $key => $returningItem) {
					        $dontdoit = FALSE;
					        if ($matchedItem['options'] == $returningItem['options'] 
					         && $matchedItem['price'] == $returningItem['price']) {
						        $returnToCartArray[$key]['quantity']++;
						        $dontdoit = TRUE;
						        break;
					        }
				        }
				        if (!$dontdoit) { $returnToCartArray[] = $matchedItem; }
			        }
			
			        $cartArray = array_merge($cartArray,$returnToCartArray);				
		        break;
		
	        }
	        return array($discount['description'],$cartArray);
        } else {            
	        return array('This discount only applies to "'
	            .$this->SC->Items->item_name($discount['item'])
	            .'" which was not found in your cart',$cartArray);
        }	
    }
    
    /**
     * Run Total Discount
     *
     * Calculates a transaction-level discount
     *
     * return float
     *
     * @param string|array $discount A discount code or discount array
     * @param float $total The total from the transaction you would like to calulate
     * the discount on.
     */
    function run_total_discount($discount,$total) {
        if (!is_array($discount)) {
            $discount = $this->get_discount($discount);       
        }           
        
        $return = 0; 
        
        if ($discount) {
        
            switch ($discount['type']) {
	            case 'percentoff':
		            $return = $total * $discount['percent']*0.01;
	            break;
	
	            case 'fixedoff':
		            $return = $discount['amount'];
	            break;
	        }               
	    
	    } 
	    
	    return $return;            
    }
}
