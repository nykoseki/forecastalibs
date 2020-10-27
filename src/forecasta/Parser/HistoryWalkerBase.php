<?php
/**
 * Created by PhpStorm.
 * User: nykos
 * Date: 2020/10/26
 * Time: 9:40
 */

namespace Forecasta\Parser;

interface HistoryWalkerBase {
    public function walk(HistoryEntry $entry, $depth=0);
}