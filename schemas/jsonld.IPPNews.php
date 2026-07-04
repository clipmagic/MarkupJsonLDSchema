<?php namespace ProcessWire;

class JsonLDIPPNews extends WireData {

    protected static function dateValue(mixed $value): ?string {
        if(empty($value)) return null;
        $timestamp = is_numeric($value) ? (int) $value : strtotime((string) $value);
        return $timestamp ? date('c', $timestamp) : null;
    }

    public static function getSchema(?array $data = null, ?Page $page = null): array {
        $data ??= [];
        $page ??= wire('page');
        $home = wire('pages')->get('/');
        $sanitizer = wire('sanitizer');
        $isTradeShow = $page->hasField('tags') && $page->tags('id=1095')->count() > 0;
        $description = !empty($data['description']) ? $sanitizer->textarea($data['description']) : $page->get('seo_description|summary|title');
        $name = !empty($data['name']) ? $sanitizer->text($data['name']) : $page->get('headline|title');
        $body = !empty($data['articleBody']) ? $sanitizer->textarea($data['articleBody']) : trim(strip_tags((string) $page->get('body')));
        $image = $data['image'] ?? null;
        $publishedDate = $page->hasField('publish_date') ? self::dateValue($page->publish_date) : null;

        if($isTradeShow) {
            $out = [
                '@context' => 'https://schema.org/',
                '@type' => 'Event',
                'organizer' => ['@id' => rtrim($home->httpUrl, '/') . '/#organization'],
                'name' => $name,
                'url' => $page->httpUrl,
                'description' => $description,
            ];

            if($page->hasField('event_from')) {
                $startDate = self::dateValue($page->event_from);
                if($startDate) $out['startDate'] = $startDate;
            }

            if($page->hasField('event_to')) {
                $endDate = self::dateValue($page->event_to);
                if($endDate) $out['endDate'] = $endDate;
            }

            $city = $page->hasField('city') ? trim((string) $page->city) : '';
            $country = $page->hasField('country') ? trim((string) $page->country) : '';
            $locationName = trim("$city $country");

            if($locationName !== '') {
                $out['location'] = [
                    '@type' => 'Place',
                    'name' => $sanitizer->text($locationName),
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressLocality' => $sanitizer->text($city),
                        'addressCountry' => $sanitizer->text($country),
                    ],
                ];
            }

            if($image instanceof Pageimage) {
                $out['image'] = [$image->httpUrl];
            }

            return array_filter($out);
        }

        $out = [
            '@context' => 'https://schema.org/',
            '@type' => 'NewsArticle',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $page->httpUrl,
            ],
            'headline' => $name,
            'url' => $page->httpUrl,
            'datePublished' => $publishedDate ?: self::dateValue($page->created),
            'dateModified' => self::dateValue($page->modified),
            'author' => [
                '@type' => 'Organization',
                '@id' => rtrim($home->httpUrl, '/') . '/#organization',
                'name' => $home->title,
            ],
            'publisher' => ['@id' => rtrim($home->httpUrl, '/') . '/#organization'],
            'description' => $description,
            'articleBody' => $body,
        ];

        if($image instanceof Pageimage) {
            $out['image'] = [
                '@type' => 'ImageObject',
                'url' => $image->httpUrl,
                'height' => $image->height(),
                'width' => $image->width(),
            ];
        }

        return array_filter($out);
    }
}
