<?php

namespace Forecasta\Common;

/**
 * 親子関係機能を提供するトレイトです
 * Trait Composite
 * @package Forecasta\Common
 */
trait Composite
{
    /**
     * 親オブジェクトです
     * @var null
     */
    private $parent = null;

    /**
     * 子オブジェクトです
     * @var array
     */
    private $children = array();

    /**
     * 親オブジェクトを取得します
     * @return null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * 親オブジェクトを設定します
     * @param $context
     * @return null
     */
    public function setParent($context)
    {
        return $this->parent;
    }

    /**
     * 子オブジェクトを追加します
     * @param $context
     */
    public function add($context)
    {
        array_push($this->children, $context);
    }

    /**
     * 子オブジェクト一覧を取得します
     * @return mixed
     */
    public function children()
    {
        return $this->children;
    }


}