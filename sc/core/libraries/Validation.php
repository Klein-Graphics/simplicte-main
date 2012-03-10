<?php

    namespace Library;
    
    class Validation extends \SC_Library {
    
        public $rules = array();
        public $messages = array();        
        
        public $language = array(
            'required' => 'Field "[display_name]" is required',
            'email' => '"[value]" is not a valid email'
        );
    
        function add_rule($element,$display_name,$rules) {
            $rules = explode('|',$rules);
            
            foreach ($rules as $rule) {
                $this->rules[] = array(
                    'element' => $element,
                    'display_name' => $display_name,
                    'rule' => $rule
                );
            }
        }
        
        function do_validation() {                    
                
            foreach ($this->rules as $rule) {
                if (!isset($_POST[$rule['element']])) {
                    if ($this->is_required($rule['element'])) {
                        $_POST[$rule['element']] = FALSE;
                    } else {
                        continue;
                    }
                }
                     
                if ($this->$rule['rule']($_POST[$rule['element']])===FALSE) {
                    $message = $this->language[$rule['rule']];
                    $message = str_replace('[display_name]',$rule['display_name'],$message);
                    $message = str_replace('[value]',$_POST[$rule['element']],$message);
                    
                    $this->messages[] = $message;
                }
            }
            
            if (count($this->messages) > 0) {
                return FALSE;
            } else {
                return TRUE;
            }
               
        }
        
        function get_messages($delimiter='<br />') {
            return implode($delimiter,$this->messages);
        }
        
        function is_required($element) {
            foreach ($this->rules as $rule) {
                if ($rule['element'] == $element && $rule['rule'] == 'required') {
                    return TRUE;
                }
            }
            
            return FALSE;
        }  
        
        //Specific rule functions
        
        function required($element_value) {
            if ($element_value === FALSE || $element_value === '') {
                return FALSE;
            } else {
                return TRUE;
            }
        }  
        
        function email($element_value) {
            global $CONFIG;
            return (preg_match($CONFIG['EMAIL_REGEX'],$element_value) == 1);        
        }    
    
    }
