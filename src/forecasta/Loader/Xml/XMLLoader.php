<?php

namespace Forecasta\Loader\Xml;

use Forecasta\Loader\Xml\XMLLoaderException;

class XMLLoader
{
    /**
     * コンストラクタ
     * XMLLoader constructor.
     */
    public function __construct()
    {
    }

    public function init(string $xmlPath)
    {
        if(!file_exists($xmlPath)) {
            throw new XMLLoaderException("XMLのロードに失敗しました(Path: <". $xmlPath . ">)", 100);
        }
    }
}