<?php

namespace App\Commands;

use App\Packages\Spatie\LaravelRay;
use Dotenv\Dotenv;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
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
    protected $signature = 'install';

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
        Storage::put('test.json', 'test');
        dd(getcwd());
        $option = $this->menu('Laravel Installer Plus', [
            'Create a new application',
            'Configure your application defaults',
            'Settings',
        ])
        ->setForegroundColour('green')
        ->setBackgroundColour('black')
        ->open();

        $this->info("You have chosen the option number #$option");


        $this->name = $this->argument('name');
        $this->installLocation = env(getcwd());
        $this->applicationLocation = $this->installLocation . $this->name . '/';

    //     dd(is_dir($this->installLocation));

    //     if (! is_dir($this->installLocation)) {
    //         return $this->error("Install location '{$this->installLocation}' does not exist.");
    //     }


    //     $this->preInstallCommands();
    //     $this->installLaravel();
    //     $this->postInstallCommands();


    //     $packages = config('packages.composer');
    //     foreach ($packages as $key => $package) {
    //         $this->task("Install Composer Package", function () use ($package) {
    //             $this->installPackage($package);

    //             return true;
    //         });
    //     }
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
