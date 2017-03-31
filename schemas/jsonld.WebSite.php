<?php

class JsonLDWebSite extends WireData  {
    
    public function __construct() {
    }
    
    public static function getSchema (array $data = null, Page $page = null) {
     
        $out = array();         
         
        $home = wire('pages')->get('/');
        $sanitizer = wire('sanitizer');
        
            
        // for flat urls with a page field of page_url   
        $pageURL  = !empty($page->page_url) ? $home->httpUrl . $page->page_url : $page->httpUrl; 
    
        // Website home page info
        $out["@context"] = "http://schema.org";
        $out["@type"]    = !empty($data["@type"])? $sanitizer->text($data["@type"]) : "WebSite";
        $out["url"]      = $home->httpUrl;
        $out["name"]     = !empty($data["name"]) ? $sanitizer->text($data["name"]) : $home->get('seo_title|headline|title');
        $out["description"] = !empty($data["description"]) ? $sanitizer->textarea($data["description"]) : $home->get('seo_description|summary');
        
        if ($data['logo']) {
            $out["logo"]    = array(
                "@type" => "ImageObject",
                "url"    => $sanitizer->url($data['logo']->httpUrl),
                "height" => $sanitizer->text($data['logo']->height),
                "width"  => $sanitizer->text($data['logo']->width)
            );
        }
        
        // Ensure your frontend search page is working correctly!
        $out["potentialAction"] = array(
            "@type" => "SearchAction",
            "target" => wire('pages')->get(1000)->httpUrl . "?q={search_term}",
            "query-input" => "required name=search_term"
        );
            
         $out = array_filter($out);   
         return $out;
    }    
} 
?>
