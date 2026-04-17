<?php namespace ProcessWire;

/**
 * JSON-LD Article schema (schema.org/Article).
 *
 * Outputs an Article type with headline, dates, author, publisher (@id), and optional image.
 * Pass module config plus optional overrides via $data.
 *
 * @see https://schema.org/Article
 */
class JsonLDArticle extends WireData {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Build the Article schema array.
     *
     * @param array<string, mixed>|null $data Config/overrides: @type, headline, description, articleBody, image (Pageimage).
     * @param Page|null $page Page context (used for mainEntityOfPage, headline, dates, author, body).
     * @return array<string, mixed> Schema array for json_encode.
     */
    public static function getSchema(?array $data = null, ?Page $page = null): array {
        $out = array();
        $data ??= [];
        $page ??= wire('page');
         
        $home = wire('pages')->get('/');
        $sanitizer = wire('sanitizer');
            
        $pageURL = !empty($data['page_url']) ? $home->httpUrl . $data['page_url'] : $page->httpUrl;
        
                            
            $pageHeadline = !empty($data['headline']) ? $sanitizer->text($data['headline']) : $page->get('seo_title|headline|title');
            
            $out["@context"]         = "https://schema.org/";
            $out["@type"]            = !empty($data["@type"]) ? $sanitizer->text($data["@type"]) : "Article";
            $out["mainEntityOfPage"] = [
                "@type" => 'WebPage',
                "@id"   => rtrim($page->httpUrl, '/') . '/#webpage',
            ];
            $out["headline"]         = !empty($data["headline"]) ? $sanitizer->text($data["headline"]) : $pageHeadline;
            $out["url"]              = $pageURL;
                
            
            $out["datePublished"]   = date('c', $page->created);
            $out["dateModified"]    = date('c', $page->modified);
            $out["author"]          = array(
                "@type" => "Person",
                "name" => wire('users')->get($page->created_user_id)->title
            );
            $out["publisher"] = ['@id' => rtrim($home->httpUrl, '/') . '/#organization'];        
            if (!empty($data['image'])) {
                $out["image"]    = array(
                    "@type"  => "ImageObject",
                    "url"    => $sanitizer->url($data['image']->httpUrl),
                    "height" => $sanitizer->text($data['image']->height),
                    "width"  => $sanitizer->text($data['image']->width)
                );
             }
            $out['description'] = !empty($data["description"]) ? $sanitizer->text($data["description"]) : $page->get('seo_description|summary|title');
            $out["articleBody"] = !empty($data["articleBody"]) ? $sanitizer->textarea($data["articleBody"]) : $page->get('body|blog-body');

        $out = array_filter($out);
        return $out;
    }
}
