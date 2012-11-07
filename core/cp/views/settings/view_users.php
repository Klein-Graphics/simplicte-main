<div class="modal modal-small hide span3" id="add_user_modal">
    <form action="<?=sc_cp('Settings/add_user')?>">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h2>New User</h2>
        </div>
        <div class="modal-body">            
                <input type="text" placeholder="Username" name="username">
                <input type="text" placeholder="Full Name" name="realname">
                <input type="text" placeholder="Email Address" name="email">
                <label class="checkbox"><input type="checkbox" name="master"> Master account?</label>
        </div>    
        <div class="modal-footer">
            <div class="message-area"></div>
            <?=ajax_loader()?>
            <input type="submit" class="btn btn-primary add-user" value="Add User" />
            <button class="btn" data-dismiss="modal">Close</button>
        </div>
    </form>
</div>
<table class="table table-bordered table-striped">
    <thead>
        <tr><th colspan="999"><a class="btn" data-toggle="modal" href="#add_user_modal"><i class="icon-plus"></i> New User</button></th></tr>
        <tr><th>User</th><th>Full name</td><td>Email</td><td>Password</td><td>Last login</td><td>Master account</td><td></td></tr>
    </thead>
    <tbody>
<?php foreach ($users as $user) : ?>
        <tr>
            <td data-name="username" class="control-group"><?=$user->username?></td>
            <td data-name="realname" class="control-group"><?=$user->realname?></td>
            <td data-name="email" class="control-group"><?=$user->email?></td>
    <?php if ($user->id == $this->SC->CP_Session->logged_in()) : ?>
            <td class="password control-group">********</td>
    <?php else : ?>
            <td class="no-edit"><a href="<?=sc_cp('Settings/reset_password/'.$user->id)?>" title="Reset Password" class="reset-password">Reset Password</a></td>
    <?php endif ?>
            <td class="no-edit"><?=($user->lastlogin) ? date('m/d/Y h:i',$user->lastlogin) : 'Never'?></td>
            <td class="master-account"><?=($user->master)?'Yes':'No'?></td>
            <td class="no-edit">
                <input type="hidden" name="id" value="<?=$user->id?>" />
                <a href="<?=sc_cp('Settings/modify_user')?>" title="Modify User" class="modify-user"><i class="icon-edit"></i></a>
                <a href="<?=sc_cp('Settings/delete_user/'.$user->id)?>" title="Remove User" class="delete-user"><i class="icon-trash"></i></a>
            </td>
        </tr>
<?php endforeach ?>
    </tbody>
</table>
<script type="text/javascript" src="<?=sc_asset('js','cp/edit_users')?>"></script>
