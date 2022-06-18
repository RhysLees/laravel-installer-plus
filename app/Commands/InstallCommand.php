<?php

namespace App\Commands;

use App\Traits\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class InstallCommand extends Command
{
    use Configuration;

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
    protected $description = 'Install Laravel Installer Plus';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->getConfiguration();
        $this->checkConfiguration();

        return $this->info('Laravel Installer Plus is installed.');
    }
}
