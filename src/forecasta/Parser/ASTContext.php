<?php

namespace Forecasta\Parser;

class ASTContext
{
    // 解決済みパーサリスト
    private $realParsers = array();

    // 未解決パーサリスト
    private $unResolves = array();

    // 遅延評価パーサリスト
    private $lazys = array();

    private function __construct()
    {

    }

    public function newInstance()
    {
        return new self();
    }

    public function resolved()
    {
        return $this->realParsers;
    }

    public function unResolved()
    {
        return $this->unResolves;
    }

    public function lazy()
    {
        return $this->lazys;
    }
}

