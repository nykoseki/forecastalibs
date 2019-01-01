<?php

namespace Forecasta\Parser\Impl\Util;

trait Cacheable {

    private $cache;

    public function getCache() {
        return $this->cache;
    }

    public function setCache($cache) {
        $this->cache = $cache;
    }
}