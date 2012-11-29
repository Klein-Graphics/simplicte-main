<?php

namespace Model;

class Discount extends \SC_Model {

    function get_what_it_does() {
        $what_it_do = '';
        switch($this->action) {
            case 'percentoff':
                $what_it_do = '%'.$this->value.' off of purchase total';
            break;
            
            case 'fixedoff':
                $what_it_do = '$'.$this->value.' off of purchase total';
            break;
            
            case 'itempercentoff':
                $value = explode('-',$this->value);
                $what_it_do = $value[1].'% off of '.\Model\Item::find($value[0])->name;
            break;
            
            case 'itemfixedoff':
                $value = explode('-',$this->value);
                $what_it_do = '$'.$value[1].' off of '.\Model\Item::find($value[0])->name;
            break;
            
            case 'bxgx':
                $value = explode(',',$this->value);
                $what_it_do = 'Buy '.$value[1].' of '.\Model\Item::find($value[0])->name.' and get '.$value[2].' free';
            break;        
        }
        $what_it_do .= '.';
        
        //Modifiers;
        if (!empty($this->modifiers)) {
            $what_it_do .= ' ';
            foreach ($this->modifiers as $modifier => $value) {
                switch ($modifier) {
                    case 'free_shipping':
                        $what_it_do .= 'With free shipping. ';    
                    break;
                }
            }
        }        
        
        return $what_it_do;
    }
    
    function get_readable_expire() {
        return $this->expires ? date('m/d/Y H:i',$this->expires) : 'Never';
    }
    
    function get_modifiers() {
        return json_decode($this->read_attribute('modifiers'),true);
    }
    
    function set_modifiers($modifiers) {
        $this->assign_attribute('modifiers',json_encode($modifiers));
    }       


}
