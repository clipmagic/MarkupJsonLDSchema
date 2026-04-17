<?php namespace ProcessWire;

$config = [

    'organization_info' => [
        'type' => 'fieldset',
        'label' => __('Organization info'),
        'columnWidth' => 50,
        'children' => [

            'organization' => [
                'type' => 'text',
                'label' => __('Organization'),
                'value' => '',
                'stripTags' => 1,
            ],

            'logo' => [
                'type' => 'text',
                'label' => __('Logo'),
                'value' => '',
                'stripTags' => 1,
            ],

            'street_address' => [
                'type' => 'text',
                'label' => __('Street'),
                'value' => '',
                'stripTags' => 1,
            ],

            'address_locality' => [
                'type' => 'text',
                'label' => __('City'),
                'value' => '',
                'stripTags' => 1,
            ],

            'address_region' => [
                'type' => 'text',
                'label' => __('State'),
                'value' => '',
                'stripTags' => 1,
            ],

            'postcode' => [
                'type' => 'text',
                'label' => __('Postcode'),
                'value' => '',
                'stripTags' => 1,
            ],

            'address_country' => [
                'type' => 'text',
                'label' => __('Country Code'),
                'value' => '',
                'stripTags' => 1,
            ],

            'description' => [
                'type' => 'textarea',
                'label' => __('Description'),
                'value' => '',
                'stripTags' => 1,
            ],

            'contact_point' => [
                'type' => 'textarea',
                'label' => __('Contact Info'),
                'value' => '',
                'stripTags' => 1,
            ],
        ],
    ],

    'extra_info' => [
        'type' => 'fieldset',
        'label' => __('Extra info'),
        'columnWidth' => 50,
        'children' => [

            'telephone' => [
                'type' => 'text',
                'label' => __('Phone'),
                'value' => '',
                'stripTags' => 1,
            ],

            'opening_hours' => [
                'type' => 'text',
                'label' => __('Opening hours'),
                'value' => '',
                'stripTags' => 1,
            ],

            'latitude' => [
                'type' => 'text',
                'label' => __('Latitude'),
                'value' => '',
                'stripTags' => 1,
            ],

            'longitude' => [
                'type' => 'text',
                'label' => __('Longitude'),
                'value' => '',
                'stripTags' => 1,
            ],

            'has_map' => [
                'type' => 'url',
                'label' => __('Google Map URL'),
                'value' => '',
            ],

            'same_as' => [
                'type' => 'textarea',
                'label' => __('Social media URLs'),
                'description' => __('Full URL including https. One per line.'),
                'value' => '',
            ],

            'search_results_page' => [
                'type' => 'text',
                'label' => __('Search results page'),
                'description' => __('Relative URL, for example /search/. Leave blank to omit SearchAction.'),
                'value' => '',
                'stripTags' => 1,
                'columnWidth' => 50,
            ],

            'search_get_var' => [
                'type' => 'text',
                'label' => __('Search GET variable'),
                'description' => __('Example: q or s. Leave blank to omit SearchAction.'),
                'value' => '',
                'stripTags' => 1,
                'columnWidth' => 50,
            ],

        ],
    ],
];