<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="brand" href="<?=sc_cp('home')?>" title="Control Panel Home">SimplictE</a>    
            <ul id="cp_menu" class="nav">
                <li>
                    <a href="<?=site_url()?>" title="View Store"><i class="icon-eye-open icon-white"></i>
                        View Store
                    </a>    
                </li>
            <?php foreach ($modules as $module) : ?>                                
                <li <?=($this_module == $module['name'])?'class="active"':''?>>    
                    <a 
                        href="<?=sc_cp($module['name'].'/')?>" 
                        title="<?=$module['readable_name']?>"><i class="icon-<?=$module['icon']?> icon-white"></i>
                        <?=$module['readable_name']?>
                    </a>
                </li>
            <?php endforeach ?>
                <li>
                    <a href="<?=sc_cp('do_logout')?>" title="Logout"><i class="icon-share icon-white"></i> Logout</a>  
                </li>
            </ul>
        </div><!--.container-->
    </div><!--.nav-inner-->
</div><!--.navbar-->
