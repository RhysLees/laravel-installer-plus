<?php

namespace App\Commands;

use App\Traits\Configuration;
use App\Traits\Execute;
use App\Traits\Packages;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use RuntimeException;
use Symfony\Component\Process\Process;

class NewCommand extends Command
{
    use Configuration;
    use Packages;
    use Execute;

    public $name;
    public $config;
    public $packages;
    public $laravelOptions;
    public $installLocation;
    public $applicationLocation;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'new
                            {name : Name of the application}
                            {--dev : Create a development environment}
                            {--git : Initialize a git repository}
                            {--branch= : Initialize the application with the given branch}
                            {--github : Create a new repository on GitHub}
                            {--organization= : The GitHub under the given organization}
                            {--jet : Installs the Laravel Jetstream scaffolding}
                            {--stack= : The Jetstream stack that should be installed}
                            {--teams : Indicates whether Jetstream should be scaffolded with team support}
                            {--prompt-jetstream : Issues a prompt to determine if Jetstream should be installed}
                            {--force : Overwrite existing files}';


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
        // Run Pre Checks
        $this->getConfiguration();
        $this->checkConfiguration();
        $this->setup();
        $this->settingsCheck();

        $this->collectPackages();

        $this->info("Creating a new application in: " . $this->installLocation);

        // Pre Install Commands
        $this->runCommands('composer', 'pre-install');
        $this->runCommands('npm', 'pre-install');

        $this->info(PHP_EOL . "
         ___         _        _ _ _             _                          _
        |_ _|_ _  __| |_ __ _| | (_)_ _  __ _  | |   __ _ _ _ __ ___ _____| |
         | || ' \(_-<  _/ _` | | | | ' \/ _` | | |__/ _` | '_/ _` \ V / -_) |
        |___|_||_/__/\__\__,_|_|_|_|_||_\__, | |____\__,_|_| \__,_|\_/\___|_|
                                        |___/
        " . PHP_EOL . PHP_EOL);

        $this->installLaravel();

        // Post Install Commands
        $this->runCommands('composer', 'post-install');
        $this->runCommands('npm', 'post-install');

        // Pre Package Commands
        $this->runCommands('composer', 'pre-package');
        $this->runCommands('npm', 'pre-package');

        $this->info(PHP_EOL . "
         ___         _        _ _ _             ___         _
        |_ _|_ _  __| |_ __ _| | (_)_ _  __ _  | _ \__ _ __| |____ _ __ _ ___ ___
         | || ' \(_-<  _/ _` | | | | ' \/ _` | |  _/ _` / _| / / _` / _` / -_|_-<
        |___|_||_/__/\__\__,_|_|_|_|_||_\__, | |_| \__,_\__|_\_\__,_\__, \___/__/
                                        |___/                       |___/
        " . PHP_EOL . PHP_EOL);

        $this->installRepositories();

        $this->installPackages('composer');
        $this->installPackages('npm');

        // Post Package Commands
        $this->runCommands('composer', 'post-package');
        $this->runCommands('npm', 'post-package');

        return $this->info(PHP_EOL . "
         _  _                        ___         _ _             _
        | || |__ _ _ __ _ __ _  _   / __|___  __| (_)_ _  __ _  | |
        | __ / _` | '_ \ '_ \ || | | (__/ _ \/ _` | | ' \/ _` | |_|
        |_||_\__,_| .__/ .__/\_, |  \___\___/\__,_|_|_||_\__, | (_)
                  |_|  |_|   |__/                        |___/
        " . PHP_EOL . PHP_EOL);
    }

    /**
     * Install Laravel.
     *
     * @return void
     */
    public function installLaravel()
    {
        $this->task("Installing application in {$this->installLocation}", function () {
            $this->executeCommand('laravel new ' . $this->name . ' ' . $this->laravelOptions, true);
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
     * Install Repositories.
     *
     *
     * @return void
     */
    public function installRepositories()
    {
        if (count($this->packages['repositories']) > 0) {
            $this->info("Installing package repositories...");
            $this->packages['repositories']->each(function ($repo) {
                $name = $this->packages['composer']->firstWhere('key', $this->packages['repositories']->first()['key'])['name'];
                $this->task("Install {$name} Repository", function () use ($repo) {
                    $this->executeCommand('composer config repositories.' . $repo['name'] . ' ' . $repo['type'] . ' ' . $repo['url']);

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

                $commands = $this->config['commands'][$manager][$method];
                $compiledCommands = [];

                foreach ($commands as $command) {
                    $cmd = $command;
                    if (Str::contains($cmd, '$nameSnake')) {
                        $cmd = Str::replace('$nameSnake', Str::of($this->name)->replace('-', '_')->lower(), $cmd);
                    }
                    if (Str::contains($cmd, '$name')) {
                        $cmd = Str::replace('$name', $this->name, $cmd);
                    }

                    $compiledCommands[] = $cmd;
                }

                $this->executeCommands($compiledCommands, $location);

                return true;
            });
        }
    }
}
