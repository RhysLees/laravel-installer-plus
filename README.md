# Laravel Installer Plus

<p align="center">
  <a href="https://packagist.org/packages/rhyslees/laravel-installer-plus"><img src="https://img.shields.io/packagist/l/rhyslees/laravel-installer-plus.svg" alt="License"></a>
</p>

#### This is a **community project** and not an official Laravel one

- This is a helper tool for the installation of laravel.
- It will install the laravel application and the packages you specify.

> Built with [Laravel Zero](https://laravel-zero.com/).


------

# Installation
1. Download the latest released phar file `laravel-installer-plus` from [GitHub Releases](https://github.com/RhysLees/laravel-installer-plus/releases/latest/download/laravel-installer-plus)
2. Open a terminal where you downloaded and run the following command
```bash
sudo mv laravel-installer-plus /usr/local/bin/laravel-installer-plus
```
3. Run the following command to complete installation and create a default config file (location: `~/.laravel-installer-plus`)
```bash
laravel-installer-plus install
```


# Documentation

## Install Location
- The location where the laravel application will be installed.
- The default is `~/Websites/`
- You can change this in the config.json file.

## Packages to install
- The packages that will be installed.
- The `composer` and `npm`.
- You can change this in the config.json file.
- You need add packages to the `packages` array.
- Specify the key of the package you want to install

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
- The commands are executed in the order you specify.
- You can execute commands with composer and npm.

> NOTE: If you wish to run sudo commands you will be prompted for your password at each command step i.e pre-install, post-install, pre-package, post-package.

### Commands Example
```json
"commands": {
    "composer": {
        "pre-install": [
            "sudo composer self-update",
            "composer cache-clear"
        ],
        "post-install": [
            "php artisan migrate:fresh --seed",
            "valet restart",
            "valet link",
            "valet secure",
        ],
        "pre-package": [
            "composer update",
        ],
        "post-package": [
            "composer dump-autoload",
        ],
    },
    "npm": {
        "pre-install": [
            "npm install -g npm@latest",
        ],
        "post-package": [
            "npm run dev",
        ],
    },
}
```

### Pre Install
The commands that will be run before the installation of laravel.
> IMPORTANT: Pre Install commands are run in the install-location not the application location.


### Post Install
The commands that will be run after the installation of laravel.

### Pre Package
The commands that will be run before the installation of packages.

### Post Package
The commands that will be run after the installation of packages.

## License

Laravel Installer Plus is an open-source software licensed under the MIT license.
