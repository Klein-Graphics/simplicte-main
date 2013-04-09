<?php
  
/**
* Config Library
*
* This library handles the configuration of Simplecart2 and the read and storing of 
* misc. data in the "details" database
*
* @package Config
*/  

namespace Library;

/**
 * The Config class
 *
 * @package Config
 */
class Config extends \SC_Library {

    /**
     * Get Setting
     *
     * Gets a setting or other data-piece from the "details" database
     *
     * @return mixed
     *
     * @param string $name The name of the setting
     */
    function get_setting($name) {       
        if (is_array($name)) {

            $return = array();

            foreach ($name as $this_name) {
                $return[] = $this->get_setting($this_name);
            }

            return $return;
        }
        
        $name = str_replace('_','',$name);  

        $detail = \Model\Detail::find(array( 
            'conditions' => array('upper(replace(`detail`,"_","")) = ?',$name)
        ));

        if (isset($detail->detail_value) && $detail->detail_value) {
            return $detail->detail_value;
        } else {
            return false;
        }

    }    
}
