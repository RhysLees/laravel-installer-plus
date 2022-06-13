<?php

namespace App\Commands;

use App\Packages\Spatie\LaravelRay;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class Install extends Command
{
    public $name;
    public $installLocation;
    public $applicationLocation;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'install
                            {name : The name of the application}
                            {--options : The options to setup the package}';

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
        $this->name = $this->argument('name');
        $this->installLocation = '~/Websites/';
        $this->applicationLocation = $this->installLocation . $this->name . '/';


        $this->preInstallCommands();
        $this->installLaravel();
        $this->postInstallCommands();


        $packages = config('packages.composer');
        foreach ($packages as $key => $package) {
            $this->task("Install Composer Package", function () use ($package) {
                $this->installPackage($package);

                return true;
            });
        }
    }

    /**
     * Execute a console command.
     *
     * @param string $command
     * @return void
     */
    public function executeCommand($command)
    {
        exec('cd ' . $this->applicationLocation . ' && ' . $command);
    }

    /**
     * Execute multiple commands
     *
     * @param array $commands
     * @return string
     */
    public function executeCommands($commands)
    {
        $this->executeCommand(implode(' && ', $commands));
    }



    /**
     * Install Laravel.
     *
     * @return void
     */
    public function installLaravel()
    {
        $this->task("Install application in {$this->installLocation}", function () {
            $this->executeCommand('laravel new ' . $this->name);

            return true;
        });
    }

    /**
     * Pre Install Commands.
     *
     * @return void
     */
    public function preInstallCommands()
    {
        if (count(config('install-commands.pre')) > 0) {
            $this->task("Pre Install Commands ...", function () {
                exec(implode(' && ', config('install-commands.pre')));

                return true;
            });
        }
    }

    /**
     * Post Install Commands.
     *
     * @return void
     */
    public function postInstallCommands()
    {
        if (count(config('install-commands.pre')) > 0) {
            $this->task("Post Install Commands ...", function () {
                $this->executeCommands(config('install-commands.post'));

                return true;
            });
        }
    }

    /**
     * Install Package.
     *
     * @param array $package
     * @return void
     */
    public function installPackage($package)
    {
        $this->task("Install Package {$package['name']}", function () use ($package) {
            $this->executeCommand('npm install ' . $package['name']);

            return true;
        });
    }
}
