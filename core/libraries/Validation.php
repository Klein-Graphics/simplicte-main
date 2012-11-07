<?php
/**
 * The Validation Library
 *
 * Validates form input
 *
 * @package Validation
 */
namespace Library;

/**
 * Validation Library Class
 *
 * @package Validation
 */
class Validation extends \SC_Library {

    /**
     * The rules to validate
     */
    public $rules = array();
    
    /**
     * Messages to return
     */
    public $messages = array();        
    
    /**
     * Elements that failed to validate
     */
    public $bad_inputs = array(); 
    
    /**
     * Language for each rule
     */
    public $language = array(
        'required' => 'Field "[display_name]" is required',
        'email' => '"[value]" is not a valid email',
        'match' => '"[display_name]" must match',
        'min' => 'Field "[display_name]" must be at least [1] characters',
        'max' => 'Field "[display_name]" must be less than [1] characters',
        'number' => 'Field "[display_name]" must be a number'
    );

    /** 
     * Add Rule
     *
     * Adds a validation rule
     *
     * @return null
     *
     * @param string|array $elements A string of the name of an element to the validation
     * or an array of elements to add the same validation to
     * @param string|array $display_name A string of the human readable name of an element 
     * or an array corresponding to the array of elements
     * @param string $rules Rules to add, delimited with a pipe
     */
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
    
    /**
     * Do Validation
     *
     * Runs the validation, pulling the data from the POST array
     *
     * @return bool Whether or not the validation was successful
     */ 
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
                
                $this->bad_inputs[] = $rule['element'];
                $this->messages[] = $message;
            }
        }
        
        if (count($this->messages) > 0) {
            return FALSE;
        } else {
            return TRUE;
        }
           
    }
    
    /**
     * Get Messages
     *
     * Implodes the messages
     *
     * @return string
     *
     * @param string $delimiter The delimiter to put between the messages
     */
    function get_messages($delimiter='<br />') {
        return implode($delimiter,$this->messages);
    }
    
    /**
     * Is Required
     *
     * Checks if an element is requred
     *
     * @return bool
     *
     * @param string $element The element
     */
    function is_required($element) {
        foreach ($this->rules as $rule) {
            if ($rule['element'] == $element && $rule['rule'] == 'required') {
                return TRUE;
            }
        }
        
        return FALSE;
    }
    
    /**
     * Specific rule functions
     */
    
    /**
     * Required
     * 
     * @return bool
     *
     * @param string $element_value The value of the element to validate
     */
    function required($element_value) {
        if ($element_value === FALSE || $element_value === '') {
            return FALSE;
        }
            
        return TRUE;
    }  
    
    /**
     * Email?
     * 
     * @return bool
     *
     * @param string $element_value The value of the element to validate
     */
    function email($element_value) {
        global $CONFIG;
        return (preg_match($CONFIG['EMAIL_REGEX'],$element_value) == 1);        
    }    
    
    /**
     * Match?
     * 
     * Matches one element against another 
     *
     * @return bool
     *
     * @param string $element_value The value of the element to validate
     * @param string $matching_element The element to match against
     */
    function match($element_value,$matching_element) {
        if ($element_value != $_POST[$matching_element]) {
            return FALSE;
        }
        
        return TRUE;
    }
    
    /**
     * Min?
     *
     * Checks to see if an element is a minumum amount of characters
     *
     * @return bool
     *
     * @param string $element_value The value of the element to validate
     * @param int $min The minumum amount of characters
     */
    function min($element_value,$min) {
        if (count($element_value < $min)) {
            return FALSE;
        }
        
        return TRUE;
    }   
    
    /**
     * Max?
     */    
    function max($element_value,$max) {
   
    }
   
    /**
     * Number?
     *
     * Checks to see if an element is numerical
     *
     * @return bool
     *
     * @param string $element_value The value of the element to validate
     */ 
    function number($element_value) {
        return is_numeric($element_value);
    } 
    

}
