<?php

namespace Packagix;


use Packagix\Cmd\CommandInterface;

class Invoker
{
    private $command;

    public function setCommand(CommandInterface $cmd)
    {
        $this->command = $cmd;
    }

    public function run()
    {
        $this->command->execute();
    }
}