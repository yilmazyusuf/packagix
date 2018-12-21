<?php

namespace Packagix\Cmd;

use Packagix\Composer;
use Packagix\Receiver;

class InitCommand extends Contract implements CommandInterface
{

    public $option = '';


    public function execute()
    {
        $composerContent = Composer::getInstance();
        $content = $composerContent->readContent();

        if (!isset($content['scripts'])) {
            $content['scripts'] = [];
        }

        if (!isset($content['scripts']['packagix'])) {
            $content['scripts']['packagix'] = 'packagix';
            $composerContent->rewriteComposerJson($content);
        }

    }

    public  function isOptionRequired()
    {
        return false;
    }







}