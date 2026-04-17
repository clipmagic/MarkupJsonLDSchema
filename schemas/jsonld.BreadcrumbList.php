<?php namespace ProcessWire;

/**
 * JSON-LD BreadcrumbList schema (schema.org/BreadcrumbList).
 *
 * Outputs a BreadcrumbList from the current page's parents. Supports flat URLs via page_url field. Returns empty when page has no parents (e.g. home); module skips this schema on home.
 *
 * @see https://schema.org/BreadcrumbList
 */
class JsonLDBreadcrumbList extends WireData {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Build the BreadcrumbList schema array.
     *
     * @param array<string, mixed>|null $data Unused; config passed for consistency.
     * @param Page|null $page Page context (current page; parents used for list items). Uses wire('page') if null.
     * @return array<string, mixed> Schema array for json_encode, or empty array when no parents.
     */
    public static function getSchema(?array $data = null, ?Page $page = null): array {
        $out = [];
        $data ??= [];
        $page ??= wire('page');
        $sanitizer = wire('sanitizer');

        $trail = [];

        foreach($page->parents() as $parent) {
            $trail[] = $parent;
        }

        $trail[] = $page;

        if(count($trail)) {
            $listItems = [];
            $positionCounter = 1;

            foreach($trail as $item) {
                $listItems[] = [
                    '@type' => 'ListItem',
                    'position' => $positionCounter,
                    'item' => [
                        '@id' => $item->httpUrl,
                        'name' => $item->get('headline|title'),
                    ],
                ];

                $positionCounter++;
            }

            $out['@context'] = 'https://schema.org';
            $out['@type'] = 'BreadcrumbList';
            $out['itemListElement'] = $listItems;
        }

        return array_filter($out);
    }}
?>
