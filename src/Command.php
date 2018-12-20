<?php

namespace Packagix;
class Command
{
    //./vendor/bin/packagix init
    //composer packagix install=maatwebsite/excel licence=ADCF-X34JK-LMUU-ASDY9
    //@TODO composer packagix update
    protected $arguments = [
        'init',
        'install',
        'licence'
    ];

    protected $composerJson = null;
    protected $argOption = [];


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

            $argWithOptions = explode('=', $argv);
            $argName = trim($argWithOptions[0]);

            if (isset($argWithOptions[1])) {
                $argOption = trim($argWithOptions[1]);
                $this->argOption[$argName] = $argOption;
            }else{
                $this->argOption[$argName] = null;
            }



        }

        foreach ($this->argOption as $arg => $option){
            if ($this->checkArgs($arg) === true) {
                $this->handleArgument($arg);
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
            case 'install':

                if (!isset($this->argOption['install']) || $this->argOption['install'] == '') {
                    fwrite(STDERR, 'Packate not found' . PHP_EOL);
                    die(4);
                }

                if (!isset($this->argOption['licence']) || $this->argOption['licence'] == '') {
                    fwrite(STDERR, 'Licence Key Required' . PHP_EOL);
                    die(4);
                }

                $this->readComposerJson();
                $this->cmdInstall();

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
                'Unable to read composer.json' . PHP_EOL
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

        if (!isset($this->composerJson['scripts']['packagix'])) {
            $this->composerJson['scripts']['packagix'] = 'packagix';
            $this->rewriteComposerJson();
        }
    }

    protected function cmdInstall()
    {

        $package = $this->argOption['install'];
        $licence = $this->argOption['licence'];

        $packageJson = file_get_contents('http://trest.net/package/?package=' . $package.'&licence='.$licence);
        if($packageJson === false){
            fwrite(STDERR, 'Unable to connect packagix' . PHP_EOL);
            die(5);
        }

        $packageInfo = json_decode($packageJson);
        if($packageInfo->result === false){
            fwrite(STDERR, $packageInfo->message . PHP_EOL);
            die(6);
        }

        if (!isset($this->composerJson['repositories'])) {
            $this->composerJson['repositories'] = [];
        }

        $packageAlias = $package;
        foreach ($this->composerJson['repositories'] as $repoKey => $repo) {

            if ($repo['package']['name'] == $packageAlias) {
                unset($this->composerJson['repositories'][$repoKey]);
                $this->composerJson['repositories'] = array_values($this->composerJson['repositories']);
            }
        }

        $packageRepo = [
            'type' => 'package',
            'package' => [
                'type' => 'packagix',
                'name' => $packageAlias,
                'version' => $packageInfo->version,
                'dist' => [
                    'type' => 'zip',
                    'url' => 'http://trest.net/download?package='.$package.'&licence='.$licence,
                ],
            ]
        ];

        $this->composerJson['repositories'][] = $packageRepo;



        if(isset($this->composerJson['require'][$packageAlias])){
            unset($this->composerJson['require'][$packageAlias]);
        }
        $this->composerJson['require'][$packageAlias] = '*';

        $this->rewriteComposerJson();



        /*
        $tmpPath =  __DIR__ . '/../packages/';

        $source = fopen('http://trest.net/package/'.$package, 'r');
        $target = $tmpPath.$package.'.zip';

        file_put_contents($target, $source);




        $zip = new ZipArchive;
        $zip->open($target);
        $zip->extractTo($tmpPath);
        */
    }

    protected function rewriteComposerJson()
    {

        $jsonify = json_encode($this->composerJson, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $update = file_put_contents(COMPOSER_PATH, $jsonify);
    }

}