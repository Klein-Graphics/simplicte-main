<h3>Login</h3>
<form action="<?=sc_location('cp/do_login')?>" method="POST">
    <table>
        <tr>
            <td><label for="login_username">Username:</label></td>
            <td><input type="text" name="login_username" name="login_username" id=/></td>
        </tr>
        <tr>
            <td><label for="login_password">Password:</label></td>
            <td><input type="password" name="login_password" name="login_password" id=/></td>
        </tr>
        <tr>
            <td><input type="submit" value="Login" /></td><td id="login_message"></td>
        </tr>        
    </table>
</form>
<script type="text/javascript">
    $('form').submit(function(e){
        e.preventDefault();
        post_data = $(this).serializeArray();
        post_data[1].value = $().crypt({
                                            method: 'md5',
                                            source: post_data[1].value || 'password'
                                         });
        console.log(post_data);
        $.post($(this).attr('action'),post_data,function(data){
            if (data.ACK) {
                location.reload();
            } else {
                $('#login_message').html('Invalid username or password');
            }  
        },'json');
    });
</script>
