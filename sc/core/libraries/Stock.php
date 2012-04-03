<?php

    namespace Library;
    
    class Stock extends \SC_Library {            
        
        function get_item_stock($item) {
            $item = \Model\Item::find($item);                        
            return $item->stock;
        }
        
        function modify_item_stock($item,$amount) {
            $item = \Model\Item::find($item);
            $item->stock = $amount;
            $item->save();
            
            return true;
        }
        
        function add_item_stock($item,$amount) {
            $item = \Model\Item::find($item);
            $item->stock += $amount;
            $item->save();
            
            return true;
        }
        
        function remove_item_stock($item,$amount) {
            return $this->add_item_stock($item,$amount*-1);            
        }    
        
        function item_stock_type($item) {
            $item = \Model\Item::find($item);   
            if ($item->stock == 'inf') {
                return INIFINITE_STOCK;
            } else if (is_numeric($item->stock)) {
                return FINITE_STOCK;
            } else {
                return OPTION_DEPENDANT;
            }
        }    
        
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
        
        function get_option_stock($option) {
            $option = \Model\Itemoption::find($option);
            return $option->stock;
        }
        
        function modify_option_stock($option,$amount) {
            $option = \Model\Itemoption::find($option);
            $option->stock = $amount;   
            $option->save();
            
            return true;
        }
        
        function add_option_stock($option,$amount) {            
            $option = \Model\Itemoption::find($option);
            $option->stock += $amount;
            $option->save();
            
            return true;
        }
        
        function remove_option_stock($option,$amount) {
            return $this->add_option_stock($option,$amount*-1);            
        }    
        
        function option_is_stockable($option) {
            if (is_numeric($this->get_option_stock($option))) {
                return true;
            } else {
                return false;
            }
        }        
        
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
        
    }
