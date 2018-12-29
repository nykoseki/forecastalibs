<?php

namespace Forecasta\Parser;

/**
 * パーサの入力と出力を司るコンテキストクラスです
 * @author nkoseki
 *
 */
class ParserContext
{

    private $target = "";
    private $currentPosition = 0;
    private $parsed = null;
    private $ctx = null;

    public function __construct($target, $position, $parsed, $ctx)
    {
        $this->target = $target;
        $this->currentPosition = $position;
        $this->parsed = $parsed;
        $this->ctx = $ctx;
    }

    public static function create($target)
    {
        return new ParserContext($target, 0, null, false);
    }

    public function target()
    {
        return $this->target;
    }

    public function current()
    {
        return $this->currentPosition;
    }

    public function parsed()
    {
        return $this->parsed;
    }

    public function result()
    {
        return $this->ctx;
    }

    public function currentTarget()
    {
        $target0 = $this->target();
        $position = $this->current();
        $len = mb_strlen($target0);

        $tmpTarget = mb_substr($target0, $position, $len - $position);
        return $tmpTarget;
    }

    public function isFinished()
    {
        return (($this->ctx) && ($this->currentPosition == mb_strlen($this->target)));
    }

    public function __toString()
    {

        $parsed0 = $this->parsed;

// 		if(is_array($parsed0)) {
// 			if(count($parsed0) == 0) {
// 				$parsed0 = '[Null]';
// 			} else {
// 				$parsed0 = array_map(function($item){
// 					return '"'. $item. '"';
// 				}, $parsed0);
// 				$parsed0 = implode(', ', $parsed0);
// 				$parsed0 = '['. $parsed0. ']';
// 			}
// 		} else if(is_null($parsed0)) {
// 			$parsed0 = '[Null]';
// 		}

// 		$Y = function($F) use(&$Y){
// 			return $F(
// 					function() use (&$F, &$Y){
// 						return call_user_func_array($Y($F), func_get_args());
// 					}
// 			);
// 		};

        // Y-Combinatorで再帰処理を行う
        // 配列->[...]
        // 文字列->"..."
        $parsed0 = Y(function ($callback) {
            return function ($x) use (&$callback) {
                if (is_array($x)) {
                    if (count($x) == 0) {
                        return '"<Null>"';
                    } else {
                        $parsed0 = array_map(function ($item) use (&$callback) {
                            if (is_array($item)) {
                                return $callback($item);
                            } else {
                                return '"' . $item . '"';
                            }

                        }, $x);

                        $parsed0 = implode(', ', $parsed0);
                        $parsed0 = '[' . $parsed0 . ']';
                        return $parsed0;
                    }
                } else if (is_null($x)) {
                    return '"<Null>"';
                } else {
                    return '"' . $x . '"';
                }
            };
        })->__invoke($parsed0);

        $res = ($this->ctx) ? '"OK"' : '"NG"';

        $finish = (mb_strlen($this->target) == $this->currentPosition) ? '"OK"' : '"NG"';

        return "{\"Target\":\"{$this->target}\", \"Current\":{$this->currentPosition}, \"Result\":{$res}, \"Finish\":{$finish}, \"Parsed\":{$parsed0}}";
    }
}
