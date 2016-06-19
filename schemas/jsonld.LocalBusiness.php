<?php
class JsonLDLocalBusiness extends WireData {    
    public function __construct() {
    }
    
    public static function getSchema (array $data = null, Page $page = null) {
        // LocalBusiness
        
        $out = array();
        $page = wire('page');
        $home = wire('pages')->get(1);
        $sanitizer = wire('sanitizer');
 
        $seo_description = !empty($data['description']) ? $sanitizer->text($data['description']) : $page->get('seo_description|headline|summary|title');
 
        $out["@context"]         = "http://schema.org/";
        $out["@type"]            = !empty($data["@type"]) ? $sanitizer->text($data["@type"]) : "LocalBusiness";
        $out['name']             = $sanitizer->text($data['organization']);
        $out['streetAddress']    = $sanitizer->text($data['street_address']);
        $out['addressLocality']  = $sanitizer->text($data['address_locality']);
        $out['addressRegion']    = $sanitizer->text($data['address_region']);
        $out['postalCode']       = $sanitizer->text($data['postcode']);
        $out['addressCountry']  = $sanitizer->text($data['address_country']);
        $out['description']      = !empty($data['description']) ? $sanitizer->text($data['description']) : $sanitizer->text($seo_description);
        $out['telephone']        = $sanitizer->text($data['telephone']);
        $out['openingHours']     = $sanitizer->text($data['opening_hours']);
        if (!empty($data['latitude']) || !empty($data['longitude'])) {
            $out['geo']          = array (
                'latitude' => $sanitizer->text($data['latitude']),
                'longitude' => $sanitizer->text($data['longitude'])
            );
            if (!empty($data['has_map'])) {
                $out['geo']['hasMap'] = $sanitizer->url($data['has_map']);
            }
        }
        if (!empty($data['same_as']))
            $out['sameAs']       = explode(PHP_EOL,$data['same_as']);
        
        
        $out = array_filter($out);
        return $out;
    }
}