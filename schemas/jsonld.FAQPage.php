<?php namespace ProcessWire;

class JsonLDFAQPage {

    public static function getSchema(?array $data = null, ?Page $page = null): array {

        $out = [];
        $data ??= [];

        $home = wire('pages')->get('/');
        $sanitizer = wire('sanitizer');
        $wt = wire('modules')->get('WireTextTools');

        $out['@context'] = 'https://schema.org';
        $out['@type'] = 'FAQPage';
        $out["publisher"] = ['@id' => rtrim($home->httpUrl, '/') . '/#organization'];
        $out['image'] = $data['image'] ?? '';
        if (!empty($data['image'])) {
            $out["image"]   = array(
                "@type"  => "ImageObject",
                "url"    => $sanitizer->url($data['image']->httpUrl),
                "height" => $sanitizer->text($data['image']->height),
                "width"  => $sanitizer->text($data['image']->width)
            );
        }

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


        $mainEntity = [];

        // Expecting $data['items'] as array of Q/A
        if (!empty($data['items']) && is_array($data['items'])) {

            foreach ($data['items'] as $item) {

                if (empty($item['question']) || empty($item['answer'])) continue;

                $question = $sanitizer->text($item['question']);

                // Strip markup + normalize
                $answer = $wt->markupToText((string) $item['answer']);
                $answer = preg_replace('/\h+/u', ' ', $answer);
                $answer = trim($answer);

                $acceptedAnswer = [
                    '@type' => 'Answer',
                    'text' => $answer,
                ];

                if(isset($item['relatedLink'])) {
                    $acceptedAnswer['relatedLink'] = $item['relatedLink'];
                }

                $faq = [
                    '@type' => 'Question',
                    'name' => $question,
                    'acceptedAnswer' => $acceptedAnswer,
                ];

                $mainEntity[] = array_filter($faq);
            }
        }

        if (!empty($mainEntity)) {
            $out['mainEntity'] = $mainEntity;
        }

        // Merge any custom fields (consistent with your module pattern)
        if (!empty($data['custom']) && is_array($data['custom'])) {
            foreach ($data['custom'] as $key => $value) {
                $out[$sanitizer->text($key)] = $value;
            }
        }

        return array_filter($out);
    }
}