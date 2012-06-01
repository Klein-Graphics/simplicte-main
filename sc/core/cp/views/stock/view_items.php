<div class="modal" id="add_items_modal">
    
    <div class="modal-header">
        <button class="close" data-dismiss="modal">&times;</button>
        <h3>Add Item</h3>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#item" data-toggle="tab">Item</a></li>
            <li><a href="#options" data-toggle="tab">Options</a></li>
        </ul>
    </div>
    <div class="modal-body tab-content">        
        <form class="tab-pane active" id="item">
            <div class="row">
                <div class="span3">
                    <input type="text" name="name" placeholder="Name" />
                </div>
                <div class="span3">
                    <input type="text" name="number" placeholder="Number" />
                </div>
            </div>
            <div class="row">
                <div class="span6 control-group">
                    <textarea class="span6" name="description" placeholder="Description"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="span3">
                    <span class="input-prepend pull-left">
                        <span class="add-on">$</span><input class="input-small" type="text" name="price" placeholder="0.00" />
                    </span>   
                </div>
                <div class="span3">
                    <input type="hidden" name="weight" id="weight" />
                    <span class="input-append overflow-hidden">
                        <input class="pull-left input-small" type="text" id="show_weight" placeholder="Weight" /><div class="btn-group" data-toggle="buttons-radio">
                            <button class="btn" value=1>Lbs</button>
                            <button class="btn" value=16>Oz</button>
                        </div>          
                    </span> 
                </div>
            </div>                
            <div class="row">
                <div class="span1">
                    <label for="image_upload"><h3>Image</h3></label>
                </div>
                <div class="span4">
                    <input class="input-file" type="file" id="image_upload" />                     
                    <input type="hidden" name="image" />
                </div>
            </div>
            <div class="row">
                <table class="span4">                
                    <thead> 
                        <tr>
                            <td colspan="999">
                                <strong>Flags<strong>
                                <button class="btn btn-mini" id="item_add_flag"><i class="icon-plus-sign"></i></button>
                            </td>
                        </tr>
                        <tr><td>Flag</td><td>Arguments</td></tr>
                        
                    </thead>                    
                    <tbody>
                        <tr>
                            <td><input type="text" name="flag[0][flag]" /></td>
                            <td><input type="text" name="flag[0][args]" /></td>
                        </tr>                        
                    </tbody>                
                </table>
            </div>
        </form><!--#item-->   
        <div class="tab-pane" id="options">
            <form id="add_option" action="<?=sc_cp('Stock/verify_option');?>">
                <div class="row">
                    <div class="span3">
                        <input type="text" name="option_name" placeholder="Name" />
                    </div>
                    <div class="span3">
                        <input type="text" name="option_number" placeholder="Number" />
                    </div>
                </div>
                <div class="row">
                    <div class="span3">
                        <span class="input-prepend pull-left">
                            <span class="add-on">+$</span><input class="input-small" type="text" name="option_price" placeholder="0.00" />
                        </span>   
                    </div>
                    <div class="span3">
                        <input type="hidden" name="option_weight" id="weight" />
                        <span class="input-prepend input-append overflow-hidden">
                            <span class="add-on pull-left">+</span><input class="pull-left input-small" type="text" id="show_weight" placeholder="Weight" /><div class="btn-group" data-toggle="buttons-radio">
                                <button class="btn" value=1>Lbs</button>
                                <button class="btn" value=16>Oz</button>
                            </div>          
                        </span> 
                    </div>
                </div> 
                <div class="row">
                    <div class="span6 input-prepend">
                        <span class="add-on">Category</span><input type="text" name="option_cat" id="option_cat" />
                    </div>
                </div>
                <div class="row">
                    <div class="span1">
                        <label for="image_upload"><h3>Image</h3></label>
                    </div>
                    <div class="span3">
                        <input class="input-file" type="file" name="image_file" data-url="<?=sc_cp('Stock/upload_file/')?>" id="option_image_upload" />                     
                        <input type="hidden" name="option_image" />
                    </div>
                    <div class="span2">
                        <div class="progress">
                          <div class="bar"
                               style="width: 60%;"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <table class="span4">                
                        <thead> 
                            <tr>
                                <td colspan="999">
                                    <strong>Flags<strong>
                                    <button class="btn btn-mini" id="item_add_flag"><i class="icon-plus-sign"></i></button>
                                </td>
                            </tr>
                            <tr><td>Flag</td><td>Arguments</td></tr>
                            
                        </thead>                    
                        <tbody>
                            <tr>
                                <td><input type="text" name="option_flag[0][flag]" /></td>
                                <td><input type="text" name="option_flag[0][args]" /></td>
                            </tr>                        
                        </tbody>                
                    </table>
                </div>
                <div class="row">
                    <div class="span2">
                        <input type="submit" class="btn btn-primary" value="Add Option"/>
                    </div>
                    <div class="span4" id="add_option_message">
                    </div>
                </div>
            </form>
            <form id="added_options">
                <fieldset>
                    <legend>Options</legend>
                    <table class="table">
                        <thead>
                            <tr><td>Number</td><td>Name</td><td>Price</td><td>Weight</td><td>Image</td><td>Flags</td><td></td></tr>    
                        </thead>
                    </table>
                </fieldset>
            </form>
        </div><!--#options-->
    </div>
    <div class="modal-footer">
        <span class="button-message" id="add_item_message"></span>
        <a href="#" class="btn" data-dismiss="modal">Close</a>
        <a href="#" class="btn btn-primary">Save</a>        
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead>
        <tr><td colspan="999">
            <input type="text" placeholder="Search..." id="search" name="search" />
            <a class="btn" data-toggle="modal" href="#add_items_modal"><i class="icon-plus-sign"></i>Add Item</a>
        </td></tr>
        <tr><td>Number</td><td>Name</td><td>Description</td><td>Price</td><td>Weight</td><td>Image</td><td>Flags</td><td>Stock</td></tr>
    </thead>
    <tbody>
<?php foreach ($items as $i) : ?>
        <tr data-search="<?=$i->number.' '.$i->name?>">
            <td><?=$i->number?></td>
            <td><?=$i->name?></td>
            <td><?=$i->short_description()?></td>
            <td>$<?=$i->price?></td>
            <td><?=$i->formated_weight()?></td>
            <td><?=$i->image_tag()?></td>
            <td><?=$i->display_flags()?></td>
            <td><?=$i->display_stock()?></td>
        </tr>
<?php endforeach ?>        
    </tbody>
</table>
<script type="text/javascript" src="<?=sc_location('core/includes/fileupload/js/vendor/jquery.ui.widget.js')?>"></script>
<script type="text/javascript" src="<?=sc_location('core/includes/fileupload/js/jquery.iframe-transport.js')?>"></script>
<script type="text/javascript" src="<?=sc_location('core/includes/fileupload/js/jquery.fileupload.js')?>"></script>
<script type="text/javascript" src="<?=sc_asset('js','cp/add_item')?>"></script>
