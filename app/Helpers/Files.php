<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class Files
{
    public static function createConfigFile()
    {
        Storage::put('config.json', json_encode([
            'install-location' => '~/Websites/',
            'packages-to-install' => [
                'composer' => [
                    'spatie-laravel-ray',
                    'barryvdh-laravel-debugbar',
                ],
                'npm' => [
                ]
            ],
            'packages' => [
                'composer' => [
                    [
                        'name' => 'Laravel Ray',
                        'key' => 'spatie-laravel-ray',
                        'commands' => [
                            'composer require spatie/laravel-ray',
                            'php artisan ray:publish-config'
                        ],
                    ],
                    [
                        'name' => 'Laravel Debugbar',
                        'key' => 'barryvdh-laravel-debugbar',
                        'commands' => [
                            'composer require barryvdh/laravel-debugbar  --dev',
                        ],
                    ],
                ],
                'npm' => [
                    [
                        'name' => 'Laravel Echo',
                        'key' => 'laravel-echo',
                        'commands' => [
                            'npm install --save-dev laravel-echo',
                        ],
                    ],
                    [
                        'name' => 'Pusher Js',
                        'key' => 'pusher-js',
                        'commands' => [
                            'npm install --save-dev pusher-js',
                        ],
                    ],
                ],
            ],
            'commands' => [
                'composer' => [
                    'pre-install' => [
                        // Commands to run before the installation of laravel.
                    ],
                    'post-install' => [
                        // Commands to run after the installation of laravel.
                    ],
                    'pre-package' => [
                        // Commands to run before the installation of packages.
                    ],
                    'post-package' => [
                        // Commands to run after the installation of packages.
                    ],
                ],
                'npm' => [
                    'pre-install' => [
                        // Commands to run before the installation of laravel.
                    ],
                    'post-install' => [
                        // Commands to run after the installation of laravel.
                    ],
                    'pre-package' => [
                        // Commands to run before the installation of packages.
                    ],
                    'post-package' => [
                        // Commands to run after the installation of packages.
                    ],
                ],
            ]
        ], JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES));
    }
}
