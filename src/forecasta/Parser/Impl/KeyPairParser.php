<?php

/**
 * Created by PhpStorm.
 * User: nykos
 * Date: 2019/12/20
 * Time: 22:10
 */

namespace Forecasta\Parser\Impl;

//require_once "../../../../vendor/autoload.php";

use Forecasta\Common\Historical;
use Forecasta\Parser\HistoryEntry;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
//use Forecasta\Parser as P;
//use Forecasta\Parser\Impl\ParserTrait as PST;
use Forecasta\Parser\ParserFactory;
//use Forecasta\Parser\ParserContext as CTX;

/**
 * キーペア(Any => Any 形式)パーサです
 * @author nkoseki
 *
 */
class KeyPairParser implements Parser
{
    use ParserTrait;
    use Historical;

    private $parser = null;

    /**
     * パースメソッド
     * @param ParserContext $ctx
     * @return ParserContext コンテキスト
     */
    public function parse($context, $depth=0, HistoryEntry $currentEntry = null)
    {
        // 深度計算
        $depth = $depth + 1;

        // 履歴登録
        $context->setParser($this);
        $context->setName($this->getName());
        if($currentEntry == null) {
            $currentEntry = HistoryEntry::createEntry($this->getName(), $context->copy(), $this);
            $currentEntry->setDepth($depth);
        }

        $this->onTry($depth);

        // 履歴enter処理
        $currentEntry->enter($this, $context->copy());

        // 履歴エントリ作成
        $childHistory = HistoryEntry::createEntry($this->parser->getName(), $context->copy(), $this->parser);
        //$currentEntry->addEntry($childHistory);

        $ctx = $this->parser->parse($context, $depth);

        if($ctx->result()) {
            $this->onSuccess($ctx, $depth);

            // 履歴leave処理
            $currentEntry->leave($this, $ctx->copy(), true);

            $currentEntry->addEntry($childHistory);
        } else {
            $this->onError($ctx, $depth);

            // 履歴leave処理
            $currentEntry->leave($this, $ctx->copy(), false);
        }

        return $ctx;
    }

    public function isResolved()
    {
        return true;
    }

    public function __construct()
    {
        $parser = ParserFactory::Forward();

        // キー
        $confKey = ParserFactory::Seq()
            ->add(ParserFactory::LbWs()->skip(true))
            ->add(ParserFactory::Token("\""))
            ->add(ParserFactory::Regex("/^[^\"]+/"))
            ->add(ParserFactory::Token("\""))
            ->setName("ConfKey");

        // キーペア := キー "=>" 値
        $keyPair = ParserFactory::Seq()->setName("KeyPair")
            ->add($confKey)
            ->add(ParserFactory::LbWs()->skip(true))
            ->add(ParserFactory::Token("=>"))
            ->add(ParserFactory::LbWs()->skip(true))
            ->add($value = ParserFactory::Forward());

        // 値 := プリミティブ | 配列
        // プリミティブ
        $premitive = ParserFactory::Seq()->setName("Primitive")
            ->add(ParserFactory::LbWs()->skip(true))
            ->add(ParserFactory::Token("\""))
            ->add(
                ParserFactory::Choice()
                    ->add(ParserFactory::Regex("/^[1-9]|([0-9][1-9]+)/"))// 数値
                    ->add(ParserFactory::Bool())// Bool値
                    ->add(ParserFactory::Regex("/^[^\"]+/"))// 文字列(アンダースコア以外)
            )
            ->add(ParserFactory::Token("\""));

        // 配列 := (キーペア + ",")* + キーペア
        $array = ParserFactory::Seq()->setName("Array")
            ->add(ParserFactory::Token("("))
            ->add(ParserFactory::LbWs()->skip(true))
            ->add(
                ParserFactory::Many()
                    ->add(
                        ParserFactory::Seq()
                            ->add(ParserFactory::LbWs()->skip(true))
                            ->add($keyPair)
                            ->add(ParserFactory::LbWs()->skip(true))
                            ->add(ParserFactory::Token(","))
                            ->add(ParserFactory::LbWs()->skip(true))
                    )
            )
            ->add($keyPair)
            ->add(ParserFactory::LbWs()->skip(true))

            ->add(ParserFactory::Token(")"));

        $value->forward(
            ParserFactory::Choice()
                ->add($premitive)
                ->add($array)
        );

        $parser->forward($keyPair);

        $this->parser = $parser;
        $this->name = "KeyPair";

        //$this->parserHistoryEntry = new P\HistoryEntry;
    }

    public function __toString()
    {
        $searched = array();
        return $this->outputRecursive($searched);
    }

    public function outputRecursive($searched)
    {
        return $this->parser->outputRecursive($searched). "";
    }
}

/*
$p = new KeyPairParser();

$target = <<<EOF
"aaa"=>


                    (
    "b\bb" =>                  "cc'c'",
    "ddd"
     
     
     
     =>           
    
    
    "eee",
    "fff" => (
        "ggg"   => "hhh",
        "iii"   => "jjj",
        "kkk"   => (
            "lll" => "mmm",
            "nnn" => (
                "rrr" => "sss"
            )
        ),
        "ppp"   => "qqq"
    )
)
EOF;

$result = $p->parse(CTX::create($target));

echo print_r($result, true). "\n";

//echo print_r($p. '', true). "\n";
*/