<?php

namespace App\Traits;

use App\Helpers\Files;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

trait Configuration
{
    /**
     * Get the configuration.
     *
     * @return void
     */
    public function getConfiguration()
    {
        if (! Storage::exists('config.json')) {
            $this->error("Config file not found. Creating a new one in: " . config('filesystems.disks.local.root'));

            Files::createConfigFile();

            $this->info('Please edit the new config file to ensure they are to your preference then run the command again.');
            exit;
        } else {
            $config = Storage::get('config.json');
            $this->config = json_decode($config, true);
            $this->applicationLocation = $this->config['install-location'] . $this->name;
        }
    }

    /**
     * Check configuration.
     *
     * @return void
     */
    public function checkConfiguration()
    {
        if (! $this->config) {
            $this->error('The config file is empty. Please run `laravel-installer-plus init --force` to set up.');
            exit;
        }

        if (
            ! $this->config['install-location']
        ) {
            $this->error('The config file is missing the install-location. Please set an install path.');
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
        if (
            count($this->config['packages-to-install']['composer']) > count($this->config['packages']['composer'])
        ) {
            $this->error('You are trying to install more composer packages than what is set in the config.');
            exit;
        }

        // Check to see if user is trying to install more npm packages but has asked for more than that is set.
        if (
            count($this->config['packages-to-install']['npm']) > count($this->config['packages']['npm'])
        ) {
            $this->error('You are trying to install more npm packages than what is set in the config.');
            exit;
        }

        $this->info('Configuration check passed.');
    }
}
