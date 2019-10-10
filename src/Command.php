<?php

namespace Packagix;

use Packagix\Cmd\CommandInterface;
use Packagix\Cmd\Init;
use Packagix\Cmd\InitCommand;
use Packagix\Cmd\InstallCommand;
use Packagix\Cmd\LicenceCommand;

class Command
{


    protected $receiverCommands = [];

    protected static function commands()
    {
        return [
            'init' => InitCommand::class,
            'install' => InstallCommand::class,
            'licence' => LicenceCommand::class,
        ];
    }




    protected function getCommand(string $commandClassPath): CommandInterface
    {
        return new $commandClassPath;
    }


    public static function main()
    {

        $command = new static;
        $commandsList = $command::commands();
        $command->prepare();
        $command->handle();

    }




    protected function prepare()
    {
        $commands = self::commands();

        foreach ($_SERVER['argv'] as $argv) {

            $argWithOptions = explode('=', $argv);
            $argName = trim($argWithOptions[0]);
            $argOption = isset($argWithOptions[1]) ? trim($argWithOptions[1]) : null;

            if (isset($commands[$argName])) {

                $command = $commands[$argName];
                $commandInstance = $this->getCommand($command);

                if ($commandInstance->isOptionRequired() === true && is_null($argOption)) {
                    fwrite(STDERR, 'Option Required for '.$argName . PHP_EOL);
                    die(1);
                }

                if(!is_null($argOption)){
                    $commandInstance->setOption($argOption);
                }

                $this->attachReceiverCommand($argName,$commandInstance);
            }
        }

    }

    protected function handle(){

        $commands = $this->getReceiverCommands();


        $commandsReversed = array_flip(self::commands());

        foreach ($commands as $command => $commandInstance){
            if(method_exists($commandInstance,'child')){

                $child = $commandInstance->child();
                $commandKey = $commandsReversed[$child];

                if(!isset($commands[$commandKey])){
                    fwrite(STDERR, 'Argument required '.$commandKey .' for '.$command . PHP_EOL);
                    die(1);
                }
                $commandInstance->setChild($commands[$commandKey]);

            }

            $invoker = new Invoker();
            $invoker->setCommand($commandInstance);
            $invoker->run();


        }


    }



    /**
     * @return array
     */
    public function getReceiverCommands(): array
    {
        return $this->receiverCommands;
    }

    /**
     * @param array $receiverCommands
     */
    public function attachReceiverCommand(string $argName,CommandInterface $receiverCommand): Command
    {
        $this->receiverCommands[$argName] = $receiverCommand;
        return $this;
    }



}