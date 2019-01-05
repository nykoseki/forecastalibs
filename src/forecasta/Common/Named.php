<?php

namespace Forecasta\Common;

/**
 * 名前付け機能を提供します
 * Trait Named
 * @package Forecasta\Common
 */
trait Named
{
    /**
     * 名前です
     * @var string
     */
    private $name = "";

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }


}