<?php namespace ProcessWire;

/**
 * JSON-LD Event schema (schema.org/Event).
 *
 * Outputs an Event type with organizer (@id), name, url, description, start/end dates, and optional location and offers. Pass module config plus optional overrides via $data.
 *
 * @see https://schema.org/Event
 */
class JsonLDEvent extends WireData {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Build the Event schema array.
     *
     * @param array<string, mixed>|null $data Config/overrides: @type, name, url, description, start_date, end_date, location (array), offers (array).
     * @param Page|null $page Page context (used for url, name, description, start_date, end_date fallbacks).
     * @return array<string, mixed> Schema array for json_encode.
     */
    public static function getSchema(?array $data = null, ?Page $page = null): array {
        $out = array();
        $data ??= [];
        $page ??= wire('page');
                 
        $home = wire('pages')->get('/');
        $sanitizer = wire('sanitizer');

        $out["@context"]    = "https://schema.org/";
        $out["@type"]       = !empty($data["@type"]) ? $sanitizer->text($data["@type"]) : "Event";
        $out["organizer"]   = ['@id' => rtrim($home->httpUrl, '/') . '/#organization'];
        $out["name"]        = !empty($data['name']) ? $sanitizer->text($data['name']) : $page->get('seo_title|title|headline');
        $out["url"]        = !empty($data['url']) ? $sanitizer->url($data['url']) : $page->httpUrl;
        $out["description"] = !empty($data['description']) ? $sanitizer->textarea($data['description']) : $page->get('seo_description|description|summary|headline');
        $out["startDate"] = !empty($data['start_date']) ? date(DATE_ISO8601, strtotime($sanitizer->text($data['start_date']))) : date(DATE_ISO8601, strtotime($sanitizer->text($page->start_date)));
        $out["endDate"] = !empty($data['end_date']) ? date(DATE_ISO8601, strtotime($sanitizer->text($data['end_date']))) : date(DATE_ISO8601, strtotime($sanitizer->text($page->end_date)));
        if (!empty($data['location']) && is_array($data['location'])) {
            $out['location'] = array();
            foreach ($data['location'] as $k => $v) {
                $out['location'][$k] = $sanitizer->text($v);
            }
        }
        
        if (!empty($data['offers']) && is_array($data['offers'])) {
            $out['offers'] = array();
            foreach ($data['offers'] as $k => $v) {
                $out['offers'][$k] = $sanitizer->text($v);
            }
        }

        $out = array_filter($out);
        return $out;
    }
}?>
