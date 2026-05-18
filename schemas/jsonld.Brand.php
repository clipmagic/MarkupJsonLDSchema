<?php namespace ProcessWire;

class JsonLDBrand extends WireData {

    public static function getSchema(?array $data = null, ?Page $page = null): array {
        $data ??= [];
        $page ??= wire('page');
        $home = wire('pages')->get('/');
        $sanitizer = wire('sanitizer');

        $out = [
            '@context' => 'https://schema.org/',
            '@type' => 'Brand',
            '@id' => rtrim($page->httpUrl, '/') . '/#brand',
            'name' => !empty($data['name']) ? $sanitizer->text($data['name']) : $sanitizer->text($page->get('headline|title')),
            'url' => $page->httpUrl,
            'description' => !empty($data['description']) ? $sanitizer->textarea($data['description']) : $sanitizer->textarea($page->get('seo_description|summary')),
            'owner' => ['@id' => rtrim($home->httpUrl, '/') . '/#organization'],
            'mainEntityOfPage' => $page->httpUrl,
        ];

        if(!empty($data['image'])) {
            $image = self::imageObject($data['image'], $sanitizer);
            if($image) $out['image'] = $image;
        } elseif($page->hasField('feature_image') && $page->feature_image) {
            $image = self::imageObject($page->feature_image, $sanitizer);
            if($image) $out['image'] = $image;
        }

        if(!empty($data['logo'])) {
            $logo = self::imageObject($data['logo'], $sanitizer);
            if($logo) $out['logo'] = $logo;
        }

        if(!empty($data['slogan'])) {
            $out['slogan'] = $sanitizer->text($data['slogan']);
        }

        return array_filter($out);
    }

    public static function reference(Page $page, ?Page $home = null, ?Sanitizer $sanitizer = null): array {
        $home ??= wire('pages')->get('/');
        $sanitizer ??= wire('sanitizer');

        $out = [
            '@type' => 'Brand',
            '@id' => rtrim($page->httpUrl, '/') . '/#brand',
            'name' => $sanitizer->text($page->get('headline|title')),
            'url' => $page->httpUrl,
            'owner' => ['@id' => rtrim($home->httpUrl, '/') . '/#organization'],
        ];

        if($page->hasField('feature_image') && $page->feature_image) {
            $image = self::imageObject($page->feature_image, $sanitizer);
            if($image) $out['image'] = $image;
        }

        return array_filter($out);
    }

    protected static function imageObject(mixed $image, Sanitizer $sanitizer): ?array {
        if(!is_object($image) || empty($image->httpUrl)) return null;

        return array_filter([
            '@type' => 'ImageObject',
            'url' => $sanitizer->url($image->httpUrl),
            'height' => !empty($image->height) ? $sanitizer->int($image->height) : null,
            'width' => !empty($image->width) ? $sanitizer->int($image->width) : null,
        ]);
    }
}
