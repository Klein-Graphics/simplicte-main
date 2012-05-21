<?php
/**
 * Stock Library
 *
 * Handles the CRUD and managment of the stock system
 *
 * @package Items
 */
 
namespace Library;

/**
 * Stock Library Class
 *
 * @package Items 
 */
class Stock extends \SC_Library {            
    
    /**
     * Get Item Stock
     *
     * Returns the number of items in stock
     *
     * @return int|string
     *
     * @param int $item The item's id
     */
    function get_item_stock($item) {
        $item = \Model\Item::find($item);                        
        return $item->stock;
    }
    
    /** 
     * Modify Item Stock
     *
     * Changes an item's stock amount to a fixed number
     *
     * @return int The item's new stock
     *
     * @param int $item The item's id
     * @param int|string $amount The new amount
     */
    function modify_item_stock($item,$amount) {
        $item = \Model\Item::find($item);
        $item->stock = $amount;
        $item->save();
        
        return $item->stock;
    }
    
    /**
     * Add Item Stock
     *
     * Adds stock to an item
     *
     * @return int The item's new stock
     *
     * @param int $item The item's id
     * @param int $amount The amount to add
     */
    function add_item_stock($item,$amount) {
        $item = \Model\Item::find($item);
        $item->stock += $amount;
        $item->save();
        
        return $item->stock;
    }
    
    /**
     * Remove Item Stock
     *
     * Removes stock to an item
     *
     * @return int The item's new stock
     *
     * @param int $item The item's id
     * @param int $amount The amount to remove
     */
    function remove_item_stock($item,$amount) {
        return $this->add_item_stock($item,$amount*-1);            
    }    
    
    /**
     * Item Stock Type
     *
     * Returns the item's stock type. Can be const INFINITE_STOCK, FINITE_STOCK, or OPTION_DEPENDANT
     *
     * @return int
     *
     * @param int $item The item's id     
     */
    function item_stock_type($item) {
        $item = \Model\Item::find($item);   
        if ($item->stock == 'inf') {
            return INFINITE_STOCK;
        } else if (is_numeric($item->stock)) {
            return FINITE_STOCK;
        } else {
            return OPTION_DEPENDANT;
        }
    }    
    
    /**
     * Item In Stock?
     *
     * Returns if item is in stock
     *
     * @return bool
     *
     * @param int $item The item's id
     */
    function item_in_stock($item) {
        $item = \Model\Item::find($item);
        
        if ($item->stock == 'inf') {
            return TRUE;
        } else if (is_numeric($item->stock)) {
            return ($item->stock >= 1);
        } else {
            $stock = explode(':',$item->stock);
            $dep_options = \Model\Itemoption::all(array(
                'conditions' => array('cat = ?',$stock[1])
            ));
            $good = FALSE;
            foreach ($dep_options as $option) {
                if ($option->stock > 0) {
                    $good = TRUE;
                    break;
                }
            }
            return $good;
            
        }                            
    }
    
    /**
     * Get Option Stock
     * 
     * Returns the number of an item's option in stock
     *
     * @return int|string
     *
     * @param int $option The option's id
     */     
    function get_option_stock($option) {
        $option = \Model\Itemoption::find($option);
        return $option->stock;
    }
    
    /** 
     * Modify Option Stock
     *
     * Changes an item's option's stock amount to a fixed number
     *
     * @return int The option's new stock
     *
     * @param int $option The option's id
     * @param int|string $amount The new amount
     */
    function modify_option_stock($option,$amount) {
        $option = \Model\Itemoption::find($option);
        $option->stock = $amount;   
        $option->save();
        
        return true;
    }
    
    /** 
     * Add Option Stock
     *
     * Adds to an item's option's stock
     *
     * @return int The option's new stock
     *
     * @param int $option The option's id
     * @param int $amount The amount to add
     */
    function add_option_stock($option,$amount) {            
        $option = \Model\Itemoption::find($option);
        $option->stock += $amount;
        $option->save();
        
        return true;
    }
    
    /** 
     * Remove Option Stock
     *
     * Removes an item's option's stock
     *
     * @return int The option's new stock
     *
     * @param int $option The option's id
     * @param int $amount The amount to remove
     */
    function remove_option_stock($option,$amount) {
        return $this->add_option_stock($option,$amount*-1);            
    }    
    
    /**
     * Option Is Stockable?
     *
     * Checks whether or not the option is stockable or is infinite
     *
     * @return bool
     *
     * @param int $option The option's id
     */
    function option_is_stockable($option) {
        if (is_numeric($this->get_option_stock($option))) {
            return true;
        } else {
            return false;
        }
    }
    
    /** 
     * Option In Stock?
     *
     * Checks whether or not the option is in stock
     *
     * @return bool
     *
     * @param int $option The option's id
     */
    function option_in_stock($option) {
        if ($this->option_is_stockable($option)) {
            if ($this->get_option_stock($option)>0 ) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return TRUE;
        }
    }        
    
    /**
     * Pull Cart
     *
     * Removes a carts items from stock
     *
     * @return null
     *
     * @param int|string|array $cart A valid cart or cart ID
     */
    function pull_cart($cart) {
        $this->SC->load_library('Cart');
        $cart = $this->SC->Cart->explode_cart($cart);        
        
        foreach ($cart as $item) {
            if ($this->item_stock_type($item['id']) === FINITE_STOCK) {
                $this->remove_item_stock($item['id'],$item['quantity']);
            }
            foreach ($item['options'] as $option) {
                if ($this->option_is_stockable($option['id'])) {
                    $this->remove_option_stock($option['id'],$option['quantity']);
                }
            }
        }
        
    }    
    
    /**
     * Return Cart
     *
     * Returns a carts items to stock
     *
     * @return null
     *
     * @param int|string|array $cart A valid cart or cart ID
     */
    function return_cart($cart) {
        $this->SC->load_library('Cart');
        $cart = $this->SC->Cart->explode_cart($cart);
        
        foreach ($cart as $item) {
            if ($this->item_stock_type($item['id']) === FINITE_STOCK) {
                $this->add_item_stock($item['id'],$item['quantity']);
            }
            foreach ($item['options'] as $option) {
                if ($this->option_is_stockable($option['id'])) {
                    $this->add_option_stock($option['id'],$option['quantity']);
                }
            }
        }
    }    
    
    /**
     * Verify Stock
     *
     * Verifies that no items or options are under a certain limit, and if they are reports it
     *
     * @return int[]|array[] If $cart is set, returns an array of the lines that under the limit. 
     * If not, it returns an array where index 0 is and item id's that are below the limit and 
     * index 1 is option id's that are below the limit.
     *
     * @param int $amount The amount to check for
     * @param int|string|array $cart A valid cart or cart id
     */
    function verify_stock($amount,$cart=FALSE) {    
        $bad_items = array();
        $bad_options = array();
            
        if ($cart) {
            $this->SC->load_library('Cart');
            $cart = $this->SC->Cart->explode_cart($cart);
            
            foreach ($cart as $key => $item) {
                $good = TRUE;
                $item_stock = $this->get_item_stock($item['id']);
                              
                if (is_numeric($item_stock) && $item_stock < $amount) {
                    $good = FALSE;
                }                                     
                
                foreach ($item['options'] as $opt_key => $option) {
                    $option_stock = $this->get_option_stock($option['id']);
                    
                    if (is_numeric($option_stock) && $option_stock < $amount) {
                        $good = FALSE;
                    }
                }
                
                if (!$good) {
                    $bad_items[] = $key;
                }
            }   
            
        return $bad_items;
 
        } else {
            $this->SC->load_library('Items');
            $items = $this->SC->Items->get_all_items();
            
            foreach ($items as $item) {
                if (is_numeric($item['stock']) && $item['stock'] < $amount) {
                    $bad_items[] = $item['id'];
                }
                
                foreach ($item['options'] as $option) {
                    if (is_numeric($option['stock']) && $option['stock'] < $amount) {
                        $bad_options[] = $option['id'];
                    }
                }
            }
            
            return array($bad_items,$bad_options);
        }
        
    
    }  
    
}
