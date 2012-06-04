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
    
    function _prepare_option() {
        $cells = array();
        $n = $_POST['optorder'];
        $extra_data = "
            <input type=\"hidden\" name=\"options[$n][optorder]\"  value=\"{$_POST['optorder']}\" />
            <input type=\"hidden\" name=\"options[$n][id]\" value=\"{$_POST['id']}\" />
        ";        
        
        if ($_POST['image']) {
            $thumbnail = explode('/',$_POST['image']);
            $thumbnail[count($thumbnail)-1] = '/thumbnails/'.$thumbnail[count($thumbnail)-1];
            $thumbnail = implode('/',$thumbnail);
        } else {
            $thumbnail = sc_asset('img','Question-Mark.gif');
        }   
        
        $cells['image'] = "<td><img src=\"$thumbnail\" alt=\"Thumbnail\" /><input type=\"hidden\" value=\"{$_POST['image']}\" /></td>";
        
        unset($_POST['optorder']);
        unset($_POST['id']);
        unset($_POST['image']);
        
        foreach ($_POST as $key => $value) {
            if ($key != 'flags') {
                $cells[$key] = "<td>$value<input type=\"hidden\" value=\"$value\" name=\"options[$n][$key]\" /></td>";
            }            
        }
        
        $flags = array();
        
        //Adjust flags
        foreach ($_POST['flags'] as $flag) {
            if ($flag['flag']) {
                $flags[] = $flag['flag'].':'.$flag['args'];
            }
        }	

        $flags = implode(',',$flags);
        $flags = "<td>$flags<input type=\"hidden\" name=\"options[$n][flags]\" value=\"$flags\" />$extra_data</td>";
       
        $output['content'] = '<tr>'
            .$cells['code']
            .$cells['name']
            .$cells['price']
            .$cells['weight']
            .$cells['image']
            .$flags
            .'</tr>';
        
        $output['row'] = $n;
        
        echo json_encode($output);
        
        
    }       
    
    function _add_item() {
    
    }
    
    function _edit_item() {
    
    }
}
