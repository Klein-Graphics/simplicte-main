<div class="sc_create_account_area">
    <div class="sc_close_link">[x]</div>
    <form action="<?=sc_ajax('do_change_password')?>" class="sc_account_form" method="POST">
        <table>
            <tr>
                <td><label class="sc_label_right">Current Password:</label></td>
                <td><input type="password" name="sc_old_password" /></td>
            </tr>
            <tr>
                <td><label class="sc_label_right">New Password:</label></td>
                <td><input type="password" name="sc_new_password" /></td>
            </tr>
            <tr>
                <td><label class="sc_label_right">Confirm New Password:</label></td>
                <td><input type="password" name="sc_confirm_password" /></td>
            </tr>
        </table>
        <input type="submit" id="sc_register_submit" value="Change" />
    </form>
    <div class="sc_display"></div>
</div><!-- #sc_create_account_area -->
