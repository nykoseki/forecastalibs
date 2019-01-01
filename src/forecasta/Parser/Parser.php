<?php

namespace Forecasta\Parser;

use Forecasta\Parser\ParserContext as CTX;

/**
 * パーサコンビネータインタフェースです
 * @author nkoseki
 *
 */
interface Parser extends RecursiveOut
{

    /**
     * パースメソッド
     * @param ParserContext $ctx
     * @return ParserContext コンテキスト
     */
    public function parse($context);

    /**
     * 引数に指定された文字列をパースし，結果を返します
     * @param string $target
     * @return ParserContext コンテキスト
     */
    public function invoke($target);


    public function setName($name);

    public function getName();

    public function setDescription($description);

    public function getDescription();

    public function onSuccess(CTX $context);

    public function onError(CTX $context);

    public function onTry();

    public function isResolved();
}

