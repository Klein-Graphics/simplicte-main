<?php 
    namespace View;
    function login() {
?>
<div class="sc_login_area">
    <span class="close_link">[x]</span>
    <form action="<?=sc_ajax('do_login')?>" class="sc_account_form" method="POST">
        <label for="sc_login_email">Email:</label>
        <input type="text" name="sc_login_email" id="sc_login_email" />
        <label for="sc_login_password">Password:</label>
        <input type="password" name="sc_login_password" id="sc_login_password" />
        <input type="checkbox" name="sc_remember_me" id="sc_remember_me" />
        <label for="sc_remember_me">Remember me on this computer</label>
        <input type="submit" id="sc_login_submit" value="login" />
    </form>
    <div class="sc_display"></div>
</div><!-- #sc_login_area -->

<?php } ?>
