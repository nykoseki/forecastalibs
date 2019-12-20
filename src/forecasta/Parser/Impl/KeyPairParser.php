<?php

/**
 * Created by PhpStorm.
 * User: nykos
 * Date: 2019/12/20
 * Time: 22:10
 */

namespace Forecasta\Parser\Impl;

//require_once "../../../../vendor/autoload.php";

use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;
use Forecasta\Parser\ParserFactory;
use Forecasta\Parser\ParserContext as CTX;

/**
 * キーペア(Any => Any 形式)パーサです
 * @author nkoseki
 *
 */
class KeyPairParser implements P\Parser
{
    use PST;

    private $parser = null;

    /**
     * パースメソッド
     * @param ParserContext $ctx
     * @return ParserContext コンテキスト
     */
    public function parse($context, $depth=0)
    {

        return $this->parser->parse($context, $depth);
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
        $keyPair = ParserFactory::Seq()
            ->add($confKey)
            ->add(ParserFactory::LbWs()->skip(true))
            ->add(ParserFactory::Token("=>"))
            ->add(ParserFactory::LbWs()->skip(true))
            ->add($value = ParserFactory::Forward());

        // 値 := プリミティブ | 配列
        // プリミティブ
        $premitive = ParserFactory::Seq()
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
        $array = ParserFactory::Seq()
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

        $this->parserHistoryEntry = new P\HistoryEntry;
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