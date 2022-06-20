<?php

namespace App\Commands;

use App\Traits\Configuration;
use App\Traits\Execute;
use App\Traits\Packages;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class NewCommand extends Command
{
    use Configuration;
    use Packages;
    use Execute;

    public $config;
    public $packagesComposer;
    public $packagesNpm;
    public $name;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'new
                            {name : Name of the application}';


    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install a new laravel application with your personal defaults.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->getConfiguration();
        $this->checkConfiguration();

        $this->collectPackages();

        $this->info("Creating a new application in: " . $this->config['install-location']);

        // Pre Install Commands
        $this->runCommands('composer', 'pre-install');
        $this->runCommands('npm', 'pre-install');

        $this->installLaravel();

        // Post Install Commands
        $this->runCommands('composer', 'post-install');
        $this->runCommands('npm', 'post-install');


        // Pre Package Commands
        $this->runCommands('composer', 'pre-package');
        $this->runCommands('npm', 'pre-package');

        $this->installPackages('composer');
        $this->installPackages('npm');

        // Post Package Commands
        $this->runCommands('composer', 'post-package');
        $this->runCommands('npm', 'post-package');

        return $this->info("Application created successfully.");
    }

    /**
     * Install Laravel.
     *
     * @return void
     */
    public function installLaravel()
    {
        $this->task("Install application in {$this->config['install-location']}", function () {
            exec('cd ' . $this->config['install-location'] .  ' && laravel new ' . $this->name, $output, $result);

            return true;
        });
    }

    /**
     * Install Package.
     *
     *
     * @return void
     */
    public function installPackages($manager)
    {
        if (count($this->config['packages-to-install'][$manager]) > 0) {
            $this->info("Installing " . Str::title($manager) . " packages...");
            $this->packages[$manager]->each(function ($package) {
                $this->task("Install {$package['name']}", function () use ($package) {
                    $this->executeCommands($package['commands']);

                    return true;
                });
            });
        }
    }

    /**
     * Run Commands.
     *
     * @return void
     */
    public function runCommands($manager, $method)
    {
        if (count($this->config['commands'][$manager][$method]) > 0) {
            $title = Str::of($manager . ' ' . $method)
                        ->replace('-', ' ')->title() . ' Commands ...';

            $this->task($title, function () use ($manager, $method) {
                $location = false;
                if ($method == 'pre-install') {
                    $location = true;
                }

                $this->executeCommands($this->config['commands'][$manager][$method], $location);

                return true;
            });
        }
    }
}
