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
     * 'action', 'description', 'single', 'item', 'percent', 'amount', 'bamount',
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
        
        return $this->parse_discount($discount);
    }
    
    /**
     * Parse discount
     *
     * Takes a discount DB object and parses it and returns a pretty array.
     *
     * @return array[] An array containing all the discount's values
     */
    
    function parse_discount($discount) {
        $return = array_intersect_key(
            $discount->to_array(),
            array_flip(array('action','code','expires','id'))
            );                    
        $return['modifiers'] = $discount->modifiers;
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
     * Update discount
     * 
     * Updates or creates a discount. Accepts an array containing the details of the discount. 
     * The keys are as follows:
     * * action - The type of discount. itempercentoff, itemfixedoff, bxgx, percentoff, fixedoff
     * * code - The code the customer will enter at checkout
     * * desc (Optional) - The description of the discount. Will be displayed to the
     *   customer when they enter the discount.
     * * value (Optional) - Either the dollar off amount or the percentage amount (as a percentage i.e. 
     *   50 for 50%, not .5), if applicable.
     * * item (Optional) - The DB ID of the item that discount applies to, if applicable.
     * * bamount (Optional) - The "buy" amount when doing a "buy-x get-y" type discount, if applicable.
     * * gamount (Optional) - The "get" amount when doing a "buy-x get-y" type discount, if applicable.
     * * discount - Unix time of when the discount should expire.
     * * modifiers - Array of modifiers. Key is the modifier name, and the value is whatever you need it to be
     * * id (Optional) - If updating and not creating, you may supply the array to be replaced 
     *
     * @return obj Returns the discount DB object
     */
    function update_discount($in_discount) {
        if ($in_discount['id']) {
            $discount = \Model\Discount::find($in_discount['id']);
        } else {
            $discount = new \Model\Discount();
        }
        
        $discount->action = $in_discount['action'];
        $discount->code = $in_discount['code'];
        $discount->expires = isset($in_discount['expires']) ? $in_discount['expires'] : 0 ;
        $discount->desc = isset($in_discount['desc']) ? $in_discount['desc'] : '';                
        $discount->modifiers = isset($in_discount['modifiers']) ? $in_discount['modifiers'] : array();
        
        switch ($discount->action) {
            case 'itempercentoff':
            case 'itemfixedoff':
                $discount->value = $in_discount['item'].'-'.$in_discount['value'];                            
            break;
            
            case 'percentoff':
            case 'fixedoff':
                $discount->value = $in_discount['value'];
            break;
            
            case 'bxgx':
                $discount->value = $in_discount['item'].','.$in_discount['bamount'].','.$in_discount['gamount'];
            break;
            
            default:
                trigger_error('Invalid discount action in function "create_discount"',E_USER_WARNING);
                return FALSE;
            break;
        } 
        
        $discount->save();                       
        
        return $discount;
    
    }
    
    /**
     * Delete discount
     * 
     */
    function delete_discount($id) {
       \Model\Discount::find($id)->delete();
       
       return true;
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
	        switch($discount['action']) {
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
			        $cartArray[$discount_item]['price'] *= round((100 - $discount['percent'])*0.01);
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
        
            switch ($discount['action']) {
	            case 'percentoff':
		            $return = round($total * $discount['percent']*0.01,2);
	            break;
	
	            case 'fixedoff':
		            $return = $discount['amount'];
	            break;
	        }               
	    
	    } 
	    
	    return $return;            
    }
    
    /**
     * Modifier Isset
     *
     * Checks if the modifier is set for the specific discount
     */   
    function modifier_isset($discount,$modifier) {        
        if (!is_array($discount)) {
            $discount = $this->get_discount($discount);       
        }
        
        echo $discount['modifiers'];
        
        return isset($discount['modifiers'][$modifier]);
    }
    
}
