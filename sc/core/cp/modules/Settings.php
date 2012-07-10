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
    
    function users() {
        $users = \Model\User::all();
        
        $this->SC->CP->load_view('settings/view_users',array('users' => $users));
    }    
    
    function _delete_user() {
    
    }
    
    function _modify_user() {
        if ( ! $this->SC->CP_Session->is_master()) {
            header('HTTP/1.1 403 Forbidden');
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
    
    function basic_store_information() {
    
    }
    
    function payment_settings() {
    
    }
    
    function shipping_settings() {
    
    }       
    
    function extentions() {
    
    }         
}
