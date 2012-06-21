<?php if ($this->Session->has_account()) {
    //Create the account form
    echo $this->Page_loading->account_driver->generate_account_info();    
} else {
    //Create the login form
    echo $this->Page_loading->account_driver->generate_login();
}
