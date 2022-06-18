<?php

namespace App\Traits;

trait Execute
{
    /**
     * Execute a console command.
     *
     * @param string $command
     * @param bool $location
     *
     * @return mixed
     */
    public function executeCommand($command, $location = false)
    {
        $path = $this->applicationLocation;

        if ($location) {
            $path = $this->config['install-location'];
        }

        exec('cd ' . $path .  ' && ' . $command, $output, $result);
        return $result;
    }

    /**
     * Execute multiple commands
     *
     * @param array $commands
     * @param bool $location true=install-location | false=application-location
     *
     * @return void
     */
    public function executeCommands($commands, $location = false)
    {
        $result = $this->executeCommand(implode(' && ', $commands), $location);
    }
}
