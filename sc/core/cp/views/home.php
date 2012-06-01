<div class="row-fluid">
<?php foreach ($modules as $module) : ?>
    <div class="span2">
    <a href="<?=sc_cp($module['name'])?>" title="<?=$module['readable_name']?>" class="module_selection">
        <img src="<?=$module['display_image']?>" alt="<?=$module['readable_name']?>">
        <h2><?=$module['readable_name']?></h2>
    </a>
    </div>
<?php endforeach ?>
</div>
<p>
    <a href="<?=sc_cp('do_logout')?>" title="Logout">Logout</a>  
</p>
