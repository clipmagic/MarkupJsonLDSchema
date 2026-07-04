<?php namespace ProcessWire;

class JsonLDPerson extends WireData
{
    public static function getSchema(?array $data = null, ?Page $page = null): array
    {
        $data ??= [];
        $page ??= wire('page');

        $home = wire('pages')->get('/');
        $sanitizer = wire('sanitizer');

        $out = [];
        $out['@context'] = 'https://schema.org';
        $out['@type'] = !empty($data['@type']) ? $sanitizer->text($data['@type']) : 'Person';

        if(!empty($data['@id'])) {
            $out['@id'] = $sanitizer->url($data['@id']);
        }

        $out['name'] = !empty($data['name'])
            ? $sanitizer->text($data['name'])
            : $sanitizer->text($page->get('seo_title|headline|title'));

        if(!empty($data['givenName'])) {
            $out['givenName'] = $sanitizer->text($data['givenName']);
        }

        if(!empty($data['familyName'])) {
            $out['familyName'] = $sanitizer->text($data['familyName']);
        }

        if(!empty($data['alternateName'])) {
            $out['alternateName'] = $sanitizer->text($data['alternateName']);
        }

        $out['description'] = !empty($data['description'])
            ? $sanitizer->textarea($data['description'])
            : $sanitizer->textarea($page->get('seo_description|summary|headline|title'));

        $out['url'] = !empty($data['url'])
            ? $sanitizer->url($data['url'])
            : $page->httpUrl;

        if(!empty($data['email'])) {
            $out['email'] = $sanitizer->email($data['email']);
        }

        if(!empty($data['telephone'])) {
            $out['telephone'] = $sanitizer->text($data['telephone']);
        }

        if(!empty($data['jobTitle'])) {
            $out['jobTitle'] = $sanitizer->text($data['jobTitle']);
        }

        if(!empty($data['image'])) {
            if(is_object($data['image']) && !empty($data['image']->httpUrl)) {
                $out['image'] = [
                    '@type' => 'ImageObject',
                    'url' => $sanitizer->url($data['image']->httpUrl),
                ];

                if(!empty($data['image']->width)) {
                    $out['image']['width'] = $sanitizer->int($data['image']->width);
                }

                if(!empty($data['image']->height)) {
                    $out['image']['height'] = $sanitizer->int($data['image']->height);
                }
            } else {
                $out['image'] = $sanitizer->url($data['image']);
            }
        }

        $worksFor = [];
        if(!empty($data['worksFor']) && is_array($data['worksFor'])) {
            $worksFor = self::sanitizeWorksFor($data['worksFor'], $sanitizer);
        } elseif(!empty($data['worksFor'])) {
            $worksFor = [
                '@type' => 'Organization',
                'name' => $sanitizer->text($data['worksFor']),
            ];
        } elseif(!empty($data['organization'])) {
            $worksFor = [
                '@type' => 'Organization',
                '@id' => rtrim($home->httpUrl, '/') . '/#organization',
                'name' => $sanitizer->text($data['organization']),
            ];
        }

        if(!empty($worksFor)) {
            $out['worksFor'] = $worksFor;
        }

        // Optional flat address fields for module-level normalization
        if(!empty($data['street_address'])) {
            $out['streetAddress'] = $sanitizer->text($data['street_address']);
        }

        if(!empty($data['address_locality'])) {
            $out['addressLocality'] = $sanitizer->text($data['address_locality']);
        }

        if(!empty($data['address_region'])) {
            $out['addressRegion'] = $sanitizer->text($data['address_region']);
        }

        if(!empty($data['postcode'])) {
            $out['postalCode'] = $sanitizer->text($data['postcode']);
        }

        if(!empty($data['address_country'])) {
            $out['addressCountry'] = $sanitizer->text($data['address_country']);
        }

        if(!empty($data['same_as'])) {
            if(is_array($data['same_as'])) {
                $out['sameAs'] = array_values(array_filter(array_map('trim', $data['same_as'])));
            } else {
                $out['sameAs'] = array_values(array_filter(
                    array_map('trim', explode("\n", (string) $data['same_as']))
                ));
            }
        }

        return array_filter($out);
    }

    protected static function sanitizeWorksFor(array $worksFor, Sanitizer $sanitizer): array
    {
        $out = [];

        foreach($worksFor as $key => $value) {
            $cleanKey = trim((string) $key);
            if($cleanKey === '') continue;

            if(is_array($value)) {
                $cleanVals = [];
                foreach($value as $subKey => $subVal) {
                    $cleanSubKey = trim((string) $subKey);
                    if($cleanSubKey === '') continue;

                    $cleanSubVal = $cleanSubKey === '@id'
                        ? $sanitizer->url((string) $subVal)
                        : $sanitizer->text((string) $subVal);

                    if($cleanSubVal !== '') {
                        $cleanVals[$cleanSubKey] = $cleanSubVal;
                    }
                }

                if(!empty($cleanVals)) {
                    $out[$cleanKey] = $cleanVals;
                }
            } else {
                $cleanVal = $cleanKey === '@id'
                    ? $sanitizer->url((string) $value)
                    : $sanitizer->text((string) $value);

                if($cleanVal !== '') {
                    $out[$cleanKey] = $cleanVal;
                }
            }
        }

        return $out;
    }
}