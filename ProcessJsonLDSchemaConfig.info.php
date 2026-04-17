<?php namespace ProcessWire;

$info = [
    'title'      => 'Json-LD Schema Config',
    'version'    => '0.1.0',
    'summary'    => 'Permite configurar el módulo MarkupJsonLDSchema sin acceso a module-admin.',
    'author'     => 'Lex Sanchez',
    'href'       => "http://www.lexsanchez.com/",
    'permission' => 'jsonld-schema-config',
    'permissions' => [
        'jsonld-schema-config' => 'Acceso a la configuración de Json-LD Schema',
    ],
    'page' => [
        'name'   => 'jsonld-schema-config',
        'parent' => 'setup',
        'title'  => 'Json-LD Schema',
    ],
    'requires'  => ['MarkupJsonLDSchema'],
    'icon'      => 'code',
];
