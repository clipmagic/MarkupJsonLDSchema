<?php
class JsonLDOrganization extends WireData {    
    public function __construct() {
    }
    
    public static function getSchema (array $data = null, Page $page = null) {
        // Organization
        
        $out = array();
        $page = wire('page');
        $home = wire('pages')->get(1);
        $sanitizer = wire('sanitizer');
 
        $seo_description = !empty($data['description']) ? $sanitizer->text($data['description']) : $page->get('seo_description|headline|summary|title');
 
        $out["@context"]         = "http://schema.org/";
        $out["@type"]            = !empty($data["@type"]) ? $sanitizer->text($data["@type"]) : "Organization";
        $out['name']             = $sanitizer->text($data['organization']);
        $out['address']          = array(
            "@type"              => "PostalAddress",
            "streetAddress"      => $sanitizer->text($data['street_address']),
            "addressLocality"    => $sanitizer->text($data['address_locality']),
            "addressRegion"      => $sanitizer->text($data['address_region']),
            "postalCode"         => $sanitizer->text($data['postcode']),
            "addressCountry"     => $sanitizer->text($data['address_country'])
        );
        $out['description']      = !empty($data['description']) ? $sanitizer->text($data['description']) : $sanitizer->text($seo_description);
        $out['telephone']        = $sanitizer->text($data['telephone']);
        $out['openingHours']     = $sanitizer->text($data['opening_hours']);
        if (!empty($data['listimage'])) {
            $out['image'] = $data['listimage'];
        }

        if (!empty($data['latitude']) || !empty($data['longitude'])) {
            $out['geo']          = array (
                '@type'     => "GeoCoordinates",
                'latitude'  => $sanitizer->text($data['latitude']),
                'longitude' => $sanitizer->text($data['longitude'])
            );
            //if (!empty($data['has_map'])) {
            //    $out['geo']['hasMap'] = $sanitizer->url($data['has_map']);
            //}
        }
        if (!empty($data['same_as']))
            $out['sameAs']       = explode(PHP_EOL,$data['same_as']);
        
        
        $out = array_filter($out);
        return $out;
    }
}