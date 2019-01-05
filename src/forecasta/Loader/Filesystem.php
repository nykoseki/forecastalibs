<?php

namespace Forecasta\Loader;

class Filesystem
{
    private $pathArray = array();

    public function __construct()
    {
        array_push($this->pathArray, getcwd());
    }

    public function _clear()
    {
        $this->pathArray = array();
        array_push($this->pathArray, getcwd());
        return $this;
    }

    public function _prev()
    {
        array_push($this->pathArray, "..");

        return $this;
    }

    public function _back() {
        array_pop($this->pathArray);

        return $this;
    }

    public function _file(string $fileName) {
        array_push($this->pathArray, $fileName);

        return $this;
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        array_push($this->pathArray, $name);
        return $this;
    }

    public function __get($name)
    {
        array_push($this->pathArray, $name);
        return $this;
    }

    public function exists()
    {
        $path = join("/", $this->pathArray);

        echo $path. PHP_EOL;
        return file_exists($path);
    }
}