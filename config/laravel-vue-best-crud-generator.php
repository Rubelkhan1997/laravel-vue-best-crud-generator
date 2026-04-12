<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Module Namespace
    |--------------------------------------------------------------------------
    |
    | This value is the default namespace for generated modules.
    | You can override this per-generation with the --module flag.
    |
    */
    'default_module' => 'FrontDesk',

    /*
    |--------------------------------------------------------------------------
    | Stubs Path
    |--------------------------------------------------------------------------
    |
    | Path to stub templates. Can be customized by publishing stubs.
    |
    */
    'stubs_path' => base_path('stubs/laravel-vue-best-crud-generator'),

    /*
    |--------------------------------------------------------------------------
    | Default Pagination
    |--------------------------------------------------------------------------
    */
    'per_page' => 15,

    /*
    |--------------------------------------------------------------------------
    | Enable Soft Deletes by default
    |--------------------------------------------------------------------------
    */
    'soft_deletes' => true,

    /*
    |--------------------------------------------------------------------------
    | Generate Tests
    |--------------------------------------------------------------------------
    */
    'generate_tests' => true,

    /*
    |--------------------------------------------------------------------------
    | Generate Resource Classes
    |--------------------------------------------------------------------------
    */
    'generate_resources' => true,

    /*
    |--------------------------------------------------------------------------
    | Frontend Default Folders
    |--------------------------------------------------------------------------
    */
    'frontend' => [
        'types_path' => 'resources/js/Types',
        'stores_path' => 'resources/js/Stores',
        'composables_path' => 'resources/js/Composables',
        'pages_path' => 'resources/js/Pages',
        'mappers_path' => 'resources/js/Utils/Mappers',
    ],

    /*
    |--------------------------------------------------------------------------
    | Backend Default Folders
    |--------------------------------------------------------------------------
    */
    'backend' => [
        'models_path' => 'app/Modules',
        'services_path' => 'app/Modules',
        'controllers_path' => 'app/Modules',
        'requests_path' => 'app/Modules',
        'resources_path' => 'app/Modules',
    ],
];
