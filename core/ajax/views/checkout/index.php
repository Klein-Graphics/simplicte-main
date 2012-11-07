<div class="sc_close_link">[x]</div>

<style type="text/css">
    #sc_checkout .sc_checkout_login .sc_close_link,
    #sc_checkout .sc_checkout_new_account .sc_close_link {
        display: none;
    }    
</style>

<div class="sc_checkout_login">
    If you have already created an account, login below:
    <?php $this->load_ajax('login');?>
</div><!--.sc_checkout_login-->
<div class="sc_checkout_new_account">
    If you don't have an account, you can create one now:
    <?php $this->load_ajax('create_account');?>
</div>
<div class="sc_checkout_no_register">
    <a href="<?=sc_ajax('get_email')?>">Continue without registering</a>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.sc_checkout_no_register a').click(function(e){
            e.preventDefault();
            page_display.checkout.load($(this).attr('href'));
        });
        $('.sc_checkout_login .reset_password').off('click').click(function(e) {
            e.preventDefault();
                
            page_display.checkout.load($(this).attr('href'));
        });
        
    });
</script>
