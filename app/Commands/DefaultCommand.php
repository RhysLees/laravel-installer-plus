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

class DefaultCommand extends Command
{
    public $name;
    public $installLocation;
    public $applicationLocation;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'new {name? : The name of the application}';

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
        if ($this->argument('name')) {
            $this->createApplication();
            return $this->info('Enjoy!');
        }

        $option = $this->menu('Laravel Installer Plus', [
            'Create a new application',
            'Settings',
        ])->setForegroundColour('green')
        ->setBackgroundColour('black')
        ->open();

        if ($option == 0) {
            $this->showCreate();
        } elseif ($option == 1) {
            $this->showSettings();
        }

        return true;
    }

    public function showSettings()
    {
        // $option = $this->menu('Laravel Installer Plus Settings', [
        //     'Show Config File',
        // ])->setForegroundColour('green')
        // ->setBackgroundColour('black')
        // ->open();

        // if ($option == 0) {
        //     $this->input();
        // }

        $this->info('Laravel Installer Plus Settings:');
        $this->info(config('filesystems.disks.local.root'));
        return true;
    }

    /**
     * Create a new application.
     *
     * @return void
     */
    public function createApplication()
    {
        if (! Storage::exists('config.json')) {
            $this->info("Config file not found. Creating a new one in: " . config('filesystems.disks.local.root'));
            Storage::put('config.json', json_encode([
                'name' => $this->argument('name'),
            ]));
        }

        $this->info("Creating a new application in: " . config('filesystems.disks.local.root'));
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
