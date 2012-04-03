<?php

    namespace Library;
    
    class Validation extends \SC_Library {
    
        public $rules = array();
        public $messages = array();        
        
        public $language = array(
            'required' => 'Field "[display_name]" is required',
            'email' => '"[value]" is not a valid email',
            'match' => '"[display_name]" must match',
            'min' => '"Field "[display_name]" must be at least [1]'
        );
    
        function add_rule($elements,$display_name,$rules) {
            $rules = explode('|',$rules); 
            
            $display_name = (array) $display_name;                       
            
            foreach ( (array) $elements as $key => $element) {
                foreach ($rules as $rule) {
                    $this->rules[] = array(
                        'element' => $element,
                        'display_name' => $display_name[$key],
                        'rule' => $rule
                    );
                }            
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
                
                $rule['rule'] = explode(':',$rule['rule']);          
                     
                if (call_user_func_array(
                        array($this,$rule['rule'][0]),
                        array_merge(
                            array($_POST[$rule['element']]),
                            array_slice($rule['rule'],1)
                        )
                    )===FALSE) {
                    $message = $this->language[$rule['rule'][0]];
                    $message = str_replace('[display_name]',$rule['display_name'],$message);
                    $message = str_replace('[value]',$_POST[$rule['element']],$message);
                    
                    preg_match_all('/\[([0-9]+)\]/',$message,$arg_matches);
                    
                    foreach ($arg_matches[1] as &$arg_match) {
                        $arg_match = $rule['rule'][$arg_match];
                    }
                    
                    $message = str_replace($arg_matches[0],$arg_matches[1],$message);
                    
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
            }
                
            return TRUE;
        }  
        
        function email($element_value) {
            global $CONFIG;
            return (preg_match($CONFIG['EMAIL_REGEX'],$element_value) == 1);        
        }    
        
        function match($element_value,$matching_element) {
            if ($element_value != $_POST[$matching_element]) {
                return FALSE;
            }
            
            return TRUE;
        }
        
        function min($element_value,$min) {
            if (count($element_value < $min)) {
                return FALSE;
            }
            
            return TRUE;
        }        
    
    }
