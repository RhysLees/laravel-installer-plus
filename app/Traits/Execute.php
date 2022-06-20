<?php

namespace App\Traits;

use RuntimeException;
use Symfony\Component\Process\Process;

trait Execute
{
    /**
     * Execute a console command.
     *
     * @param string $command
     * @param bool $location
     *
     * @return Process $process
     */
    public function executeCommand($command, $location = false)
    {
        $path = $this->applicationLocation;

        if ($location) {
            $path = $this->installLocation;
        }

        $command = 'cd ' . $path .  ' && ' . $command;

        $this->info('Executing: ' . $command);

        $process = Process::fromShellCommandline($command, null, $env = [], null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->error('Warning: ' . $e->getMessage());
            }
        }

        $process->run(function ($type, $line) {
            $this->info('    ' . $line);
        });

        return $process;
    }

    /**
     * Execute multiple commands
     *
     * @param array $commands
     * @param bool $location true=install-location | false=application-location
     *
     * @return Process $process
     */
    public function executeCommands($commands, $location = false)
    {
        $process = $this->executeCommand(implode(' && ', $commands), $location);

        return $process;
    }
}
