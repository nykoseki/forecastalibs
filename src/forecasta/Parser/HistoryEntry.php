<?php

namespace Forecasta\Parser;

use Forecasta\Common\Named;

/**
 * パーサを用いた解析ヒストリです
 * Class History
 * @package Forecasta\Parser
 */
class HistoryEntry implements \ArrayAccess, \IteratorAggregate, \Countable
{
    use Named;

    // == ArrayAccess ==================================================================================================
    public function offsetGet($index) {
        return isset($this->child[$index]) ? $this->child[$index] : null;
    }
    public function offsetExists($index) {
        return isset($this->child[$index]);
    }
    public function offsetSet($index, $value) {
        if(is_null($index)) {
            $this->child[] = value;
        } else {
            $this->child[$index] = $value;
        }
    }
    public function offsetUnset($index) {
        unset($this->child[$index]);
    }
    // == IteratorAggregate ============================================================================================
    public function getIterator() {
        return new \ArrayIterator($this->child);
    }
    // =================================================================================================================

    // == Countable ====================================================================================================
    public function count() {
        return count($this->child);
    }
    // =================================================================================================================

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
     * パーサーのタイプです
     * @var string
     */
    private $parserType = "";

    /**
     * このエントリに付加するためのメモです
     * @var null
     */
    private $memo = null;

    /**
     * このエントリでパースされたコンテキスト
     * @var null
     */
    private $context = null;

    /**
     * パースの結果です
     * @var bool
     */
    private $isSuccess = false;

    /**
     * パースの状態です()
     * @var string
     */
    private $parseState = "";

    /**
     * パースされた文字列です
     * @var string
     */
    private $parseResult = "";

    /**
     * この履歴エントリのルートエントリからの距離をあらわします
     * @var int
     */
    private $depth = 0;

    /**
     * 解析された文字列の文字列長です
     * @var int
     */
    private $len = 0;

    /**
     * 解析対象文字列全体における現在の解析終端位置です
     * @var int
     */
    private $position = 0;

    /**
     * コンテキストとパーサを指定して新たな履歴エントリを作成します
     * @param ParserContext $context
     * @param Parser $parser
     * @return HistoryEntry
     */
    public static function createEntry($entryName, ParserContext $context, Parser $parser) {
        $newEntry = new HistoryEntry();
        $newEntry->setContext($context);
        $newEntry->setParser($parser);
        $newEntry->setName($entryName);

        return $newEntry;
    }

    /**
     * この履歴エントリがルートエントリの場合にtrueを返します。
     * @return bool
     */
    public function isRoot()
    {
        return ($this->parent === null);
    }

    /**
     * 引数に指定した履歴エントリを親エントリとして設定します
     * @param HistoryEntry $entry
     */
    public function setParentEntry(HistoryEntry $entry) {
        $this->parent = $entry;
    }

    /**
     * この履歴エントリに子エントリを追加します
     * @param HistoryEntry $entry
     */
    public function addEntry(HistoryEntry $entry) {
        array_push($this->child, $entry);
        $entry->setParentEntry($this);
        $entry->setDepth($this->getDepth() + 1);
    }

    /**
     * この履歴エントリに指定されているコンテキストオブジェクトを返します
     * @return null
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * この履歴エントリにコンテキストオブジェクトを設定します
     * @param ParserContext $context
     */
    protected function setContext(ParserContext $context)
    {
        $this->context = $context;
    }

    /**
     * parser#enterコール時にコールされます
     * @param Parser $parser
     */
    public function enter(Parser $parser, ParserContext $context)
    {
        //$this->parser = $parser;
    }

    /**
     * この履歴エントリにパーサーを設定します
     * @param Parser $parser
     */
    public function setParser(Parser $parser) {
        $this->parser = $parser;
    }

    /**
     * この履歴エントリに紐づくパーサーを返します
     * @return mixed
     */
    public function getCurrentParser()
    {
        return $this->currentParser;
    }

    /**
     * parser#onSuccess, parser#onErrorコール時にコールされます
     * @param Parser $parser
     * @param boolean $resultFlg success:true / error:false
     */
    public function leave(Parser $parser, ParserContext $context, bool $resultFlg)
    {
        // parser#onSuccess, parser#onErrorコール時にコールされる
        $this->name = $parser->getName();
        $this->isSuccess = $resultFlg;
        $this->parser = $parser;
        $this->context = $context;
        $this->parseResult = $context->parsed();
        $this->parserType = get_class($parser);
        $this->position = $context->current();

        //echo $context->parsed()

        if(is_string($context->parsed())) {
            $this->len = mb_strlen($context->parsed());
        }
    }

    public function length() {
        return $this->len;
    }

    public function position() {
        return $this->position;
    }

    public function parsed() {
        return $this->parseResult;
    }

    public function isSuccess() {
        return $this->isSuccess;
    }

    public function getMemo() {
        return $this->memo;
    }
    public function setMemo($memo) {
        $this->memo = $memo;
    }

    public function getDepth() {
        return $this->depth;
    }
    public function setDepth($depth) {
        $this->depth = $depth;
    }

    public function walk(HistoryWalkerBase $walker, $depth = 0) {
        if($walker != null) {
            $walker->walk($this, $depth);
        }
    }

    public function childCount() {
        return count($this->child);
    }

    public function children() {
        return $this->child;
    }

    public function hasMoreChildren() {
        return count($this->child) > 0;
    }

    public function isSkip() {
        if($this->parser != null) {
            return $this->parser->isSkip();
        } else {
            return false;
        }
    }

    public function getParserType() {
        return $this->parserType;
    }
}