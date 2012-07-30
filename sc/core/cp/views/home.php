<div id="home">
    <div class="row">
        <div class="span offset1">
            <h1>Simplecart Store Control Panel</h1>
            <h2><?=$store_name?></h2>
        </div>
    </div>
<?php foreach ($modules as $module) : ?>
    <div class="row">
        <div class="span offset1 cp_module">
            <a href="<?=sc_cp($module['name'])?>" title="<?=$module['readable_name']?>" class="module_selection">
                <h3><i class="icon-<?=$module['icon']?>"></i> <?=$module['readable_name']?></h3>
            </a>
        </div>
    </div>
<?php endforeach ?>
    <div class="row">
        <div class="span offset1 cp_module">
            <i class="icon-share"></i> <a href="<?=sc_cp('do_logout')?>" title="Logout">Logout</a>  
        </div>
    </div>
</div><!--#home-->
