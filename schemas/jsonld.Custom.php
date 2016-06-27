<?php

class JsonLDCustom extends WireData  {
    
    public function __construct() {
    }
    
    public static function getSchema (array $data = null, Page $page = null) {    
    // Use this class when you wish build your entire schema in the template
    // Start the $options array in your template as $options = array('custom' => array( PUT YOUR SCHEMA STUFF HERE ));
        $out = array();
        $out["@context"]         = "http://schema.org/";
             
         return $out;
    }    
}