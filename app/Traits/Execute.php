<?php

namespace App\Traits;

trait Execute
{
    /**
     * Execute a console command.
     *
     * @param string $command
     * @return mixed
     */
    public function executeCommand($command)
    {
        exec('cd ' . $this->applicationLocation .  ' && ' . $command, $output, $result);
        return $result;
    }

    /**
     * Execute multiple commands
     *
     * @param array $commands
     * @return void
     */
    public function executeCommands($commands)
    {
        $result = $this->executeCommand(implode(' && ', $commands));
    }
}
