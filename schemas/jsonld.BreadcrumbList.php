<?php
class JsonLDBreadcrumbList extends WireData {    
    public function __construct() {
    }
    
    public static function getSchema (array $data = null, Page $page = null) {
        // Breadcrumbs - accommodates flat .html page segments if page_url field present
        
        $out = array();
        $home = wire('pages')->get(1);
        $page = wire('page');
                     
        // for flat urls with a page field of page_url   
        $pageURL  = !empty($page->page_url) ? $home->httpUrl . $page->page_url : $page->httpUrl; 
        
        if(strlen($page->parents()) > 0) {
    
            $listItems = array();
            $positionCounter = 1;
    
            foreach($page->parents() as $parent) {
                
                $parentURL   = !empty($page->page_url) ? $home->httpUrl : $parent->httpUrl;
                $parentTitle = !empty($page->page_url) ? $home->title   : $parent->title;
    
                $listItems[] = array (
                    "@type" => "ListItem",
                    "position" => $positionCounter,
                    "item" => array (
                        "@id" => $parentURL,
                        "name" => $parentTitle
                    )
                );
    
                $positionCounter++;
    
            }
            $out["@context"]      =  "http://schema.org";
            $out["type"]          =  "BreadcrumbList";
            $out['itemListElement'][] = $listItems;
        }
         $out = array_filter($out);   
         return $out;
    
    }
}    
?>