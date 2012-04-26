<?php
    
    namespace Library;
    class Discounts extends \SC_Library {
        
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
        
        function create_discount() {
        
        }
        
        function delete_discount() {
        
        }   
        
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
						        if ($matchedItem['options'] == $returningItem['options'] && $matchedItem['price'] == $returningItem['price']) {
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
		        return array('This discount only applies to "'.$this->SC->Items->item_name($discount['item']).'" which was not found in your cart',$cartArray);
	        }	
        }
        
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
