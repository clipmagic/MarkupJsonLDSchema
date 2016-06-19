<?php
class JsonLDNewsArticle extends WireData {    
    public function __construct() {
    }
    
    public static function getSchema (array $data = null, Page $page = null) {
        // Article (news post, blog post, etc)
        
        $out = array();
         
        $home = wire('pages')->get(1);
        $sanitizer = wire('sanitizer');
            
        // for flat urls with a page field of page_url   
        $pageURL  = !empty($page->page_url) ? $home->httpUrl . $page->page_url : $page->httpUrl; 
        
                            
            $pageHeadline = !empty($data['headline']) ? $sanitizer->text($data['headline']) : $page->get('seo_title|headline|title');
            
            $out["@context"]         = "http://schema.org/";
            $out["@type"]            = !empty($data["@type"]) ? $sanitizer->text($data["@type"]) : "NewsArticle";
            $out["mainEntityOfPage"] = array(
                "@type" => 'WebPage',
                "@id"   => $page->httpUrl
            );
            $out["headline"]         = !empty($data["headline"]) ? $sanitizer->text($data["headline"]) : $pageHeadline;
            $out["url"]              = $pageURL;
                
            
            $out["datePublished"]   = date('c', $page->created);
            $out["dateModified"]    = date('c', $page->modified);
            $out["author"]          = array(
                "@type" => "Person",
                "name" => wire('users')->get($page->created_user_id)->title
            );
            if ($data['organization']) {        
                $out["publisher"]          = array(
                    "@type" => "Organization",
                    "name" => $sanitizer->text($data['organization'])
                );                
            }        
            if ($data['image']) {
                $out["image"]    = array(
                    "@type"  => "ImageObject",
                    "url"    => $sanitizer->url($data['image']->httpUrl),
                    "height" => $sanitizer->text($data['image']->height),
                    "width"  => $sanitizer->text($data['image']->width)
                );
             }
            $out['description'] = !empty($data["description"]) ? $sanitizer->text($data["description"]) : $page->get('seo_description|summary|title');
            $out["articleBody"] = !empty($data["articleBody"]) ? $sanitizer->textarea($data["description"]) : $page->get('body|blog-body');
         
         $out = array_filter($out);   
         return $out;
    }
}
?>