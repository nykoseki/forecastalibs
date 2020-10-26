<?php

namespace Forecasta\Common;

use Forecasta\Parser\HistoryEntry;

/**
 * 履歴管理機能を構成するためのトレイトです
 * Trait Historical
 * @package Forecasta\Common
 */
trait Historical {
    private $history = null;

    public function setHistory(HistoryEntry $history) {
        $this->history = $history;
    }

    public function getHistory() {
        return $this->history;
    }
}