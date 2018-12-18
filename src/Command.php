<?php

namespace Packagix;
class Command
{
    //./vendor/bin/packagix init
    protected $arguments = [
        'init'
    ];

    protected $composerJson = null;


    public static function main()
    {

        $command = new static;

        return $command->run();
    }

    protected function run()
    {
        return $this->getArguments();
    }

    protected function checkArgs(string $arg): bool
    {
        return in_array($arg, $this->arguments);
    }

    protected function getArguments()
    {

        foreach ($_SERVER['argv'] as $argv) {
            if ($this->checkArgs($argv) === true) {
                $this->handleArgument($argv);
            }
        }
    }

    protected function handleArgument(string $argv)
    {

        switch ($argv) {
            case 'init':
                $this->readComposerJson();
                $this->cmdInit();
                break;
        }
    }

    protected function readComposerJson()
    {

        if (!is_null($this->composerJson)) {
            return $this->composerJson;
        }

        $composerContent = file_get_contents(COMPOSER_PATH);

        if ($composerContent === false || is_null($composerContent)) {
            fwrite(
                STDERR,
                'unable to read composer.json' . PHP_EOL
            );
            die(1);
        }

        $this->composerJson = json_decode($composerContent, true);
        return $composerContent;

    }

    protected function cmdInit()
    {
        if (!isset($this->composerJson['scripts'])) {
            $this->composerJson['scripts'] = [];
        }

        if(!isset($this->composerJson['scripts']['packagix'])){
            $this->composerJson['scripts']['packagix'] = 'packagix';
            $this->rewriteComposerJson();
        }
    }

    protected function rewriteComposerJson(){

        $jsonify = json_encode($this->composerJson,JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
        $update = file_put_contents(COMPOSER_PATH,$jsonify);
    }

}