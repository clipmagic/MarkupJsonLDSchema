<?php namespace ProcessWire;

/**
 * JSON-LD WebSite schema (schema.org/WebSite).
 *
 * Outputs a WebSite type with url, name, description, publisher (@id), logo, and optional SearchAction (potentialAction) from config (search_results_page, search_get_var).
 *
 * @see https://schema.org/WebSite
 */
class JsonLDWebSite extends WireData {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Build the WebSite schema array.
     *
     * @param array<string, mixed>|null $data Module config: name, description, logo (Pageimage or URL), search_results_page, search_get_var; overrides: @type.
     * @param Page|null $page Page context (unused; home page used for url/fallbacks).
     * @return array<string, mixed> Schema array for json_encode.
     */
    public static function getSchema(?array $data = null, ?Page $page = null): array {
        $out = array();         
        $data ??= [];
        $page ??= wire('page');
         
        $home = wire('pages')->get('/');
        $sanitizer = wire('sanitizer');

        $pageURL = !empty($data['page_url']) ? $home->httpUrl . $data['page_url'] : $page->httpUrl;
        
        // Website home page info
        $out["@context"] = "https://schema.org/";
        $out["@type"]    = !empty($data["@type"])? $sanitizer->text($data["@type"]) : "WebSite";
        $out["url"]       = $pageURL;
        $out["name"]      = !empty($data["name"]) ? $sanitizer->text($data["name"]) : $home->get('seo_title|headline|title');
        $out["description"] = !empty($data["description"]) ? $sanitizer->textarea($data["description"]) : $home->get('seo_description|summary');
        $out["publisher"] = ['@id' => rtrim($home->httpUrl, '/') . '/#organization'];
        
        if (!empty($data['logo'])) {
            $out["logo"]    = array(
                "@type" => "ImageObject",
                "url"    => $sanitizer->url($data['logo']->httpUrl),
                "height" => $sanitizer->text($data['logo']->height),
                "width"  => $sanitizer->text($data['logo']->width)
            );
        }
        
        // Ensure your frontend search page is working correctly!

        $searchPage = trim($sanitizer->text($data['search_results_page'] ?? ''));
        $searchVar = trim($sanitizer->text($data['search_get_var'] ?? ''));

        if($searchPage !== '' && $searchVar !== '') {
            $searchPage = '/' . trim($searchPage, '/') . '/';

            $out['potentialAction'] = [
                '@type' => 'SearchAction',
                'target' => rtrim($home->httpUrl, '/') . $searchPage . '?' . $searchVar . '={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ];
        }

        return array_filter($out);
    }    
} 
