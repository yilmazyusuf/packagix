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
        $composerContent->addPackagixToScripts('packagix','packagix');
    }

    public  function isOptionRequired()
    {
        return false;
    }







}