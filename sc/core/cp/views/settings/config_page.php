<h1><?=$page_name?></h1>
<form class="form-horizontal">
<?php foreach($elements as $element) : ?>
    <?=$element['HTML']?>
<?php endforeach ?>
<?php if (isset($sections)) : ?>
    <?php foreach($sections as $name => $section) : ?>
    <h3 class="section-header"><?=$name?></h3>
        <?php foreach($section as $element) : ?>
            <?=$element['HTML']?>
        <?php endforeach ?>
    <?php endforeach ?>
<?php endif?>
    <div class="form-actions">
        <input class="brn btn-primary" type="submit" value="Save">
    </div>
</form>
