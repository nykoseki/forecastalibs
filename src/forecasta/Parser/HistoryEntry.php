<?php

namespace Forecasta\Parser;

/**
 * パーサを用いた解析ヒストリです
 * Class History
 * @package Forecasta\Parser
 */
class HistoryEntry
{
    /**
     * 子エントリ
     * @var
     */
    private $child = array();

    /**
     * 親エントリ
     * @var
     */
    private $parent;

    /**
     * このエントリが所属するパーサ
     * @var null
     */
    private $parser = null;

    /**
     * このエントリでパースされたコンテキスト
     * @var null
     */
    private $context = null;

    public function isRoot()
    {
        return ($this->parent === null);
    }

    public function getContext()
    {
        return $this->context;
    }

    protected function setContext(P\ParserContext $context)
    {
        $this->context = $context;
    }

    public function enter(Parser $parser)
    {
        // parser#onTryコール時にコールされる
    }

    public function getCurrentParser()
    {
        return $this->currentParser;
    }

    public function leave()
    {
        // parser#onSuccess, parser#onErrorコール時にコールされる
    }
}