<?php

/**
 * Items search
 *
 * Queries the items database and returns a list of matching items. 
 * Must be logged into a Control Panel account for this to work
 */

namespace Ajax\live_search; 
/**
 * Ajax Call for Item Live Search
 *
 * *Ajax Controller Function* Returns an html list of items.
 */
function items($row,$query) {
    global $SC;
    
    if (!$SC->CP_Session->logged_in()) {
        header('HTTP/1.1 403 Forbidden');
        die();
    }        
    
    $results = \Model\Item::all(array('conditions'=>array("$row LIKE ?","%$query%")));
    
    $return = '<ul class="live_search_results">';
    
    if (!empty($results)) {
        foreach ($results as $result) {
            $return .= "<li><a class=\"sc_live_search_result\" href=\"#\" data-id=\"{$result->id}\" title=\"{$result->name}\">{$result->name}</a></li>";
        }
    } else {
        $return .= "<li>No Results</li>";
    }
    
    $return .= '</ul>';
    echo $return;
}
