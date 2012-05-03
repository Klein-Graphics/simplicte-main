<?php
/**
 * URI Library
 *
 * Handles the URI sections and query string
 *
 * @package Core 
 */
namespace Library;

/**
 * URI Library Class
 *
 * @package Core
 */
class URI extends \SC_Library {

    /**
     * Construct
     *
     * Runs the initialization method
     *
     * @return null
     */
    function __construct() {

        parent::__construct();

        //Initialize query string      
        $this->initialize_query_string();
    
    }

    /**
     * Initialize Query String
     *
     * Parses the query string
     *
     * @return array
     */
    function initialize_query_string() {

        $query = $_SERVER['QUERY_STRING'];      
        $query = preg_replace('/^\//','',$query);            

        $query = explode('/',$query);

        $this->view = array_shift($query);

        $this->request = $query;

        return array($this->view,$this->request);

    }

    /**
     * Get View
     *
     * Gets the name of the view
     *
     * @return string
     */
    function get_view() {
        return $this->view;
    }

    /**
     * Get Request
     *
     * Gets the request
     *
     * @return string|array
     *
     * @param int $part The request section to grab
     */
    function get_request($part=FALSE) {
        if ($part) {
            return $this->request[$part];
        } else {
            return $this->request;
        }
    }

}
