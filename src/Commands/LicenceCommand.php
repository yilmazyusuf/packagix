<?php

namespace Packagix\Cmd;

use Packagix\Receiver;

class LicenceCommand extends Contract implements CommandInterface
{

    public $option = '';


    public function execute()
    {

    }

    public  function isOptionRequired()
    {
        return true;
    }



}