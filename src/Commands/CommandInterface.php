<?php

namespace Packagix\Cmd;


interface CommandInterface
{
    public function execute();

    public function isOptionRequired();

    public function setOption(string $option);

    public function getOption() :string ;

}