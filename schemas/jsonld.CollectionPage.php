<?php namespace ProcessWire;

class JsonLDCollectionPage extends WireData {

    public static function getSchema(?array $data = null, ?Page $page = null): array {
        $data ??= [];
        $page ??= wire('page');
        $home = wire('pages')->get('/');
        $sanitizer = wire('sanitizer');
        $items = self::items($data, $home, $sanitizer);

        $out = [
            '@context' => 'https://schema.org/',
            '@type' => 'CollectionPage',
            '@id' => rtrim($page->httpUrl, '/') . '/#collection',
            'url' => $page->httpUrl,
            'name' => !empty($data['name']) ? $sanitizer->text($data['name']) : $sanitizer->text($page->get('headline|title')),
            'description' => !empty($data['description']) ? $sanitizer->textarea($data['description']) : $sanitizer->textarea($page->get('seo_description|summary')),
            'publisher' => ['@id' => rtrim($home->httpUrl, '/') . '/#organization'],
        ];

        if($items) {
            $out['mainEntity'] = [
                '@type' => 'ItemList',
                'itemListElement' => array_map(
                    fn(array $item, int $position): array => [
                        '@type' => 'ListItem',
                        'position' => $position + 1,
                        'item' => $item,
                    ],
                    $items,
                    array_keys($items)
                ),
            ];
        }

        return array_filter($out);
    }

    protected static function items(array $data, Page $home, Sanitizer $sanitizer): array {
        $items = [];
        $pages = $data['brands'] ?? $data['items'] ?? [];

        if(!is_array($pages) && !$pages instanceof \Traversable) return $items;

        foreach($pages as $itemPage) {
            if(!$itemPage instanceof Page || !$itemPage->id) continue;

            if(class_exists(JsonLDBrand::class)) {
                $items[] = JsonLDBrand::reference($itemPage, $home, $sanitizer);
                continue;
            }

            $items[] = [
                '@type' => 'Thing',
                '@id' => rtrim($itemPage->httpUrl, '/') . '/#thing',
                'name' => $sanitizer->text($itemPage->get('headline|title')),
                'url' => $itemPage->httpUrl,
            ];
        }

        return $items;
    }
}
