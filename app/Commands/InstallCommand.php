<?php

namespace App\Commands;

use App\Traits\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class InstallCommand extends Command
{
    use Configuration;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'install {--force : Overwrite existing files}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install Laravel Installer Plus';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('force')) {
            $this->warn('Replacing existing config file. (A backup will be created)');
            if ($this->confirm('Do you wish to continue?')) {
                Storage::disk('config')->move('config.json', 'config.json.bak');
            }
        }

        $this->getConfiguration();
        $this->checkConfiguration();

        return $this->info('Laravel Installer Plus is installed.');
    }
}
