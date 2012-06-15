<?php

class SC_Model extends \ActiveRecord\model {

    private $hooks = array();
        
    public function __construct(array $attributes=array(), $guard_attributes=true, $instantiating_via_find=false, $new_record=true) {                                        
        $class_name = explode('\\',get_class($this));
        
        $class_name = array_pop($class_name);
        
        if (isset($this->SC()->hooks['db'][$class_name])) {
            $this->hooks = $this->SC()->hooks['db'][$class_name];
        } 
          
        parent::__construct($attributes, $guard_attributes, $instantiating_via_find, $new_record);                        
    
    }
    
    public function &SC() {
        global $SC;        
        return $SC;
    }
    
    public function &__get($name) {
        $value = parent::__get($name);                                                                                                             
        //Check for hooks
        if (isset($this->hooks['get_'.$name])) {
            foreach ($this->hooks['get_'.$name] as $hook) {
                $copy = $this;
                $copy->$name = $value;
                
                $value = $hook($this);
            }            
            
        }
        
        return $value;
    }
}
