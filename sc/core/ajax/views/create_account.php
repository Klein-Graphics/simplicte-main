<div class="sc_create_account_area">
    <div class="sc_close_link">[x]</div>
    <form action="<?=sc_ajax('do_create_account')?>" class="sc_account_form" method="POST">
        <label class="sc_label_right">Email:</label><br />
        <input type="text" name="sc_register_email" value="<?=($user = $this->Session->get_user()) ? \Model\Customer::find($user)->email : '' ?>"/><br />
        <label class="sc_label_right">Password:</label><br />
        <input type="password" name="sc_register_password"/><br />
        <label class="sc_label_right">Confirm:</label><br />
        <input type="password" name="sc_confirm_password"/><br />
        <input type="submit" id="sc_register_submit" value="signup" />
    </form>
    <div class="sc_display"></div>
</div><!-- #sc_create_account_area -->
