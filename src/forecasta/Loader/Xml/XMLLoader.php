<?php

namespace Forecasta\Loader\Xml;

use Forecasta\Parser as P;
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

    /**
     *
     *
     * @param string $xmlPath
     * @throws \Forecasta\Loader\Xml\XMLLoaderException
     */
    public function init(string $xmlPath)
    {
        if(!file_exists($xmlPath)) {
            throw new XMLLoaderException("XMLのロードに失敗しました(Path: <". $xmlPath . ">)", 100);
        }
    }

    /**
     * XMLから動的にパーサコンビネータを作成する。
     *
     * @return P\Parser
     */
    public function create() {

    }
}