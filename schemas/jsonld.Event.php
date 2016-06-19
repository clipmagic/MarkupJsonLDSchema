<?php
class JsonLDEvent  extends WireData {
    
    public function __construct() {
    }
    
    public static function getSchema (array $data = null, Page $page = null) {
     // Current web page info  
     
        $out = array();
                 
        $home = wire('pages')->get(1);
        $sanitizer = wire('sanitizer');
/*
 * <script type='application/ld+json'> 
{
  "@context": "http://www.schema.org",
  "@type": "BusinessEvent",
  "name": "fgfg",
  "url": "ghghgfh",
  "description": "fhfgfg",
  "startDate": "2016-06-14T14:00",
  "endDate": "2016-06-14T17:30",
  "location": {
    "@type": "Place",
    "name": "dfdfdf",
    "sameAs": "venu.com",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "some street",
      "addressLocality": "sdfs",
      "addressRegion": "sdf",
      "postalCode": "sdf",
      "addressCountry": "sdf"
    }
  },
  "offers": {
    "@type": "Offer",
    "description": "sdf",
    "url": "sdf",
    "price": "sdf"
  }
}
 </script>
 */
        $out["@context"]    = "http://schema.org";
        $out["@type"]       = !empty($data["@type"]) ? $sanitizer->text($data["@type"]) : "Event";
        $out["name"]        = !empty($data['name']) ? $sanitizer->text($data['name']) : $page->get('seo_title|title|headline');
        $out["url"]        = !empty($data['url']) ? $sanitizer->url($data['url']) : $page->httpUrl;
        $out["description"]        = !empty($data['description']) ? $sanitizer->url($data['description']) : $page->get('seo_description|description|summary|headline');
        $out["start_date"]        = !empty($data['start_date']) ? date(DATE_ISO8601, strtotime($sanitizer->text($data['start_date']))) : date(DATE_ISO8601, strtotime($sanitizer->text($page->start_date)));
        $out["end_date"]        = !empty($data['end_date']) ? date(DATE_ISO8601, strtotime($sanitizer->text($data['end_date']))) : date(DATE_ISO8601, strtotime($sanitizer->text($page->end_date)));
        if (is_array($data['location'])) {
            $out['location'] = array();
            foreach ($data['location'] as $k => $v) {
                $out['location'][$k] = $sanitizer->text($v);
            }
        }
        
        if (is_array($data['offers'])) {
            $out['offers'] = array();
            foreach ($data['offers'] as $k => $v) {
                $out['offers'][$k] = $sanitizer->text($v);
            }
        }
        
        
        $out = array_filter($out);   
         return $out;
    }
}?>