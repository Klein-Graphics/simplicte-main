<?php

/** 
 * Settings Control Panel Module
 *
 * The module responsable for managing the store's settings
 *
 * @package Control Panel
 */
namespace CP_Module;

/**
 * Settings Control Panel Module Class
 *
 * @package Control Panel
 */
class Settings extends \SC_CP_Module {
    public static $readable_name = "Store Settings";
    public static $icon = "wrench";
    public $hidden_pages = array('extension_config','update');    
    
    /**
     * Construct
     */
    function __construct() {
        parent::__construct();
        if ( ! $this->SC->CP_Session->is_master()) {
            unset($this->visible_methods['users']);
        } else {
            unset($this->visible_methods['my_account']);
        }
    }
    
    function my_account() {
    
    }
    
    function _update($method) {
        $method = '_update_'.$method;
        if (method_exists($this,$method)) { 
            $this->$method();
        } else {
            header("HTTP/1.0 404 Not Found");
            echo '<h1>404</h1>Page not found. Unknown update method.';
        }
        
    }             
    
    /**
     * Users Configuration Page
     */
    function users() {
        $users = \Model\User::all();        
        
        if ( ! $this->SC->CP_Session->is_master()) {
            die('You do not have access');
        }
        $this->SC->CP->load_view('settings/view_users',array('users' => $users));
    } 
    
    /**
     * Ajax - Add User
     */
    function _add_user() {
        if ( ! $this->SC->CP_Session->is_master()) {
            header('HTTP/1.1 403 Forbidden');
            die();
        }
        
        if ( ! $_POST['username'] || ! $_POST['email'] || ! $_POST['realname']) {
            die('Please fill out all inputs');
        }
        
        if (\Model\User::count(array('conditions'=>array('username = ?',$_POST['username'])))) {
            die('This username is already in use.');
        }        
        
        $user = new \Model\User();
        
        $user->username = $_POST['username'];
        $user->email = $_POST['email'];
        $user->realname = $_POST['realname'];
        $user->master = (isset($_POST['master']) && $_POST['master']) ? '1' : '0';
        
        $password = rand_string(10);
        
        $user->passwordmd5 = md5($password);
        
        $store_name = $this->SC->Config->get_setting('store_name');
        $cp_url = sc_cp();
        
        $this->SC->Messaging->send_mail(
            $user->email,
            $this->SC->Config->get_setting('sendEmail'),
            'Your Simplecart control panel password account',
            "
            A Simplecart control panel account for $store_name has been created for you.<br>
            <a href=\"$cp_url\" title=\"Control panel\">Login here.</a><br>
            Username: <strong>{$user->username}</strong><br>
            Password: <strong>$password</strong> (You may change it after logging in)<br>
            "
        );
        
        $user->save();
        
        echo 'ok';
        
    }   
    
    /**
     * Ajax - Delete User
     */
    function _delete_user($user) {
        if ( ! $this->SC->CP_Session->is_master()) {
            header('HTTP/1.1 403 Forbidden');
            die();
        }
        
        \Model\user::find($user)->delete();
        
        echo 'ok';
    }
    
    /**
     * Ajax - Modify User
     */
    function _modify_user() {
        if ( ! $this->SC->CP_Session->is_master()) {
            header('HTTP/1.1 403 Forbidden');
            die();
        }
        
        $user = \Model\User::find($_POST['id']);
        
        $fields = array('username','realname','email');
        
        $bad_inputs = array();
        
        if ($_POST['username'] != $user->username 
        && \Model\User::find_by_username($_POST['username'])) {
            $bad_inputs['username'] = 'This username is already in use';
        }                     
        
        foreach ($fields as $field) {
            $user->$field = $_POST[$field];
        }                        
        
        if (isset($_POST['master'])) {
            $user->master = 1;                   
        } else {
            $master_accounts = \Model\User::count('master = 1'); 
            if ($master_accounts < 0 || ($master_accounts == 1 && $user->master == TRUE)) {
                $bad_inputs['master'] = 'At least one master account must exist';
            } else {
                $user->master = 0;
            }
        }
        
        if (isset($_POST['passwordmd5']) && $_POST['passwordmd5']) {
            if ($_POST['passwordmd5'] != $_POST['confirm_password']) {
                $bad_inputs['confirm_password'] = 'Passwords must match!';
            }
        }
        
        if (count($bad_inputs)) {
            die(json_encode(array(
                'ack' => '0',
                'bad_inputs' => $bad_inputs
            )));
        }   
        
        $user->save();
        echo json_encode(array('ack'=>1));                                        
    }
    
    /**
     * Ajax - Reset Password
     */
    function _reset_password($id) {
        if ( ! $this->SC->CP_Session->is_master() && $this->SC->CP_Session->logged_in() != $id) {
            header('HTTP/1.1 403 Forbidden');    
            die();
        }
        
        $new_password = rand_string(10);   
        
        $user = \Model\User::find($id);
        
        $user->passwordmd5 = md5($new_password);
        
        $this->SC->Messaging->send_mail(
            $user->email,
            $this->SC->Config->get_setting('sendEmail'),
            'Your Simplecart control panel password has been reset',
            '<h2>Simplecart</h2>
            Please <a href="'.sc_cp().'" title="Control Panel">login</a> with the following password:'.PHP_EOL
                .$new_password);
        
        $user->save();
        
        echo 'New password was emailed to user';        
    }        
    
    /**
     * Private - Generate Element
     */
    private function generate_element($obj) { 
        if ($obj->type == 'special') return '';
        $element = '<div class="control-group">';
        $element .= "<label class=\"control-label\" for=\"{$obj->detail}\">{$obj->readable}</label>
                     <div class=\"controls\">";
        switch($obj->type) { //Prepends
            case 'percent':
                $element .= '<div class="input-append">';
            break;
            
            case 'currency':
                $element .= '<div class="input-prepend"><span class="add-on">$</span>';
            break;
        }
                             
        switch($obj->type) { //Input itself
            case 'number':            
            case 'email':
            case 'currency':
            case 'text':
                $size = 'span'.ceil(strlen($obj->detail_value)/7);
                $element .= "<input class=\"$size\" type=\"text\" value=\"{$obj->detail_value}\" name=\"{$obj->detail}\" id=\"{$obj->detail}\">";
            break;
            
            case 'percent':
                $size = 'span'.ceil(strlen($obj->detail_value)/7);
                $percent = $obj->detail_value*100;
                $element .= "<input class=\"$size\" type=\"text\" value=\"{$percent}\" name=\"{$obj->detail}\" id=\"{$obj->detail}\">";
            break;
            
            case 'password':
                $element .= "<div><input type=\"password\" placeholder=\"Password\" name=\"{$obj->detail}[password]\" id=\"detail_{$obj->detail}\"></div>
                             <div><input type=\"password\" placeholder=\"Confirm\" name=\"{$obj->detail}[confirm]\"></div>";
            break;                
            
            case 'checkbox':
                $checked = ($obj->detail_value) ? 'checked="checked"' : '';
                $element .= "<label class=\"checkbox\"><input type=\"checkbox\" name=\"{$obj->detail}\" $checked>";
            break;                        
        }
        
        switch($obj->type) { //Appends
            case 'percent':
                $element .= '<span class="add-on">%</span></div>';
            break;
            
            case 'currency':
                $element .= '</div>';
            break;
        }
                   
        $element .= '</div></div>';
        
        return $element;            
    }
    
    /**
     * Private - Prepare elements
     */
    private function prepare_elements($setting,$special_sections=FALSE) {
        $settings = \Model\Detail::all(array('conditions'=>array('category LIKE ?',$setting.'%'))); 
        
        $elements = array();
    
        foreach ($settings as $setting) {
            if ($special_sections !== FALSE   &&
                isset($setting->category[1]) && 
                $setting->category[1] == $special_sections
            ) { 
                                  
                $sections[$setting->category[2]][$setting->detail]['HTML'] = $this->generate_element($setting) ;
                continue;
            }         
                   
            $elements[$setting->detail]['HTML'] = $this->generate_element($setting);
            $elements[$setting->detail]['obj'] = $setting;                                                                       
        }
        
        return ($special_sections !== FALSE) 
                ? array($elements,$sections)
                : $elements;
    }
    
    /**
     * Private - Get update errors
     */
    private function get_update_errors() {
        $errors = array();
        foreach ($this->SC->Validation->bad_inputs as $key => $input) {
            $errors[$key]['name'] = $input;
            $errors[$key]['message'] = $this->SC->Validation->messages[$key];
        }
        
        return $errors;
    }
    
    /**
     * Private - Update fields
     */
    private function update_fields($setting) {
        //Grab all checkboxes from this setting
        $checkboxes = \Model\Detail::all(array('conditions'=>array('category LIKE ? AND type = "checkbox"',$setting.'%')));
        
        $fields = array();
        foreach ($checkboxes as $checkbox) {
            $fields[$checkbox->detail] = 0;
        }
        
        $fields = array_merge($fields,$_POST);
                
        foreach ($fields as $field_name => $new_value) {
            $field = \Model\Detail::find_by_detail($field_name);            
            
            switch($field->type) {
                case 'number':            
                case 'email':
                case 'currency':
                case 'text': 
                case 'password':   
                    $field->detail_value = $new_value;
                break;
                
                case 'percent':
                    $field->detail_value = $new_value * 100;
                break;                                         
            
                case 'checkbox':
                    $field->detail_value = ($new_value === 'on') ? 1 : 0;
                break;
                
                case 'special':
                    continue 2;
                break;
            }
            
            $field->save();
        }                
        
        return TRUE;
        
    }
    
    /**
     * Private - Echo ACK
     */
    private function echo_ACK($ACK=1,$bad_elements=FALSE) {
        $output['ACK'] = $ACK;
        $output['bad_elements'] = $bad_elements;
        
        echo json_encode($output);
    }
    
    /**
     * Basic Store Information
     */
    function basic_store_information() {        
        $elements = $this->prepare_elements('basic');
        
        /**
         * Special elements
         */
        
        // Timezone        
        $elements['timezone']['HTML'] .= '<div class="control-group">
                                            <label class="control-label" for=\"timezone\">Store timezone</label>
                                            <div class="controls">
                                                <select name="timezone" id="timezone">'
                                                    .file_get_contents('core/includes/timezones.html').
                                               '</select>
                                             </div>
                                         </div>
                                         <script type="text/javascript">
                                            $("#timezone").find(\'option[value="'.$elements['timezone']['obj']->detail_value.'"]\').attr("selected","selected");
                                         </script>
                                         ';                                                  
                                         
        
        $this->SC->CP->load_view('settings/config_page',array(
            'page_name' => 'Basic Store Information',
            'elements' => $elements
            ));            
    }        
    
    /**
     * Ajax - Update store information
     */
    function _update_basic_store_information() {
        $this->SC->Validation->add_rule('storename','Store name','required');
        $this->SC->Validation->add_rule('cleanupRate','Order cleanup rate','required|number');
        $this->SC->Validation->add_rule('sendEmail','Outgoing email','required|email');
        
        if (! $this->SC->Validation->do_validation()) {
            $this->echo_ACK(0,$this->get_update_errors());
        }
        
        $this->update_fields('basic');
        
        //Special cases
        $timezone = \Model\Detail::find('timezone');
        $timezone->detail_value = $_POST['timezone'];
        $timezone->save();
        
        $this->echo_ACK();
        
        
    }     
    
    /**
     * Store Display Configuration Page
     */
    function store_display() {        
        $elements = $this->prepare_elements('display');
        
        //Buttons
        $buttons = scandir('core/assets/button');
        $elements['buttons_folder']['HTML'] =  
               '<div class="control-group">
                    <label class="control-label" for="buttons_folder">Button style</label>
                    <div class="controls">
                        <select name="buttons_folder" id="buttons_folder">';
        
        foreach ($buttons as $key => $button) {
            if ( ! is_dir('core/assets/button/'.$button) || strpos($button,'.') === 0) {               
                unset($buttons[$key]);
                continue;
            }
            
            $selected = ($elements['buttons_folder']['obj']->detail_value == $button) ? 'selected="selected"' : '';
            
            $elements['buttons_folder']['HTML'] .=
               "<option value=\"$button\" $selected>
                    ".ucfirst($button)."
                </option>";                        
        }
        
        $elements['buttons_folder']['HTML'] .= 
                       '</select>                    
                        <img class="help-inline" src="'.sc_location('core/assets/button/'.$elements['buttons_folder']['obj']->detail_value.'/add_to_cart.gif').'" title="Button Preview">
                    </div>
                </div>';
                
        $elements['buttons_folder']['HTML'] .= 
           '<script type="text/javascript">
                $("#buttons_folder").change(function() {
                    $(this).next().attr("src","'.sc_location('core/assets/button/').'"+$(this).val()+"/add_to_cart.gif");
                });    
            </script>
            ';
        
        //Item templates
        $templates = scandir('user/item_templates/');
        
        $elements['item_template']['HTML'] = 
           '<div class="control-group">
                <label class="control-label" for="item_template">Item template</label>
                <div class="controls">
                    <select name="item_template" id="item_template">';        
                        
        foreach ($templates as $key => $template) {
            if (is_dir('user/item_templates/'.$template) || strpos($template,'.') === 0) {
                unset($templates[$key]);
                continue;
            }
            
            $template_cont = file_get_contents('user/item_templates/'.$template);
            
            preg_match('/<!--NAME\s*(.*)\s*-->/',$template_cont,$template_name);
            
            $template = basename($template,'.html');
            
            $elements['item_template']['HTML'] .= "<option value=\"$template\">{$template_name[1]}</option>";
        }
        
        $elements['item_template']['HTML'] .=
                   '</select>
                </div>
            </div>';
                     
        
        //Drivers
        function get_drivers($type) {
            $drivers = scandir("core/libraries/{$type}_drivers/");
            $bad_files = array(ucfirst($type).'_Driver.php','.','..');
            
            return array_diff($drivers,$bad_files);
        }
        
        $driver_types = array('cart','checkout','account');
        
        foreach ($driver_types as $type) {
            $elements["{$type}_driver"]['HTML'] =  
               '<div class="control-group">
                    <label class="control-label" for="'.$type.'_driver">'.ucfirst($type).' style</label>
                    <div class="controls">
                        <select name="'.$type.'_driver" id="'.$type.'_driver">';  
            
            $avail_drivers = get_drivers($type);
            
            foreach ($avail_drivers as $driver) {
                $driver = str_ireplace('.php','',$driver);
                $selected = ($elements["{$type}_driver"]['obj']->detail_value == $driver) ? 'selected="selected"' : '';
                
                $elements["{$type}_driver"]['HTML'] .= "<option value=\"$driver\" $selected>$driver</option>";
            }
            
            $elements["{$type}_driver"]['HTML'] .=
               '        </select>
                    </div>
                </div>';  
                        
        }                                                  
    
        $this->SC->CP->load_view('settings/config_page',array(
            'page_name' => 'Store Display',
            'elements' => $elements
        )); 
    
    }
    
    /**
     * Update store display     
     */
     
    function _update_store_display() {
        foreach($_POST as $field_name => $new_value) {
            $field = \Model\Detail::find($field_name);
            $field->detail_value = $new_value;
            $field->save();
        }
        
        $this->echo_ACK();  
    }
    
    /**
     * Payment Configuration Page
     */
    function payment_configuration() {        
        list($elements,$sections) = $this->prepare_elements('payment','driver');                                                   
        
        $elements['paymentmethods']['HTML'] = 
            '<div class="control-group">
                <label class="control-label">Enabled Payment Methods</label>
                <div class="controls">';
        
        $enabled_gateways = explode('|',$this->SC->Config->get_setting('paymentmethods'));
        
        foreach ($enabled_gateways as $key => $enabled_gateway) {
            list($driver,$name) = explode(',',$enabled_gateway);
            
            unset($enabled_gateways[$key]);
            $enabled_gateways[$driver] = $name;
        }
                
        foreach ($this->SC->Gateways->get_all_drivers() as $driver => $name) {
            if (isset($enabled_gateways[$driver])) {
                $name = $enabled_gateways[$driver];
                $checked = 'checked="checked"';
            } else {
                $checked = '';
            }
            
            $elements['paymentmethods']['HTML'] .= 
               "<label>$driver
                    <input type=\"checkbox\" name=\"driver[$driver][enabled]\" $checked>
                </label>
                <input type=\"text\" name=\"driver[$driver][display]\" value=\"{$name}\">";
        }
        
        $elements['paymentmethods']['HTML'] .=
            '   </div>
             </div>';
        $this->SC->CP->load_view('settings/config_page',array(
            'page_name' => 'Payment Configuration',
            'elements' => $elements,
            'sections' => $sections
            ));  
    }
    
    /**
     * Update Payment Configuration
     */
    
    function _update_payment_configuration() {
        print_r($_POST);
    }
    
    /**
     * Shipping Configuration Page
     */
    function shipping_configuration() {    
        list($elements,$sections) = $this->prepare_elements('shipping','driver');                
        
        $enabled_drivers = $this->SC->Shipping->shipping_drivers;
        
        $elements['shipping_drivers']['HTML'] = 
            '<div class="control-group">
                <label class="control-label" for="shipping_drivers">Enabled Shipping Methods</label>
                <div class="controls">
                    <table class="clean_table"><tr><td>';
        $shipping_drivers = $this->SC->Shipping->get_all_drivers();
        $num_of_methods = count($shipping_drivers,TRUE);
        
        $col = min($num_of_methods/10,5);
        
        $per_row = $num_of_methods/$col;
        
        $i = 1;
        foreach ($shipping_drivers as $driver_name => $methods) {
            foreach ($methods as $method_code => $method_name) {
                $checked = (isset($enabled_drivers[$driver_name]) && array_search($method_code,$enabled_drivers[$driver_name]) !== FALSE ) 
                            ? 'checked="checked"' 
                            : '';                
                            
                $elements['shipping_drivers']['HTML'] .= 
                        "<label><input type=\"checkbox\" value=\"$driver_name|$method_code\" $checked> $driver_name -&gt $method_name</label>";   
                
                if ($i++ % $per_row == 0) {
                    $elements['shipping_drivers']['HTML'] .= '</td><td>';
                }
            }
        }
        
        $elements['shipping_drivers']['HTML'] .=
           '        </td></tr></table>    
                </div>
            </div>';
            
        $this->SC->CP->load_view('settings/config_page',array(
            'page_name' => 'Shipping Configuration',
            'elements' => $elements,
            'sections' => $sections
            ));
    }              
    
    /**
     * Extentions Configuration Page
     */
    function extensions() {
        $this->SC->CP->load_view('settings/extensions',array(
            'extensions' => $this->SC->extensions
            ));   
    }
    
    /**
     * Load a specific extention page
     */            
    function extension_config($extension) {
        if (file_exists("extensions/$extension/cp.php")) {
            include_once("extensions/$extension/cp.php");
        }
    }
}
