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
        
        return $modules;                        
    }
}
