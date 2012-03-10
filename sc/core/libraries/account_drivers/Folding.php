<?php 

namespace Account_Driver;
class Folding extends \SC_Account_Driver {

    function add_account_info($input) {
        
        $account_info = '
        <div id="sc_account_info">
            <div id="sc_account_options">
                <a href="'.sc_ajax('edit_account').'" title="Edit Account Infomation" class="sc_account_link">Edit Account</a> - <a href="'.sc_ajax('logout').'" title="Logout" class="sc_logout">Logout</a>
            </div>
            <div id="sc_account_action" style="display:none">
                <form action="'.sc_ajax('do_account_update').'"
            </div><!-- #sc_account_action -->
        </div><!-- #sc_account_info -->
        ';
    
        //Backward compat with depreciated [[custcontrol]] tag
        $input = $this->SC->Page_loading->replace_tag($input,'custcontrol',$account_info);
        $input = $this->SC->Page_loading->replace_tag($input,'account_info',$account_info);
        
        return $input;
        
    }
    
    function add_login($input) {
        
        $account_info = '
        <div id="sc_account_info">
            <div id="sc_account_options">
                <a href="'.sc_ajax('login').'" title="Login" class="sc_account_link">Login</a> - <a href="'.sc_ajax('create_account').'" title="Create a Store Account" class="sc_account_link">Create Account</a>
            </div>
            <div id="sc_account_action" style="display:none">                
            </div><!-- #sc_account_action -->
        </div><!-- #sc_account_info -->
        ';
        //Backward compat with depreciated [[custcontrol]] tag
        $input = $this->SC->Page_loading->replace_tag($input,'custcontrol',$account_info);
        $input = $this->SC->Page_loading->replace_tag($input,'account_info',$account_info);
        
        return $input;
    }
}
