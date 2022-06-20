<?php

namespace App\Traits;

use App\Helpers\Files;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait Configuration
{
    /**
     * Get the configuration.
     *
     * @return void
     */
    public function getConfiguration()
    {
        if (! Storage::disk('config')->exists('config.json')) {
            $this->error("Config file not found. Creating a new one in: " . config('filesystems.disks.config.root'));

            Files::createConfigFile();

            $this->info('Please edit the new config file to ensure they are to your preference then run the command again.');
            exit;
        } else {
            $config = Storage::disk('config')->get('config.json');
            $this->config = json_decode($config, true);
        }
    }

    public function setup()
    {
        // Set name
        $this->name = $this->argument('name');

        // Set Locations
        $this->installLocation = $this->config['install-location'] ?  config('filesystems.disks.local.root') . $this->config['install-location'] : getcwd();
        $this->applicationLocation = $this->installLocation . (Str::endsWith($this->installLocation, '/') ? '' : '/') . $this->name;

        // Set Laravel Options
        $selected = [];

        // Collect the options from the config file.
        $defaultOptions = Collection::wrap($this->config['laravel-options'] ?: []);

        // Collect the options and filter any that are false, null or empty.
        $options = Collection::wrap([
            '--dev' => $this->option('dev'),
            '--git' => $this->option('git'),
            '--branch' => $this->option('git') || $this->option('github') ? $this->option('branch') : false,
            '--github' => $this->option('github'),
            '--organization' => $this->option('organization'),
            '--jet' => $this->option('jet'),
            '--stack' => $this->option('stack'),
            '--teams' => $this->option('teams'),
            '--prompt-jetstream' => $this->option('prompt-jetstream'),
            '--force' => $this->option('force'),
        ])->filter(function ($item) {
            if ($item != false || $item != null || $item != '') {
                return true;
            }
        });

        // Merge the options with the default options.
        // Filter any that are false, null or empty.
        // Iteate the options and add them to the selected array converting them to a string of key and value.
        $defaultOptions
            ->merge($options)
            ->filter(function ($item) {
                if ($item != false || $item != null || $item != '') {
                    return true;
                }
            })
            ->each(function ($item, $key) use (&$selected) {
                $selected[] = gettype($item) == "boolean" ? $key : $key . '=' . $item;
            });

        $this->laravelOptions = implode(' ', $selected);

        $this->forced = in_array('--force', $selected);
    }

    /**
     * Check configuration.
     *
     * @return void
     */
    public function checkConfiguration()
    {
        if (! $this->config) {
            $this->error('The config file is empty. Please run `laravel-installer-plus install` to set up.');
            exit;
        }

        if (! key_exists('install-location', $this->config)) {
            $this->error('The config file is missing the install-location. Please set an install path.');
            exit;
        }

        if (! key_exists('laravel-options', $this->config)) {
            $this->error('The config file is missing the laravel-options. Please set laravel options.');
            exit;
        }

        if (
            ! key_exists('packages-to-install', $this->config) ||
            ! key_exists('composer', $this->config['packages-to-install']) ||
            ! key_exists('npm', $this->config['packages-to-install'])
        ) {
            $this->error('Either packages-to-install, packages-to-install->composer or packages-to-install->npm is missing from the config.');
            exit;
        }

        if (
            ! key_exists('packages', $this->config) ||
            ! key_exists('composer', $this->config['packages']) ||
            ! key_exists('npm', $this->config['packages'])
        ) {
            $this->error('Either packages, packages->composer or packages->npm is missing from the config.');
            exit;
        }

        // Check to see if user is trying to install composer packages but hasnt set any packages.
        if (
            count($this->config['packages-to-install']['composer']) > 0 &&
            ! count($this->config['packages']['composer']) > 0
        ) {
            $this->error('You are trying to install composer packages but you have not set any composer packages in the config.');
            exit;
        }

        // Check to see if user is trying to install npm packages but hasnt set any packages.
        if (
            count($this->config['packages-to-install']['npm']) > 0 &&
            ! count($this->config['packages']['npm']) > 0
        ) {
            $this->error('You are trying to install npm packages but you have not set any npm packages in the config.');
            exit;
        }

        // Check to see if user is trying to install more composer packages but has asked for more than that is set.
        if (count($this->config['packages-to-install']['composer']) > count($this->config['packages']['composer'])) {
            $this->error('You are trying to install more composer packages than what is set in the config.');
            exit;
        }

        // Check to see if user is trying to install more npm packages but has asked for more than that is set.
        if (count($this->config['packages-to-install']['npm']) > count($this->config['packages']['npm'])) {
            $this->error('You are trying to install more npm packages than what is set in the config.');
            exit;
        }

        $this->info('Configuration check passed.');
    }

    public function settingsCheck()
    {
        if (
            $this->forced == false &&
            Storage::exists(Str::replaceFirst(config('filesystems.disks.local.root'), '', $this->applicationLocation))
        ) {
            $this->error('The application already exists. Please run `laravel-installer-plus ' . $this->name . ' --force` to set up.');
            exit;
        }

        $this->info('Settings check passed.');
    }
}
