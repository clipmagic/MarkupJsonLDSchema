<?php namespace ProcessWire;

/**
 * JSON-LD LocalBusiness schema (schema.org/LocalBusiness).
 *
 * Outputs a LocalBusiness (Organization subtype) with name, address, description, telephone, openingHours, optional geo and sameAs. Uses module config (organization, address fields, etc.).
 *
 * @see https://schema.org/LocalBusiness
 */
class JsonLDLocalBusiness extends WireData {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Build the LocalBusiness schema array.
     *
     * @param array<string, mixed>|null $data Module config: organization, street_address, address_locality, address_region, postcode, address_country, description, telephone, opening_hours, latitude, longitude, has_map, same_as; overrides: @type.
     * @param Page|null $page Page context (used for description fallback from seo_description|headline|summary|title).
     * @return array<string, mixed> Schema array for json_encode.
     */
    public static function getSchema(?array $data = null, ?Page $page = null): array {
        $out = [];
        $data ??= [];
        $page ??= wire('page');
        $sanitizer = wire('sanitizer');
 
        $seo_description = !empty($data['description']) ? $sanitizer->text($data['description']) : $page->get('seo_description|headline|summary|title');
 
        $out["@context"]         = "https://schema.org/";
        $out["@type"]            = !empty($data["@type"]) ? $sanitizer->text($data["@type"]) : "LocalBusiness";
        $out['name']             = $sanitizer->text($data['organization']);

        $out['streetAddress']   = $sanitizer->text($data['street_address'] ?? '');
        $out['addressLocality'] = $sanitizer->text($data['address_locality'] ?? '');
        $out['addressRegion']   = $sanitizer->text($data['address_region'] ?? '');
        $out['postalCode']      = $sanitizer->text($data['postcode'] ?? '');
        $out['addressCountry']  = $sanitizer->text($data['address_country'] ?? '');

        $out['description']      = !empty($data['description']) ? $sanitizer->text($data['description']) : $sanitizer->text($seo_description);
        $out['telephone']        = $sanitizer->text($data['telephone']);


        if(!empty($data['opening_hours']))
            if(is_string($data['opening_hours'])) {
                $out['openingHours'] = $sanitizer->textarea($data['opening_hours']);
            }  else if(is_array($data['opening_hours'])) {
                $out['openingHours'] = $data['opening_hours'];
            }


        if (!empty($data['latitude']) || !empty($data['longitude'])) {
            $out['geo']          = array (
                '@type' => 'GeoCoordinates',
                'latitude' => $sanitizer->text($data['latitude']),
                'longitude' => $sanitizer->text($data['longitude'])
            );
        }
        if (!empty($data['has_map'])) {
            $out['hasMap'] = $sanitizer->url($data['has_map']);
        }

        $sameAs = [];

        if(!empty($data['same_as'])) {
            $sameAs = array_values(array_filter(
                array_map('trim', explode("\n", $data['same_as']))
            ));
        }

        if (!empty($sameAs)) {
            $out['sameAs'] = $sameAs;
        }

        if (!empty($data['image'])) {
            $image = self::sanitizeImageValue($data['image'], $sanitizer);
            if (!empty($image)) {
                $out['image'] = $image;
            }
        }

        $out = array_filter($out);
        return $out;
    }

    protected static function sanitizeImageValue(mixed $image, Sanitizer $sanitizer): mixed
    {
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
