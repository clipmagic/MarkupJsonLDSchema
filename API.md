# MarkupJsonLDSchema

Generates JSON-LD schema arrays and JSON strings from bundled schema classes, with optional single-script `@graph` output. Retrieve an instance from ProcessWire's module API:

```php
$jsonld = $modules->get('MarkupJsonLDSchema');
```

The module can be used in two ways:

- `render()` for backward-compatible one-schema-at-a-time JSON-LD output.
- `add()` / `addNode()` / `renderGraph()` for one `@graph` per page.

## Single Schema Rendering

### `render($name, ?array $options = null, ?Page $page = null)`

Render one bundled schema as a JSON-LD string.

```php
$jsonld = $modules->get('MarkupJsonLDSchema');

echo '<script type="application/ld+json">';
echo $jsonld->render('WebPage');
echo '</script>';
```

Arguments:

| Argument | Type | Description |
|----------|------|-------------|
| `$name` | `string` | Schema name, such as `WebPage`, `WebSite`, `LocalBusiness`, `Person`, or `BreadcrumbList`. |
| `$options` | `array|null` | Optional schema overrides and custom values. |
| `$page` | `Page|null` | Optional page context. Defaults to the current `$page`. |

Returns a JSON string. For `BreadcrumbList` on the home page, returns an empty string.

### `getSchema($name, ?array $options = null, ?Page $page = null)`

Build one bundled schema and return it as an array without JSON encoding.

```php
$schema = $jsonld->getSchema('WebPage', [
    'custom' => [
        '@id' => rtrim($page->httpUrl, '/') . '/#webpage',
    ],
]);
```

Use this when you need to inspect, alter, cache, or combine schema data before rendering.

Returns an array. For `BreadcrumbList` on the home page, returns an empty array.

## Schema Options

Pass options to override defaults or add custom schema properties.

```php
echo $jsonld->render('LocalBusiness', [
    '@type' => 'RealEstateAgent',
    'telephone' => '+61 2 0000 0000',
    'custom' => [
        '@id' => rtrim($homepage->httpUrl, '/') . '/#localbusiness',
        'areaServed' => [
            '@type' => 'AdministrativeArea',
            'name' => 'Central Coast NSW',
        ],
    ],
]);
```

Common option behavior:

| Option | Description |
|--------|-------------|
| `@type` | Overrides the schema type when supported by the schema class. |
| `custom` | Adds extra schema properties after the bundled schema is built. |
| `image` / `logo` | Many schema classes accept `Pageimage` values and render `ImageObject` data. |
| `page_url` | Some schema classes use this to build a URL from the homepage URL plus a relative path. |

`custom` supports nested arrays. Empty custom values are skipped.

## Graph Rendering

### `add($name, ?array $options = null, ?Page $page = null)`

Build a bundled schema and add it to the current graph.

```php
$jsonld
    ->clearGraph()
    ->add('WebSite')
    ->add('WebPage')
    ->add('BreadcrumbList');
```

Returns `$this`, so calls can be chained.

### `addNode(array $node)`

Add one custom schema node to the current graph.

```php
$jsonld->addNode([
    '@type' => 'Service',
    '@id' => rtrim($page->httpUrl, '/') . '/#service',
    'name' => $page->title,
    'url' => $page->httpUrl,
    'provider' => [
        '@id' => rtrim($homepage->httpUrl, '/') . '/#organization',
    ],
]);
```

Use this for site-specific schema that is not covered by the bundled schema classes.

### `addNodes(array $nodes)`

Add multiple custom schema nodes to the current graph.

```php
$nodes = [
    [
        '@type' => 'Service',
        '@id' => rtrim($page->httpUrl, '/') . '/#service',
        'name' => $page->title,
    ],
    [
        '@type' => 'Person',
        '@id' => rtrim($author->httpUrl, '/') . '/#person',
        'name' => $author->title,
    ],
];

$jsonld->addNodes($nodes);
```

Non-array items in `$nodes` are ignored.

### `renderGraph(?array $options = null)`

Render all collected graph nodes as one JSON-LD `@graph`.

```php
echo $jsonld
    ->clearGraph()
    ->add('WebSite', [
        'custom' => [
            '@id' => rtrim($homepage->httpUrl, '/') . '/#website',
            'url' => $homepage->httpUrl,
        ],
    ])
    ->add('WebPage', [
        'custom' => [
            '@id' => rtrim($page->httpUrl, '/') . '/#webpage',
            'isPartOf' => [
                '@id' => rtrim($homepage->httpUrl, '/') . '/#website',
            ],
        ],
    ])
    ->add('BreadcrumbList')
    ->renderGraph([
        'script' => true,
        'pretty' => true,
    ]);
```

Options:

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `script` | `bool` | `false` | When true, wraps the JSON in a `<script type="application/ld+json">` tag. |
| `pretty` | `bool` | `false` | When true, renders pretty-printed JSON. |
| `clear` | `bool` | `true` | When true, clears the collected graph after rendering. |

Returns an empty string when no graph nodes have been collected.

### `getGraph()`

Return the currently collected graph nodes as an array.

```php
$jsonld->clearGraph()->add('WebPage');
$nodes = $jsonld->getGraph();
```

This does not clear the graph.

### `clearGraph()`

Clear all collected graph nodes and graph indexes.

```php
$jsonld->clearGraph();
```

Call this before building a page graph when reusing the module instance.

## Graph Behavior

Graph rendering normalizes collected nodes for single-script output:

- Individual node `@context` values are removed.
- The rendered graph has one top-level `@context` of `https://schema.org`.
- Nodes with the same scalar `@id` are merged.
- When duplicate `@id` nodes contain conflicting populated values, the first populated value wins.
- Later duplicate nodes can fill missing nested associative values.
- Nodes without `@id` are keyed by a hash of their content, so identical raw nodes are deduplicated.
- Empty values are removed from graph nodes before rendering.

## Bundled Schema Names

Bundled schema classes live in `schemas/jsonld.*.php` and can be rendered by their schema name:

| Schema name | Description |
|-------------|-------------|
| `Article` | Article schema for page content. |
| `Brand` | Brand schema/reference. |
| `BreadcrumbList` | Breadcrumb trail for the current page. |
| `CollectionPage` | Collection page schema. |
| `Custom` | Empty schema shell for custom data. |
| `Event` | Event schema. |
| `FAQPage` | FAQPage schema from provided question/answer items. |
| `IPPNews` | Project-specific news schema included with the module. |
| `LocalBusiness` | LocalBusiness or subtype schema from config/options. |
| `NewsArticle` | NewsArticle schema. |
| `Organization` | Organization schema from config/options. |
| `Person` | Person schema, primarily driven by explicit options. |
| `Product` | Product schema with optional offers. |
| `WebPage` | WebPage schema for the current page. |
| `WebSite` | WebSite schema for the site. |

## Hooks

The primary single-schema methods are hookable because they are implemented with ProcessWire's triple-underscore method convention.

| Hook | When | Arguments |
|------|------|-----------|
| `MarkupJsonLDSchema::render` | Before or after rendering one schema as JSON. | `$name`, `$options`, `$page` |
| `MarkupJsonLDSchema::getSchema` | Before or after building one schema array. | `$name`, `$options`, `$page` |

Example:

```php
$wire->addHookAfter('MarkupJsonLDSchema::getSchema', function(HookEvent $event) {
    $schema = $event->return;
    if(!is_array($schema)) return;

    if(($schema['@type'] ?? '') === 'WebPage') {
        $schema['inLanguage'] = $event->wire()->user->language->name;
    }

    $event->return = $schema;
});
```

## Multilingual Notes

The module uses ProcessWire page URL APIs such as `$page->httpUrl` and `$homepage->httpUrl`. On multilingual sites, generated URLs follow the current language's configured root path and page slugs.

Schema.org property keys remain English. Values such as `name`, `description`, and breadcrumb labels can be localized when the underlying ProcessWire fields have translated values.

## Notes

- Access the module with `$modules->get('MarkupJsonLDSchema')`.
- `render()` is the backward-compatible API for existing templates.
- `renderGraph()` is the preferred API for outputting one JSON-LD script per page.
- `renderGraph()` clears graph state by default after rendering.
- Use `addNode()` or `addNodes()` for site-specific schema structures that are not represented by bundled schema classes.
- The module extends [[WireData]] and implements [[Module]] and [[ConfigurableModule]].

**Source file:** `site/modules/MarkupJsonLDSchema/MarkupJsonLDSchema.module`
