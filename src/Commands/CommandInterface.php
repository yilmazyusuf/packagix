<?php

namespace Packagix\Cmd;


interface CommandInterface
{
    public function execute();
    public function isOptionRequired();
}