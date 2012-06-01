 <ul id="module_menu" class="nav nav-tabs">
<?php foreach ($methods as $method) : ?>
    <li <?=(uri_part(2)==$method['name'])?'class="active"':''?>>    
        <a href="<?=sc_cp(uri_part(1).'/'.$method['name'])?>" title="<?=$method['readable']?>"><?=$method['readable']?></a>
    </li>
<?php endforeach ?>
</ul>

