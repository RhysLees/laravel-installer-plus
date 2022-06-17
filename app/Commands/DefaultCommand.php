<?php

namespace App\Commands;

use App\Helpers\Files;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class DefaultCommand extends Command
{
    public $configFile;
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
        $this->name = $this->argument('name');
        $this->getConfiguration();

        $this->checkConfiguration();

        //Check name was given
        // $this->createApplication();

        return true;
    }

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
            Files::createHelpFile();

            $this->info('Please edit the new config file to ensure they are to your preference then run the command again.');
            exit;
        } else {
            $config = Storage::get('config.json');
            $this->configFile = json_decode($config, true);
        }
    }

    /**
     * Check configuration.
     *
     * @return void
     */
    public function checkConfiguration()
    {
        if (! $this->configFile) {
            $this->error('The config file is empty. Please run `laravel-installer-plus init --force` to set up.');
            exit;
        }

        if ($this->configFile['install-location'] === '') {
            $this->error('The config file is missing the install-location. Please set an install path.');
            exit;
        }

        if (
            ! $this->configFile['packages-to-install'] ||
            ! $this->configFile['packages-to-install']['composer'] ||
            ! $this->configFile['packages-to-install']['npm']
        ) {
            $this->error('Either packages-to-install, packages-to-install->composer or packages-to-install->npm is missing from the config.');
            exit;
        }

        if (
            ! $this->configFile['packages'] ||
            ! $this->configFile['packages']['composer'] ||
            ! $this->configFile['packages']['npm']
        ) {
            $this->error('Either packages, packages->composer or packages->npm is missing from the config.');
            exit;
        }

        $this->info('Configuration check passed.');
    }

    /**
     * Create a new application.
     *
     * @return void
     */
    public function createApplication()
    {
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


    // public function showSettings()
    // {
    //     $option = $this->menu('Laravel Installer Plus Settings', [
    //         'Show Config File',
    //     ])->setForegroundColour('green')
    //     ->setBackgroundColour('black')
    //     ->open();

    //     if ($option == 0) {
    //         $this->input();
    //     }

    //     $this->info('Laravel Installer Plus Settings:');
    //     $this->info(config('filesystems.disks.local.root'));
    //     return true;
    // }
}
