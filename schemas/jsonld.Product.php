<?php
class JsonLDProduct  extends WireData {
    
    public function __construct() {
    }
    
    public static function getSchema (array $data = null, Page $page = null) {
     // Current web page info  
     
        $out = array();
                 
        $home = wire('pages')->get(1);
        $sanitizer = wire('sanitizer');

        $out["@context"]    = "http://schema.org";
        $out["@type"]       = !empty($data["@type"]) ? $sanitizer->text($data["@type"]) : "Product";
        $out["brand"]       = !empty($data['brand']) ? $sanitizer->text($data['brand']) : $page->get('brand|manufacturer|title');
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
        
        if (!empty($data['rating_value']) || !empty($data['review_count'])) {
            $out['aggregateRating'] = array(
                "@type" => "aggregateRating",
                "ratingValue" => $sanitizer->text($data['rating_value']),
                "reviewCoumt" => $sanitizer->text($data['review_count'])
            
            );
         }
         $out = array_filter($out);   
         return $out;
    }
}?>