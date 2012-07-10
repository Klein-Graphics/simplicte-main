<table class="table table-bordered table-striped">
    <thead>
        <tr><th colspan="999"></th></tr>
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
            <td class="no-edit"><a href="<?=sc_cp('Settings/reset_password/'.$user->id)?>" title="Reset Password">Reset Password</a></td>
    <?php endif ?>
            <td class="no-edit"><?=date('m/d/Y h:i',$user->lastlogin)?></td>
            <td class="master-account"><?=($user->master)?'Yes':'No'?></td>
            <td class="no-edit">
                <input type="hidden" name="id" value="<?=$user->id?>" />
                <a href="<?=sc_cp('Settings/modify_user')?>" title="Modify User" class="modify-user"><i class="icon-edit"></i></a>
                <a href="<?=sc_cp('Settings/delete_user')?>" title="Remove User"><i class="icon-trash"></i></a>
            </td>
        </tr>
<?php endforeach ?>
    </tbody>
</table>
<script type="text/javascript" src="<?=sc_asset('js','cp/edit_users')?>"></script>
