<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Manifest
    |--------------------------------------------------------------------------
    |
    | This is the path to the manifest file that holds the colors.
    | You can customize it if you want to use a different JSON file.
    |
    */
    'default_manifest' => resource_path('theme-generator/manifest.json'),

    /*
    |--------------------------------------------------------------------------
    | CSS Files Directory
    |--------------------------------------------------------------------------
    |
    | This option allows you to define where your package will look for CSS files
    | to modify. By default, it is set to 'resources/theme-generator/assets'.
    |
    */
    'css_directory' => resource_path('theme-generator/assets'),
    'theme_file_path' => resource_path('theme-generator/theme.json'),
    'image_cache_path' => resource_path('theme-generator/image-cache.json'),

    /*
    |--------------------------------------------------------------------------
    | Output Directory
    |--------------------------------------------------------------------------
    |
    | This option defines where the generated CSS files will be placed.
    | By default, it's set to the 'public/assets/theme-generator' folder.
    |
    */
    'output_directory' => public_path('assets'),

    /*
    |--------------------------------------------------------------------------
    | Default Colors
    |--------------------------------------------------------------------------
    |
    | Define the default color palette for the themes. These colors can be
    | overridden by the 'manifest.json' file.
    |
    */
    'default_colors' => [
        '#287F7A',  // Primary Color
        '#FF5722',  // Secondary Color
        '#4CAF50',  // Accent Color
        '#FFFFFF',  // Background Color
    ],

    /*
    |--------------------------------------------------------------------------
    | Force overwrite of existing CSS files
    |--------------------------------------------------------------------------
    |
    | If this is set to true, it will overwrite existing files in the
    | output directory when running the theme:generate command.
    |
    */
    'force_overwrite' => false,

    /*
    |--------------------------------------------------------------------------
    | Enable Logging
    |--------------------------------------------------------------------------
    |
    | If set to true, the package will log all actions, such as theme generation,
    | errors, or missing files. This is useful for debugging.
    |
    */
    'enable_logging' => true,
];
