<?php namespace ProcessWire;

/**
 * JSON-LD Organization schema (schema.org/Organization).
 *
 * Outputs an Organization type with @id (homepage/#organization), name, url, description, logo, address, telephone, openingHours, sameAs, and optional geo. Used as the canonical entity referenced by publisher/@id in other schemas.
 *
 * @see https://schema.org/Organization
 */
class JsonLDOrganization extends WireData {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Build the Organization schema array.
     *
     * @param array<string, mixed>|null $data Module config: organization, description, logo, street_address, address_locality, address_region, postcode, address_country, telephone, opening_hours, same_as, latitude, longitude, has_map; overrides: @type.
     * @param Page|null $page Page context (unused; home page used for fallbacks).
     * @return array<string, mixed> Schema array for json_encode.
     */
    public static function getSchema(?array $data = null, ?Page $page = null): array {
        $data ??= [];
        $page ??= wire('page');
        $home = wire('pages')->get('/');
        $sanitizer = wire('sanitizer');

        $out = [];
        $out['@context'] = 'https://schema.org/';
        $out['@type'] = !empty($data['@type']) ? $sanitizer->text($data['@type']) : 'Organization';
        $out['@id'] = rtrim($home->httpUrl, '/') . '/#organization';

        $out['name'] = !empty($data['organization'])
            ? $sanitizer->text($data['organization'])
            : $sanitizer->text($home->get('seo_title|headline|title'));
        $out['url'] = $home->httpUrl;
        $out['description'] = !empty($data['description'])
            ? $sanitizer->textarea($data['description'])
            : $sanitizer->textarea($home->get('seo_description|summary'));

        if (!empty($data['logo'])) {
            if (is_object($data['logo']) && !empty($data['logo']->httpUrl)) {
                $out['logo'] = [
                    '@type' => 'ImageObject',
                    'url'   => $sanitizer->url($data['logo']->httpUrl),
                ];
                if (!empty($data['logo']->width)) {
                    $out['logo']['width'] = $sanitizer->int($data['logo']->width);
                }
                if (!empty($data['logo']->height)) {
                    $out['logo']['height'] = $sanitizer->int($data['logo']->height);
                }
            } else {
                $out['logo'] = $sanitizer->url($data['logo']);
            }
        }

        $out['streetAddress']   = $sanitizer->text($data['street_address'] ?? '');
        $out['addressLocality'] = $sanitizer->text($data['address_locality'] ?? '');
        $out['addressRegion']   = $sanitizer->text($data['address_region'] ?? '');
        $out['postalCode']      = $sanitizer->text($data['postcode'] ?? '');
        $out['addressCountry']  = $sanitizer->text($data['address_country'] ?? '');

        if (!empty($data['telephone'])) {
            $out['telephone'] = $sanitizer->text($data['telephone']);
        }
        if (!empty($data['opening_hours'])) {
            $out['openingHours'] = $sanitizer->text($data['opening_hours']);
        }

        if (!empty($data['same_as'])) {
            $out['sameAs'] = array_values(array_filter(
                array_map('trim', explode("\n", $data['same_as']))
            ));
        }

        $latitude = $data['latitude'] ?? null;
        $longitude = $data['longitude'] ?? null;
        if (is_numeric($latitude) || is_numeric($longitude)) {
            $out['geo'] = ['@type' => 'GeoCoordinates'];
            if (is_numeric($latitude)) {
                $out['geo']['latitude'] = (float) $latitude;
            }
            if (is_numeric($longitude)) {
                $out['geo']['longitude'] = (float) $longitude;
            }
        }

        if (!empty($data['has_map'])) {
            $out['hasMap'] = $sanitizer->url($data['has_map']);
        }


        if (!empty($data['image'])) {
            $image = self::sanitizeImageValue($data['image'], $sanitizer);
            if (!empty($image)) {
                $out['image'] = $image;
            }
        }

        return array_filter($out);
    }

    protected static function sanitizeImageValue(mixed $image, Sanitizer $sanitizer): mixed
    {
        // Pageimage extends WireData, which is Traversable. Check for a single
        // image object before treating a value as a collection of images.
        if (is_object($image) && !empty($image->httpUrl)) {
            return self::sanitizeSingleImageValue($image, $sanitizer);
        }

        if (is_array($image) || $image instanceof \Traversable) {
            $images = [];

            foreach ($image as $item) {
                $clean = self::sanitizeSingleImageValue($item, $sanitizer);
                if (!empty($clean)) {
                    $images[] = $clean;
                }
            }

            return $images;
        }

        return self::sanitizeSingleImageValue($image, $sanitizer);
    }

    protected static function sanitizeSingleImageValue(mixed $image, Sanitizer $sanitizer): mixed
    {
        if (is_object($image) && !empty($image->httpUrl)) {
            $out = [
                '@type' => 'ImageObject',
                'url'   => $sanitizer->url($image->httpUrl),
            ];

            if (!empty($image->width)) {
                $out['width'] = $sanitizer->int($image->width);
            }

            if (!empty($image->height)) {
                $out['height'] = $sanitizer->int($image->height);
            }

            return array_filter($out);
        }

        if (is_scalar($image)) {
            return $sanitizer->url((string) $image);
        }

        return null;
    }
}
