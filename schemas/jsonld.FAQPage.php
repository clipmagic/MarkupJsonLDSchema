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

        $out['primaryImageOfPage'] = $data['primaryImageOfPage'];
        if (!empty($data['image'])) {
            $out["primaryImageOfPage"]   = array(
                "@type"  => "ImageObject",
                "url"    => $sanitizer->url($data['primaryImageOfPage']->httpUrl),
                "height" => $sanitizer->text($data['primaryImageOfPage']->height),
                "width"  => $sanitizer->text($data['primaryImageOfPage']->width)
            );
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

                if(array_key_exists('relatedLink', $item) && !is_null($item['relatedLink'])) {
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
