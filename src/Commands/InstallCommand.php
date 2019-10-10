<?php

namespace Packagix\Cmd;

use Packagix\Composer;
use Packagix\Repository;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class InstallCommand extends Contract implements CommandInterface
{

    public $option = '';
    private $licence = null;

    public function execute()
    {

        $licence = $this->getLicence();
        $licenceKey = $licence->getOption();

        $package = $this->getOption();
        $packageJson = file_get_contents('https://packagix.com/package/?package=' . $package . '&licence=' . $licenceKey);

        if ($packageJson === false) {
            fwrite(STDERR, 'Unable to connect packagix' . PHP_EOL);
            die(5);
        }

        $packageInfo = json_decode($packageJson);
        if ($packageInfo->result === false) {
            fwrite(STDERR, $packageInfo->message . PHP_EOL);
            die(6);
        }

        $composerContent = Composer::getInstance();
        $repository = new Repository();
        $repository->setName($package)
            ->setVersion($packageInfo->version)
            ->setLicence($licenceKey)
            ->setType($packageInfo->type);

        $composerContent->addToRepositories($repository);
        $this->installPackage($repository);


    }

    public function isOptionRequired()
    {
        return true;
    }

    public function child(): string
    {
        return LicenceCommand::class;

    }

    public function setChild(LicenceCommand $licence) : void
    {
        $this->licence = $licence;
    }

    private function getLicence(): LicenceCommand
    {
        return $this->licence;
    }

    private function installPackage(Repository $repository){

        $process = new Process('composer install '. $repository->getName());
        $process->run();
        echo $process->getOutput();
    }


}
