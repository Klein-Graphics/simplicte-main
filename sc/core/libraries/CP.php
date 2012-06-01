<?php

/**
 * Control Panel Library File
 * 
 * Handles the loading of the control panel modules
 * and their various methods
 * 
 * @package Control Panel
 */
namespace Library;

/**
 * Control Panel Library Class
 *
 * 
 */
 
class CP extends \SC_Library {

    /**
     * Get Modules
     *
     * Returns an array of Modules, containing the name, readable name
     * and display image for that module
     *
     * @return Array[] An array of assoc arrays with keys 'name', 'readable_name',
     * and 'display_image'
     */
    function get_modules() {            
    
        include_once('core/cp/SC_CP_Module.php');
        //Read the modules directory        
        $modules = scandir('core/cp/modules');
        
        foreach ($modules as $key => &$module) {
            $this_module = array();
            $file = pathinfo($module);
            if ($module == '.' 
             || $module == '..' 
             || $file['extension'] != 'php') 
            {
                unset($modules[$key]);
                continue;
            }
            
            include_once ("core/cp/modules/$module");
            $this_module['name'] = $file['filename'];
            $ns_module = "CP_Module\\{$this_module['name']}";
            
            $this_module['readable_name'] = $ns_module::$readable_name;
            $this_module['display_image'] = $ns_module::$display_image;
            $module = $this_module;
        }
        
        usort($modules,function($module1,$module2){
            if ($module1['readable_name'] == $module2['readable_name']) {
                return 0;
            }
            
            $sorted_modules = array($module1['readable_name'],$module2['readable_name']);
            sort($sorted_modules);
            
            return ($module1['readable_name'] == $sorted_modules[0]) ? -1 : 1;
        });
        
        return array_values($modules);                        
    }
    
    /**
     * Module exists
     *
     * Checks if a module exists
     *
     * @return bool
     *
     * @param string $module The name of the module
     */
    function module_exists($module) {
        if (file_exists("core/cp/modules/$module.php")) {
            include_once("core/cp/modules/$module.php");
            $ns_module = "CP_Module\\$module";
            return class_exists($ns_module);                        
        }
        
        return FALSE;
    }
    
    /**
     * Load View
     *
     * Loads a control panel view
     *
     * @return bool Returns whether or not loading was successful
     *
     * @param string $view The name of the view
     * @param array $data Data to pass to the view
     */
    function load_view($view,$data=array()) {
        if (file_exists("core/cp/views/$view.php")) {  
            extract($data);        
            include_once("core/cp/views/$view.php");
            return TRUE;
        } else {
            return FALSE;
        }
    }    
}
