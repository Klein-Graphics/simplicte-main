<?php

/** 
 * Transactions Control Panel Module
 *
 * The module responsable for managing transactions
 *
 * @package Control Panel
 */
namespace CP_Module;

/**
 * Transactions Control Panel Module Class
 *
 * @package Control Panel
 */
class Stock extends \SC_CP_Module {
    public static $readable_name = "Items and Stock";
    public static $display_image = "assets/img/stock.jpg";
    
    function view_items() {
        $items = \Model\Item::all();
        
        $this->SC->CP->load_view('stock/view_items',array('items'=>$items));
    }
    
    function build_shipment() {
    
    }
    
    private function _prepare_option_output($data) {
        $cells = array();
        $output['row'] = $data['optorder'];
        $n = $data['row_id'];
        
        $output['cat'] = $data['cat'];
        
        $extra_data = "
            <input type=\"hidden\" name=\"options[$n][optorder]\"  value=\"{$data['optorder']}\" />
            <input type=\"hidden\" name=\"options[$n][id]\" value=\"{$data['id']}\" />
        ";                
        
        if ($data['image']) {
            $thumbnail = explode('/',$data['image']);
            $thumbnail[count($thumbnail)-1] = 'thumbnails/'.$thumbnail[count($thumbnail)-1];
            $thumbnail = implode('/',$thumbnail);
        } else {
            $thumbnail = sc_asset('img','Question-Mark.gif');
        }   
            
        $show_cat = ($data['name'] != $data['cat']) ?
            $data['cat'].': ' : '';    
        
        $cells['image'] = "<td><img src=\"$thumbnail\" alt=\"Thumbnail\" /><input type=\"hidden\" name=\"options[$n][image]\" value=\"{$data['image']}\" /></td>";
        $cells['name'] = "<td>
            $show_cat{$data['name']}            
            <input type=\"hidden\" value=\"{$data['name']}\" name=\"options[$n][name]\" />
            <input type=\"hidden\" value=\"{$data['cat']}\" name=\"options[$n][cat]\" />
            </td>";
        
        if ($data['weight']) {    
            $show_weight = ($data['weight'] < 1) ?
                ($data['weight'] * 16).' oz':$data['weight'].' lbs';
            
            $cells['weight'] = "<td>$show_weight<input type=\"hidden\" value=\"{$data['weight']}\" name=\"options[$n][weight]\" /></td>";
            
            unset($data['weight']);
        }                    
        
        unset($data['optorder'],$data['id'],$data['image'],$data['name'],$data['cat']);
        
        foreach ($data as $key => $value) {
            if ($key != 'flags') {
                $cells[$key] = "<td>$value<input type=\"hidden\" value=\"$value\" name=\"options[$n][$key]\" /></td>";
            }            
        }
        
        
        $flags = array();
        
        if (isset($data['flags']) && $data['flags']) {
            //Adjust flags
            foreach ($data['flags'] as $flag) {
                if ($flag['flag']) {
                    $flags[] = $flag['flag'].':'.$flag['args'];
                }
            }	
        }      

        $flags = implode(',',$flags);
        $show_flags = str_trunc($flags, 6, '');
        $flags = "<td rel=\"tooltip\" title=\"$flags\">$show_flags<input type=\"hidden\" name=\"options[$n][flags]\" value=\"$flags\" />$extra_data</td>";
        
        $code = '<td><i class="icon-chevron-up move-up"></i>
                 <i class="icon-chevron-down move-down"></i>
                 <i class="icon-remove delete-row"></i>
                 <i class="icon-pencil edit-option"></i></td>';
       
        $output['content'] = '<tr>'
            .$cells['code']
            .$cells['name']
            .$cells['price']
            .$cells['weight']
            .$cells['image']
            .$cells['stock']
            .$flags
            .$code.'</tr>';                                   
                    
        return $output; 
    }
    
    function _prepare_option() {
        echo json_encode($this->_prepare_option_output($_POST)); 
    }       
    
    function _edit_item($item) {
        $item = \Model\Item::find($item);
        $options = \Model\Itemoption::all(array(
            'itemid' => $item->id
        ));
        
        $sort = array();
        foreach ($options as &$option) {
            $sort[] = $option->optorder;                        
        }        
        
        array_multisort($sort,$options);
        
        foreach ($options as $row => &$option) {
            $option = $this->_prepare_option_output($option->to_array()+array('row_id' => $row));
        }
        
        if ($item->image) {
            $the_image = (strpos($item->image,'http') === FALSE) ?
                site_url($item->image) : $item->image;    
            $thumbnail = explode('/',$the_image);
            $thumbnail[count($thumbnail)-1] = 'thumbnails/'.$thumbnail[count($thumbnail)-1];
            $thumbnail = implode('/',$thumbnail);
        } else {
            $thumbnail = sc_asset('img','Question-Mark.gif');
        }
        
        $thumbnail = "<img src=\"$thumbnail\" title=\"{$item->name}\" />";        
                                
        echo json_encode(array('item'=>$item->to_array(),'options'=>$options,'item_thumb'=>$thumbnail));        
    
    }
    
    function _update_item() {
        //Check if the item exists
        if ($_POST['id']) {
            $item = \Model\Item::find($_POST['id']);
            unset($_POST['id']);
        } else {
            $item = new \Model\Item();
        }
        
        if (isset($_POST['flags'])) {        
            $item->flags = '';
            
            foreach ($_POST['flags'] as &$flag) {
                $flag = implode(':',$flag);
            }
            
            $item->flags = implode(',',$_POST['flags']);
        }
        
        foreach ($_POST as $name => $value) {
            if (!is_array($value)) {
                $item->$name = $value;
            }
        }
        
        $item->save();
        
        if (isset($_POST['options'])) {
            foreach ($_POST['options'] as $option_data) {
                if ($option_data['id']) {
                    $option = \Model\Itemoption::find($option_data['id']);
                    unset($option_data['id']);
                } else {
                    $option = new \Model\Itemoption();
                }
                
                foreach($option_data as $name => $value) {
                    $option->$name = $value;
                }
                
                $option->itemid = $item->id;
                
                $option->save();
            }   
        }
        
        $i = $item;
        
        ?>
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
        <?php        
        
    }
    
    function _delete_item($item) {
        $item = \Model\Item::find($item);
        $item->delete();
        
        $options = \Model\Itemoption::all(array(
            'itemid' => $item->id
        ));
        
        foreach ($options as $option) {
            $option->delete();
        }
    
    }
}
