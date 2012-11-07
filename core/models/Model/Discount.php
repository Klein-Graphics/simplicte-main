<?php

namespace Model;

class Discount extends \SC_Model {

    function get_what_it_does() {
        switch($this->action) {
            case 'percentoff':
                return '%'.$this->value.' off of purchase total';
            break;
            
            case 'fixedoff':
                return '$'.$this->value.' off of purchase total';
            break;
            
            case 'itempercentoff':
                $value = explode('-',$this->value);
                return '%'.$value[1].' off of '.\Model\Item::find($value[0])->name;
            break;
            
            case 'itemfixedoff':
                $value = explode('-',$this->value);
                return '$'.$value[1].' off of '.\Model\Item::find($value[0])->name;
            break;
            
            case 'bxgx':
                $value = explode(',',$this->value);
                return 'Buy '.$value[1].' of '.\Model\Item::find($value[0]->name).' and get '.$value.' free';
            break;        
        }
    }
    
    function get_readable_expire() {
        return $this->expires ? date('m/d/Y H:i',$this->expires) : 'Never';
    }


}
