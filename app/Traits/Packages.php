<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait Packages
{
    /**
     * Collect Packages
     *
     * @return void
     */
    public function collectPackages()
    {
        $this->info('Collecting packages...');

        // Get composer repositories.
        $repositories = Collection::wrap($this->config['repositories'])
                                        ->whereInStrict('key', $this->config['packages-to-install']['composer']);

        // Get composer packages.
        $packagesComposer = Collection::wrap($this->config['packages']['composer'])
                                        ->whereInStrict('key', $this->config['packages-to-install']['composer']);

        if ($packagesComposer->count() != count($this->config['packages-to-install']['composer'])) {
            $this->error('You are trying to install composer packages but you have not set some composer packages you require in the config.');
            exit;
        }

        // Get npm packages.
        $packagesNpm = Collection::wrap($this->config['packages']['npm'])
                                        ->whereInStrict('key', $this->config['packages-to-install']['npm']);

        if ($packagesNpm->count() != count($this->config['packages-to-install']['npm'])) {
            $this->error('You are trying to install npm packages but you have not set some npm packages you require in the config.');
            exit;
        }

        $this->packages = Collection::wrap([
            'repositories' => $repositories,
            'composer' => $packagesComposer,
            'npm' => $packagesNpm,
        ]);

        $this->info('Packages collected.');
    }
}
