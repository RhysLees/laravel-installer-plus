# Laravel Installer Plus

<p>
  <a href="https://packagist.org/packages/rhyslees/laravel-installer-plus"><img src="https://img.shields.io/packagist/l/rhyslees/laravel-installer-plus.svg" alt="License"></a>
</p>

#### This is a **community project** and not an official Laravel one

- This is a cli tool for the installation of laravel.
- It will install the laravel application and the packages you specify.

> Built with [Laravel Zero](https://laravel-zero.com/).

---
# Requirements

- [Laravel Installer](https://laravel.com/docs/#the-laravel-installer)
- [Composer](https://getcomposer.org/download).
- [PHP](https://www.php.net/) 8.0 or higher.

> IMPORTANT: This Package assumes that you have laravel-installer, composer and php installed globally.
---

# Installation

```bash
$ composer global require rhyslees/laravel-installer-plus
$ laravel-installer-plus install
```

Your configuration is stored in `$HOME/.laravel-installer-plus/config.json`

---

# Documentation

---

## Options

All Laravel Installer options are included in Laravel Installer plus so you can optionally pass them.

```name : Name of the application (required)
    --dev : Create a development environment
    --git : Initialize a git repository
    --branch : Initialize the application with the given branch
    --github : Create a new repository on GitHub
    --organization : The GitHub under the given organization
    --jet : Installs the Laravel Jetstream scaffolding
    --stack : The Jetstream stack that should be installed
    --teams : Indicates whether Jetstream should be scaffolded with team support
    --prompt-jetstream : Issues a prompt to determine if Jetstream should be installed
    --force : Overwrite existing files
```

> NOTE: If you use options such as `--git` for every appliction you can add them to `laravel-options` in the config and they will be appended to the command so you dont need to specifiy them manually (see example below).

```json
"laravel-options": {
    "--branch": "green",
    "--git": true
},
```

---


## Project Install Location
- The location where the laravel application will be installed.

If you have a directory where all projects exist, you can set it `install-location` in `$HOME/.laravel-installer-plus/config.json`. This will then run the install commands to that directory no matter where you run the command from.

When changing this remember you only specify the path relative to your home directory.

> macOS / GNU / Linux Distributions: `$HOME/`

For example if you use a folder named `Projects` in your home directory, you can set the install location to `Projects`.

```json
"install-location": "Projects"
```

Leaving this option as an empty string will use the current directory.

> NOTE: If you are unsure if you have set this correctly, you can check by running `laravel-installer-plus location {name}` which will output where it would install the application.

---

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

---

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

---

## Repositories

If you need to require a private package, first add the package to the `packages->composer` array. You can then add the repository to the `repositories` array.

```json
"repositories": [
    {
        "key": "",
        "name": "",
        "type": "",
        "url": ""
    }
],
```

`key` must match the package key.
`name` is the name of the package (see example below).
`type` is the type of repository.
`url` is the url of the repository.

With VCS:
```json
"repositories": [
    {
        "key": "spatie-laravel-ray",
        "name": "laravel-ray",
        "type": "vcs",
        "url": "https://github.com/spatie/laravel-ray"
    }
],
```

With Path (symlink):
```json
"repositories": [
    {
        "key": "spatie-laravel-ray",
        "name": "laravel-ray",
        "type": "path",
        "url": "../../Packages/spatie/laravel-ray"
    }
],
```

> NOTE: when using path ensure the url is relative to your project directory.

---

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

---

####TODO

- [x] Make it posible to install from current working directory.
- [ ] Add support for manipluating files such as .env, app.css etc.
- [ ] Move away from using laravel/installer to install laravel.


# License

Laravel Installer Plus is an open-source software licensed under the MIT license.
