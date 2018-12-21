<?php

namespace Packagix\Cmd;
use Packagix\Composer;

class InstallCommand extends Contract implements CommandInterface
{

    public $option = '';
    private $licence = null;


    public function execute()
    {

        $licence = $this->getLicence();
        $licenceKey = $licence->getOption();

        $package = $this->getOption();
        $packageJson = file_get_contents('http://trest.net/package/?package=' . $package . '&licence=' . $licenceKey);

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
        $content = $composerContent->readContent();

        if (!isset($content['repositories'])) {
            $content['repositories'] = [];
        }

        foreach ($content['repositories'] as $repoKey => $repo) {

            if ($repo['package']['name'] == $package) {
                unset($content['repositories'][$repoKey]);
                $content['repositories'] = array_values($content['repositories']);
            }
        }


        $packageRepo = [
            'type' => 'package',
            'package' => [
                'type' => 'packagix',
                'name' => $package,
                'version' => $packageInfo->version,
                'dist' => [
                    'type' => 'zip',
                    'url' => 'http://trest.net/download?package=' . $package . '&licence=' . $licenceKey,
                ],
            ]
        ];

        $content['repositories'][] = $packageRepo;



        if (isset($content['require'][$package])) {
            unset($content['require'][$package]);
        }
        $content['require'][$package] = '*';

        $composerContent->rewriteComposerJson($content);



    }

    public function isOptionRequired()
    {
        return true;
    }

    public function child()
    {
        return LicenceCommand::class;

    }

    public function setChild(LicenceCommand $licence)
    {
        $this->licence = $licence;
    }

    private function getLicence(): LicenceCommand
    {
        return $this->licence;
    }


}