<?php

/**
* The Items library
* 
* This library handles the CRUD of Items and Item Options
* 
* @package Items
*/

namespace Library;

    /**
    * The Items library class
    *
    * @package Items
    */
class Items extends \SC_Library {  

    /**
     * Get All Items
     *
     * Queries the database and returns all items, including their various options
     *
     * @return array An array of items
     */
    function get_all_items() {
        
        $items = \Model\Item::all();

        
        foreach ($items as &$item) {
            $item = $item->to_array();
            $item['options'] = \Model\Itemoption::find('all',array(
                'conditions' => array('itemid =?',$item['id'])
            ));
            foreach ($item['options'] as &$option) {
                $option = $option->to_array();
            }
        }
        
        return $items;
        
    }

    /**
     * Get Item
     * 
     * Gets and returns a specific item
     *
     * @return mixed
     *
     * @param int $id The item's DB id
     * @param string $return_cols The column(s) you want returned, seperated by ','
     */
    function get_item($id,$return_cols='*') {
      
        $item = \Model\Item::find($id,array('select'=>$return_cols));

        return db_return($item,$return_cols);

    }    

    /**
     * Get Item Price
     *
     * An alias to get the item price
     *
     * @return float
     *
     * @param int $id The item's DB id
     */
    function item_price($id) {
        return $this->get_item($id,'price');
    }

    /**
     * Get Item Name
     *
     * An alias to get the item name
     *
     * @return string
     *
     * @param int $id The item's DB id
     */
    function item_name($id) {
        return $this->get_item($id,'name');
    }

    /**
     * Check Item Flag
     *
     * Checks to see if a flag exists on an item, returning false if it doesn't
     * or the flag and its arguments if it does
     *
     * @return mixed
     *
     * @param int $id The item's DB id
     * @param string $check_flag The flag to search for
     */
    function item_flag($id,$check_flag) {
        $item = \Model\Item::find($id,array('select'=>'flags'));

        $flags = explode(',',$item->flags);

        foreach ($flags as $flag) {
            if (strpos($flag,$check_flag)===0) {
                $flag = explode(':',$flag);
                break;
            }
            $flag = false;
        }

        return $flag;
        
    }
    
    /**
     * Get Option Categories
     *
     * Returns a sorted array of option catergories for a specific item
     *
     * @return array
     *
     * @param int $item The item's ID
     */
     function get_option_categories($item) {
        $item_options = \Model\Itemoption::find('all',array(
            'conditions' => array('itemid = ?',$item)
        ));

        $sorted_options = array();                     

        foreach ($item_options as $item_option) {
            
            $sorted_options[$item_option->cat][] = $item_option;
        }         
        
        return $sorted_options;
     }

    /**
     * Get Item Option
     * 
     * Gets and returns a specific item option
     *
     * @return mixed
     *
     * @param int $id The item option's DB id
     * @param string $return_cols The column(s) you want returned, seperated by ','
     */
    function get_option($id,$return_cols='*') {

        $option = \Model\Itemoption::find($id,array('select'=>$return_cols));

        return db_return($option,$return_cols);
    }

    /**
     * Get Item Option Price
     *
     * An alias to get the item option price
     *
     * @return float
     *
     * @param int $id The item option's DB id
     */
    function option_price($id) {
        return $this->get_option($id,'price');
    }

    /**
     * Get Item Option Name
     *
     * An alias to get the item option name
     *
     * @return string
     *
     * @param int $id The item option's DB id
     */
    function option_name($id) {
        return $this->get_option($id,'name');
    } 

    /**
     * Check Item Option Flag
     *
     * Checks to see if a flag exists on an item option, returning false if it 
     * doesn't or the flag and its arguments if it does
     *
     * @return mixed
     *
     * @param int $id The item option's DB id
     * @param string $check_flag The flag to search for
     */
    function option_flag($id,$check_flag) {
        $item = \Model\Itemoption::find($id,array('select'=>'flags'));
        
        $flags = explode(',',$item->flags);
        
        foreach ($flags as $flag) {
            if (strpos($flag,$check_flag)===0) {
                $flag = explode(':',$flag);
                break;
            }
            $flag = false;
        }
        
        return $flag;
        
    }    



}
  
