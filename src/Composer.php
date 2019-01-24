<?php

namespace Packagix;


final class Composer
{

    private $path;
    private $content;


    /**
     * Composer constructor.
     * @param $path
     */
    private function __construct()
    {
        $this->path = COMPOSER_PATH;
    }

    public static function getInstance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new Composer();
        }
        return $inst;
    }


    private function readContent()
    {
        if (is_null($this->content)) {
            $getContent = file_get_contents($this->path);
            if ($getContent === false) {
                fwrite(STDERR, 'Unable to read composer.json' . PHP_EOL);
                die(1);
            }
            $this->content = $getContent;
        }

        return json_decode($this->content, true);
    }


    private function rewriteComposerJson($content)
    {

        $jsonify = json_encode($content, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $update = file_put_contents($this->path, $jsonify);

        return $update;
    }

    public function addToScripts($path, $command)
    {
        $content = $this->readContent();

        if (!isset($content['scripts'])) {
            $content['scripts'] = [];
        }

        if (!isset($content['scripts'][$path])) {
            $content['scripts'][$path] = $command;
            $this->rewriteComposerJson($content);
        }
    }

    public function addToRequire($content,$package, $version)
    {


        if (isset($content['require'][$package])) {
            unset($content['require'][$package]);
        }
        $content['require'][$package] = $version;

        return $content;
    }



    public function addToRepositories(Repository $repository)
    {
        $content = $this->readContent();

        if (!isset($content['repositories'])) {
            $content['repositories'] = [];
        }


        foreach ($content['repositories'] as $repoKey => $repo) {

            if ($repo['package']['name'] == $repository->getName()) {
                unset($content['repositories'][$repoKey]);
                $content['repositories'] = array_values($content['repositories']);
            }
        }


        $packageRepo = [
            'type' => $repository->getType(),
            'package' => [
                'type' => 'packagix',
                'name' => $repository->getName(),
                'version' => $repository->getVersion(),
                'dist' => [
                    'type' => 'zip',
                    'url' => 'https://packagix.com/download?package=' . $repository->getName() . '&licence=' . $repository->getLicence(),
                ],
            ]
        ];

        $content['repositories'][] = $packageRepo;
        $content = $this->addToRequire($content,$repository->getName(),'*');
        $this->rewriteComposerJson($content);

    }


}
