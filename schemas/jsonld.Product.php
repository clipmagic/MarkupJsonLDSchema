<?php namespace ProcessWire;

/**
 * JSON-LD Product schema (schema.org/Product).
 *
 * Outputs a Product type with optional publisher, brand, image, and aggregateRating.
 * Pass module config plus optional overrides/custom fields via $data.
 *
 * @see https://schema.org/Product
 */
class JsonLDProduct extends WireData {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Build the Product schema array.
     *
     * @param array<string, mixed>|null $data Config/overrides: @type, name, description, brand, image (Pageimage), rating_value, review_count.
     * @param Page|null $page Page context (used for fallback name, description, brand from fields).
     * @return array<string, mixed> Schema array for json_encode.
     */
    public static function getSchema(?array $data = null, ?Page $page = null): array {
        $out = array();
        $data ??= [];
        $page ??= wire('page');
                 
        $home = wire('pages')->get('/');
        $sanitizer = wire('sanitizer');

        $out["@context"]    = "https://schema.org/";
        $out["@type"]       = !empty($data["@type"]) ? $sanitizer->text($data["@type"]) : "Product";
        $out["publisher"]   = ['@id' => rtrim($home->httpUrl, '/') . '/#organization'];
        $out["brand"]       = !empty($data['brand']) ? $sanitizer->text($data['brand']) : $page->get('brand|manufacturer|title');
        $out["name"]        = !empty($data['name']) ? $sanitizer->text($data['name']) : $page->get('seo_title|title|headline');
        $out["description"] = !empty($data['description']) ? $sanitizer->textarea($data['description']) : $page->get('seo_description|summary|blog-summary');
        if (!empty($data['image'])) {
            $out["image"]   = array(
                "@type"  => "ImageObject",
                "url"    => $sanitizer->url($data['image']->httpUrl),
                "height" => $sanitizer->text($data['image']->height),
                "width"  => $sanitizer->text($data['image']->width)
            );
         }
        
        if (!empty($data['rating_value']) || !empty($data['review_count'])) {
            $out['aggregateRating'] = array(
                "@type" => "AggregateRating",
                "ratingValue" => $sanitizer->text($data['rating_value']),
                "reviewCount" => $sanitizer->text($data['review_count'])
            
            );
         }


        $out = array_filter($out);
        return $out;
    }
}?>
