<div class="sc_login_area">
    <div class="sc_close_link">[x]</div>
    <form action="<?=sc_ajax('do_login')?>" class="sc_account_form" method="POST">
        <label class="sc_label_right">Email:</label><br />
        <input type="text" name="sc_login_email"/><br />
        <label class="sc_label_right">Password:</label><br />
        <input type="password" name="sc_login_password"/><br />
        <input type="checkbox" name="sc_remember_me"/>
        <label class="sc_label_left">Remember me</label><br />
        <input type="submit" id="sc_login_submit" value="Login" />
    </form>
    <a href="<?=sc_ajax('reset_password')?>" title="Reset password" class="reset_password">Forgot password?</a>    
    <div class="sc_display"></div>
</div><!-- #sc_login_area -->
<script type="text/javascript">
$(document).ready(function() {
    $('.reset_password').click(function(e){
        e.preventDefault();
                
        page_display.account.load($(this).attr('href'));
    });    
});
</script>

