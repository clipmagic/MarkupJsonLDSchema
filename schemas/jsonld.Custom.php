<?php namespace ProcessWire;

/**
 * JSON-LD Custom schema placeholder.
 *
 * Use when building the full schema in the template. Pass options as $options = ['custom' => array( ... your schema ... )]; the module merges $data['custom'] into the result. This class returns an empty array by default; add schema via the custom key when calling render().
 *
 * @see https://schema.org
 */
class JsonLDCustom extends WireData {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Placeholder; return an array from the template via $data['custom'].
     *
     * @param array<string, mixed>|null $data Config; use 'custom' key with your full schema array when calling render().
     * @param Page|null $page Page context (available in template when building $options).
     * @return array<string, mixed> Empty array; module merges in $data['custom'] when present.
     */
    public static function getSchema(?array $data = null, ?Page $page = null): array {
        return [];
    }
}
