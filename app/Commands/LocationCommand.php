<?php

namespace App\Commands;

use App\Helpers\Files;
use App\Traits\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class LocationCommand extends Command
{

    public $installLocation;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'location';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Check where your application would be installed.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! Storage::disk('config')->exists('config.json')) {
            $this->error('Run `laravel-installer-plus install` first!');
            exit;
        } else {
            $config = Storage::disk('config')->get('config.json');
            $this->config = json_decode($config, true);

            // Set Locations
            $this->installLocation = $this->config['install-location'] ?  config('filesystems.disks.local.root') . $this->config['install-location'] : getcwd();

            $this->info('Your application would be installed in: ' . $this->installLocation);
        }
    }
}
