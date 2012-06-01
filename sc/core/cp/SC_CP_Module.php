<?php
/**
 * Control Panel Module Parent Class File
 *
 * @package Control Panel
 */

/**
 * Control Panel Module Parent Class
 *
 * @package Control Panel
 */
class SC_CP_Module {

    function __construct() {
        global $SC;
        
        $this->SC = &$SC;         
        
        $methods = get_class_methods($this);     
        $mod_class = get_class($this); 
        $module_name = explode('\\',$mod_class);
        $module_name = $module_name[1];
        
        $this->methods = array();
        
        foreach ($methods as $key => &$method) {
            if (substr($method,0,1) == '_') {
                unset($methods[$key]);
                continue;
            }
            
            if ($method == 'index') {                                                           
                continue;                
            } else {
                $this_method['name'] = $method;
                $this_method['readable'] = ucwords(str_replace('_',' ',$method)); 
            }                                                                                 
           
            $this->methods[] = $this_method;                         
        }               
        
        if (count($this->methods)>1) {   
            array_unshift($this->methods,array(
                'name' => '',
                'readable' => $mod_class::$readable_name.' Home'
            ));                              
        }
    }
    
    function index() {
        $methods = $this->methods;
        if (count($this->methods)>1) {
            $methods = array_shift($methods);   
            $this->SC->CP->load_view('module_home',array('methods'=>$this->methods));
        } else {    
            $this->$methods[0]['name']();
        }
    }
    
    function _load_module_menu() {
        if (count($this->methods)>1) { 
            $SC->load_library('URI');
            $SC->CP->load_view('module_menu',array('methods'=>$this->methods));
        }
    }

}
