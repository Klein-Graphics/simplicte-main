<ul class="nav nav-pills nav-stacked">
<?php foreach ($methods as $method) : ?>
    <li>
        <a href="<?=sc_cp(uri_part(1).'/'.$method['name'])?>" title="<?=$method['readable']?>">
            <h2><?=$method['readable']?></h2>
        </a>
    </li>
<?php endforeach ?>
</ul>
