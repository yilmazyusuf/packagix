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



    public function readContent()
    {
        if (is_null($this->content)) {
            $getContent = file_get_contents($this->path);
            if($getContent === false){
                fwrite(STDERR,'Unable to read composer.json' . PHP_EOL);
                die(1);
            }
            $this->content = $getContent;
        }

        return json_decode($this->content,true);
    }


    public function rewriteComposerJson($content)
    {

        $jsonify = json_encode($content, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $update = file_put_contents($this->path, $jsonify);
    }


}