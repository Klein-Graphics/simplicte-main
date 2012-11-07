<div class="modal hide" id="confirm_delete_modal">
    <div class="modal-header">
        <h3>Are you sure?</h3>
    </div>
    <div class="modal-body">
        This will delete this item, causing any refrences to the item to be invalid. <strong>Only
        do this if you're sure this item hasn't been purchased!</strong>
    </div>
    <div class="modal-footer">
        <button class="btn btn-danger">I'm Sure! Delete it.</button>
        <button class="btn btn-primary" data-dismiss="modal">What? NO! Leave it be!</button>
    </div>
</div>
<div class="modal hide" id="add_items_modal">
    
    <div class="modal-header">
        <button class="close" data-dismiss="modal">&times;</button>
        <h3></h3>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#item" data-toggle="tab">Item</a></li>
            <li><a href="#options" data-toggle="tab">Options</a></li>
        </ul>
    </div>
    <div class="modal-body tab-content">        
        <form class="tab-pane active" id="item" action="<?=sc_cp('Stock/update_item')?>">
            <input type="hidden" name="id" />
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
                <div class="span3 weight-selection">
                    <input type="hidden" name="weight" />
                    <span class="input-append overflow-hidden">
                        <input class="pull-left input-small show_weight" type="text" placeholder="Weight" /><div class="btn-group" data-toggle="buttons-radio">
                            <button class="btn" value=1>Lbs</button>
                            <button class="btn" value=16>Oz</button>
                        </div>          
                    </span> 
                </div>
            </div>                
            <div class="row image-upload">
                <div class="span2">
                    <span class="btn fileinput-button">
                        <span>Upload Image</span>
                        <input class="input-file" type="file" name="files[]" accept="image/*"  data-url="<?=sc_location('file_upload.php')?>"/>                     
                        <input type="hidden" name="image" class="file-location"/>
                    </spn>
                </div>
                <div class="span3">
                    <div class="progress">
                      <div class="bar"
                           style="width: 0%;"></div>
                    </div>
                </div>
                <div class="span1 image-thumbnail"></div>
            </div>
            <div class="row item-stock-selection">
                <div class="span3">
                    <select class="stock-type">
                        <option value="">Normal Stock</option>
                        <option value="inf">Unlimited Stock</option>
                        <option value="opt">Option Dependant Stock</option>                        
                    </select>
                </div><h3></h3>
                <div class="span3">
                    <input type="hidden" name="stock" />
                    <input type="text" class="display_stock" placeholder="Initial Stock"/>                 
                    <select class="stock-option" />
                        <option disabled="disabled" selected="selected" value="">Select an option</option>
                    </select>
               </div>                   
            </div>
            <div class="row">
                <table class="span4 flags">                
                    <thead> 
                        <tr>
                            <td colspan="999">
                                <strong>Flags<strong>
                                <button class="btn btn-mini"><i class="icon-plus-sign"></i></button>
                            </td>
                        </tr>
                        <tr><td>Flag</td><td>Arguments</td></tr>                        
                    </thead>                    
                    <tbody>                      
                    </tbody>                
                </table>
            </div>
        </form><!--#item-->   
        <div class="tab-pane" id="options">
            <form id="add_option" action="<?=sc_cp('Stock/prepare_option');?>">
                <input type="hidden" name="optorder" />
                <input type="hidden" name="id" />
                <input type="hidden" name="row_id" />
                <div class="row">
                    <div class="span3">
                        <input type="text" name="name" placeholder="Name" />
                    </div>
                    <div class="span3">
                        <input type="text" name="code" placeholder="Number" />
                    </div>
                </div>
                <div class="row">
                    <div class="span3">
                        <span class="input-prepend pull-left">
                            <span class="add-on">+$</span><input class="input-small" type="text" name="price" placeholder="0.00" />
                        </span>   
                    </div>
                    <div class="span3 weight-selection">
                        <input type="hidden" name="weight"/>
                        <span class="input-prepend input-append overflow-hidden">
                            <span class="add-on pull-left">+</span><input class="pull-left input-small show_weight" type="text" placeholder="Weight" /><div class="btn-group" data-toggle="buttons-radio">
                                <button class="btn" value=1>Lbs</button>
                                <button class="btn" value=16>Oz</button>
                            </div>          
                        </span> 
                    </div>
                </div> 
                <div class="row">
                    <div class="span4 input-prepend">
                        <span class="add-on">Category</span><input type="text" name="cat" id="option_cat" />
                    </div>
                    <div class="span2">
                        <span class="input-append">
                            <input type="text" name="stock" class="span1" placeholder="Stock"/><button class="btn inf-option-stock">Unlimited</button>
                        </span>
                    </div>
                </div>
                <div class="row image-upload">
                    <div class="span2">
                        <span class="btn fileinput-button">
                            <span>Upload Image</span>
                            <input class="input-file" type="file" name="files[]" accept="image/*" data-url="<?=sc_location('file_upload.php')?>"/>                     
                            <input type="hidden" name="image" class="file-location" />
                        </spn>
                    </div>
                    <div class="span3">
                        <div class="progress">
                          <div class="bar"
                               style="width: 0%;"></div>
                        </div>
                    </div>
                    <div class="span1 image-thumbnail"></div>
                </div>
                <div class="row">
                    <table class="span4 flags">                
                        <thead> 
                            <tr>
                                <td colspan="999">
                                    <strong>Flags<strong>
                                    <button class="btn btn-mini"><i class="icon-plus-sign"></i></button>
                                </td>
                            </tr>
                            <tr><td>Flag</td><td>Arguments</td></tr>                            
                        </thead>                    
                        <tbody>                        
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
                            <tr><td>Number</td><td>Name</td><td>Price</td><td>Weight</td><td>Image</td><td>Stock</td><td>Flags</td><td></td></tr>    
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </fieldset>
            </form>
        </div><!--#options-->
    </div>
    <div class="modal-footer">
        <span class="button-message" id="add_item_message"></span>
        <a href="#" class="btn" data-dismiss="modal">Close</a>
        <a href="#" class="btn btn-primary" id="save_item">Save</a>        
    </div>
</div>

<table class="table table-bordered table-striped" id="items_table">
    <thead>
        <tr><td colspan="999">
            <input type="text" placeholder="Search..." id="search" name="search" />
            <a class="btn" data-toggle="modal" href="#add_items_modal"><i class="icon-plus-sign"></i>Add Item</a>
        </td></tr>
        <tr><td>Number</td><td>Name</td><td>Description</td><td>Price</td><td>Weight</td><td>Image</td><td>Flags</td><td>Stock</td><td></td></tr>
    </thead>
    <tbody>
<?php foreach ($items as $i) : ?>
        <tr data-search="<?=$i->number.' '.$i->name?>" class="item-<?=$i->id?>">
            <td><?=$i->number?></td>
            <td><?=$i->name?></td>
            <td><?=$i->short_description()?></td>
            <td>$<?=$i->price?></td>
            <td><?=$i->formated_weight()?></td>
            <td><?=$i->image_tag()?></td>
            <td><?=$i->display_flags()?></td>
            <td><?=$i->display_stock()?></td>
            <td>
                <button class="btn btn-mini btn-danger delete-item" value="<?=sc_cp('Stock/delete_item/'.$i->id)?>"><i class="icon-remove"></i></button>
                <button class="btn btn-mini btn-primary edit-item" value="<?=sc_cp('Stock/edit_item/'.$i->id)?>"><i class="icon-pencil"></i></button>
            </td>
        </tr>
<?php endforeach ?>        
    </tbody>
</table>
<script type="text/javascript" src="<?=sc_location('core/includes/fileupload/js/vendor/jquery.ui.widget.js')?>"></script>
<script type="text/javascript" src="<?=sc_location('core/includes/fileupload/js/jquery.iframe-transport.js')?>"></script>
<script type="text/javascript" src="<?=sc_location('core/includes/fileupload/js/jquery.fileupload.js')?>"></script>
<script type="text/javascript" src="<?=sc_asset('js','cp/add_item')?>"></script>
