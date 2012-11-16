<?php

namespace Model;

class Item extends \SC_Model {
    function short_description($length=60) {
        return html_str_trunc(str_replace('<p>',' ',str_replace('</p>','<br />',strip_tags($this->description,'<br><p>'))),$length);
    }
    
    function formated_weight() {
        return ($this->weight < 1) 
            ? ($this->weight*16).' oz'
            : $this->weight.' lbs';                
    }
    
    function image_tag($addl='') {
        if ($this->image) {
            $image = (strpos($this->image,'http')===FALSE) ?
                site_url($this->image) : $this->image;
            return "<img src=\"".$image."\" alt=\"{$this->name}\" $addl />";
        } else {
            return "";
        }
    }
    
    function display_flags() {
        if (!$this->flags) {
            return 'None';
        }
        return '<ul><li>'.implode('</li><li>',explode(',',$this->flags)).'</li></ul>';                                               
    }
    
    function display_stock() {
        if ($this->stock == 'inf') {
            return 'Unlimited';            
        } else if(is_numeric($this->stock)) {
            return $this->stock;            
        } else {
            $option = explode(':',$this->stock);                
            $option = $option[1];               
            return 'Dependant on Option: '.$option;
        }            
    }        
    
    //Getters        
    function get_price() {
        return round($this->read_attribute('price'),2);
    }

}
