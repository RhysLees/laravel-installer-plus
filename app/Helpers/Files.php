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
                    'laravel-echo',
                    'pusher-js'
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
        ], JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES));
    }

    public static function createHelpFile()
    {
        Storage::put('help.md', <<<EOT
            # Laravel Installer Plus
            =====================================================================
            .This is a helper tool for the installation of laravel.
            .It will install the laravel application and the packages you specify.

            ## Install Location
            - The location where the laravel application will be installed.
            - The default is `~/Websites/`
            - You can change this in the config.json file.

            ## Packages to install
            - The packages that will be installed.
            - The default is `composer` and `npm`.
            - You can change this in the config.json file.
            - You can also add packages to the `packages` array.

            ### Packages to install Example
            ```json
            "packages-to-install": {
                "composer": [
                    "spatie-laravel-ray",
                    "barryvdh-laravel-debugbar",
                ],
                "npm": [
                    "laravel-echo",
                    "pusher-js"
                ]
            },
            ```

            ## Packages
            - The package that can be installed.
            - You can change this in the config.json file.
            - The packages are installed in the order you specify.
            - You can install packages with both composer and npm.

            ### Package Example

            ```json
            "packages": {
                "composer": [
                    {
                        "name": "Laravel Ray",
                        "key": "spatie-laravel-ray",
                        "commands": [
                            "composer require spatie/laravel-ray",
                            "php artisan ray:publish-config"
                        ]
                    }
                ],
                "npm": [
                    {
                        "name": "Laravel Echo",
                        "key": "echo",
                        "commands": [
                            "npm i laravel-echo",
                        ],

                    }
                ]
            },
            ```

            ## Commands
            The commands are executed in the order you specify.
            You can execute commands with composer and npm.

            ### Commands Example
            ```json
            "commands": {
                "composer": {
                    "pre-install": [
                        'sudo composer self-update',
                        'composer cache-clear
                    ],
                    "post-install": [
                        'php artisan migrate:fresh --seed',
                        'valet restart',
                        'valet link',
                        'valet secure',
                    ],
                    "pre-package": [
                        'composer update',
                    ],
                    "post-package": [
                        'composer dump-autoload',
                    ],
                },
                "npm": {
                    "pre-install": [
                        'npm install -g npm@latest',
                    ],
                    "post-package": [
                        'npm run dev',
                    ],
                },
            }
            ```

            ### Pre Install
            The commands that will be run before the installation of laravel.

            ### Post Install
            The commands that will be run after the installation of laravel.

            ### Pre Package
            The commands that will be run before the installation of packages.

            ### Post Package
            The commands that will be run after the installation of packages.
            EOT
        );
    }
}
