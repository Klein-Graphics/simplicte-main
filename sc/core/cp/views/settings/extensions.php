<h1>Installed Extensions</h1>
<?php foreach ($extensions as $extension) : ?>
    <div class="row">
        <div class="span offset1 cp_module">
            <a href="<?=sc_cp('settings/extension_config/'.$extension)?>" title="<?=ucfirst($extension)?>">
                <h3><?=ucfirst($extension)?></h3>
            </a>
        </div>
    </div>
<?php endforeach ?>
