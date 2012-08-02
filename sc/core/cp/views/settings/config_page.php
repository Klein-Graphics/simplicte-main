<h1><?=$page_name?></h1>
<form class="form-horizontal" action="<?=sc_cp('settings/update/'.$this->SC->URI->get_data(1))?>">
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
        <input class="brn btn-primary" type="submit" value="Save"><?=ajax_loader()?><span class="message"></span>
    </div>
</form>
<script type="text/javascript">
    $('form').submit(function(e){
        e.preventDefault();
        
        $('.cp-ajax-loader').show();
        $('.control-group.error').removeClass('error').popover('disable');
        $.post($(this).attr('action'),$(this).serialize(),function(data) {
            $('.cp-ajax-loader').hide();
            if (data.ACK) {
                $('.message').html('Saved!').delay(500).fadeout();                              
            } else {
                for (var i=0;i<data.bad_elements.length;i++) {
                    $('input[name="'+data.bad_elements[i].name+'"]').closest('.control-group')
                        .addClass('error')
                        .popover({
                            title: 'Please correct:',
                            content: data.bad_elements[i].message                       
                        });
                }
            }
        },'json');
    });
</script>
