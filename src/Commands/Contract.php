<?php

namespace Packagix\Cmd;


abstract class Contract
{

    public $option = '';
    private $composer = null;
    /**
     * @return string
     */
    public function getOption(): string
    {
        return $this->option;
    }

    /**
     * @param string $option
     */
    public function setOption(string $option)
    {
        $this->option = $option;
        return $this;
    }

}