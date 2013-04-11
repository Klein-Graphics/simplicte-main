<?php $this->load_library('Session') ?>
<div class="sc_create_account_area">
    <div class="sc_close_link">[x]</div>
    <form action="<?=sc_ajax('do_get_email')?>" class="sc_account_form" method="POST">
        <label class="sc_label_right">Email:</label><br />
        <input type="text" name="sc_register_email" value="<?=($user = $this->Session->get_user()) ? \Model\Customer::find($user)->email : '' ?>"/><br />
        <input type="submit" id="sc_register_submit" value="continue" />
    </form>
    <div class="sc_display"></div>
</div><!-- #sc_create_account_area -->
