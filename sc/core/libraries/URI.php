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

        $this->request = array_shift($query);

        $this->data = $query;

        return array($this->request,$this->data);

    }

    /**
     * Get Request
     *
     * Gets the name of the request method
     *
     * @return string
     */
    function get_request() {
        return $this->request;
    }

    /**
     * Get Data
     *
     * Gets the request data
     *
     * @return string|array
     *
     * @param int $part The request section to grab
     */
    function get_data($part=FALSE) {
        if ($part) {
            return $this->data[$part];
        } else {
            return $this->data;
        }
    }

}
