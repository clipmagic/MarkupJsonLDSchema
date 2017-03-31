<?php
class JsonLDWebPage  extends WireData {
    
    public function __construct() {
    }
    
    public static function getSchema (array $data = null, Page $page = null) {
     // Current web page info  
     
        $out = array();
                 
        $home = wire('pages')->get(1);
        $sanitizer = wire('sanitizer');
            
        // for flat urls with a page field of page_url   
        $pageURL  = !empty($page->page_url) ? $home->httpUrl . $page->page_url : $page->httpUrl; 
                      
        $out["@context"]    = "http://schema.org";
        $out["@type"]       = !empty($data["@type"]) ? $sanitizer->text($data["@type"]) : "WebPage";
        $out["url"]         = $pageURL;
        $out["name"]        = !empty($data['name']) ? $sanitizer->text($data['name']) : $page->get('seo_title|title|headline');
        $out["description"] = !empty($data['description']) ? $sanitizer->textarea($data['description']) : $page->get('seo_description|summary|blog-summary');
        if ($data['image']) {
            $out["image"]   = array(
                "@type"  => "ImageObject",
                "url"    => $sanitizer->url($data['image']->httpUrl),
                "height" => $sanitizer->text($data['image']->height),
                "width"  => $sanitizer->text($data['image']->width)
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
}?>
