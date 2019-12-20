<?php

namespace Forecasta\Comment\Processor;

use Forecasta\Parser as P;
use Forecasta\Parser\ParserContext as CTX;
use Forecasta\Parser\Impl as PImpl;
use Forecasta\Parser\ParserFactory;

class CommentParser
{
    public function parse($classInstance, $methodName)
    {
        $rc = new \ReflectionClass(get_class($classInstance));
        $m = $rc->getMethod($methodName);

        $comment = $m->getDocComment();
        $comment = $this->normalizeComment($comment);

        //サブジェクト := "@" + (文字列 | 数値 | アンダースコア) + (文字列 | 数値 | アンダースコア | ハイフン)*
        $subject = ParserFactory::Seq()
            ->add(ParserFactory::Token("@"))
            ->add(ParserFactory::Regex("/^[A-Za-z0-9_][A-Za-z0-9_\-]+/")->setName("Subject"));

        // プリミティブ := ダブルクォート + (ダブルクォート以外)+ + ダブルクォート
        $premitive = ParserFactory::Seq()
            ->add(ParserFactory::Token("\""))
            ->add(
                ParserFactory::Choice()
                    ->add(ParserFactory::Regex("/^[1-9]|([0-9][1-9]+)/"))// 数値
                    ->add(ParserFactory::Bool())// Bool値
                    ->add(ParserFactory::Regex("/^[^\"]+/"))// 文字列(ダブルクォート以外)
            )
            ->add(ParserFactory::Token("\""));

        $keyPairs = ParserFactory::Seq()
            ->add(
                ParserFactory::Many()
                    ->add(
                        ParserFactory::Seq()
                            ->add(ParserFactory::KeyPair())
                            ->add(ParserFactory::LbWs()->skip(true))
                            ->add(ParserFactory::Token(","))
                    )
            )
            ->add(ParserFactory::KeyPair());


        // 設定値 := プリミティブ | 設定値配列
        $confValue = ParserFactory::Choice()
            ->add($premitive)
            ->add(
                ParserFactory::Seq()
                    ->add(ParserFactory::Regex("/^\s*/")->skip(true))
                    ->add(ParserFactory::Token("("))
                    ->add(ParserFactory::LbWs()->skip(true))
                    ->add($keyPairs)
                    ->add(ParserFactory::LbWs()->skip(true))
                    ->add(ParserFactory::Token(")"))
            )->setName("ConfValue");

        // 設定エントリ
        $difinition = ParserFactory::Choice()

            ->add(
                ParserFactory::Seq()
                    ->add(ParserFactory::LbWs()->skip(true))
                    ->add($subject)
                    ->add(ParserFactory::LbWs()->skip(true))
                    ->add($confValue)
                    ->add(ParserFactory::LbWs()->skip(true))
            )

            ->add(
                ParserFactory::Seq()
                    ->add($subject)
                    ->add(ParserFactory::LbWs()->skip(true))
                    ->add(ParserFactory::Regex("/^[A-Za-z0-9_\-]+/"))// 文字列
                    ->add(ParserFactory::LbWs()->skip(true))
            )
            ->add(
                ParserFactory::Seq()
                ->add($subject)
                ->add(
                    ParserFactory::Option()
                        ->add(ParserFactory::Regex("/^\r|\n|\r\n/"))
                )

            )

            ->add(
                ParserFactory::Any()
                    ->add(ParserFactory::Regex("/^[^@].+/"))


            )

            ->setName("Definition");

        // 設定エントリ群
        $difinitions = ParserFactory::Any()
            ->add($difinition);

        $result = $difinitions->parse(CTX::create($comment));
        //$result2 = $difinitions->parse(CTX::create($comment));

        //echo print_r(join("\n", $difinitions), true). "\n";
        return $result;
    }

    /**
     * コメント先頭の不要な文字列を削除して、きれいなコメント文字列として返す。
     * @param $comment
     * @return string
     */
    public function normalizeComment($comment)
    {
        $ary = explode("\n", $comment);

        $newAry = array();
        foreach ($ary as $value) {
            $ptn01 = '/\s*?\\/\\*\\*/i';
            $rep01 = '';
            $tmp = preg_replace($ptn01, $rep01, $value);

            $ptn02 = '/\s*?\\*\\//i';
            $rep02 = '';
            $tmp = preg_replace($ptn02, $rep02, $tmp);

            $ptn03 = '/\s*?\\*\s+?(.+)/i';
            $rep03 = '${1}';
            $tmp = preg_replace($ptn03, $rep03, $tmp);

            $ptn04 = '/\s*?\\*\s*/i';
            $rep04 = '';
            $tmp = preg_replace($ptn04, $rep04, $tmp);


            if (empty($tmp)) {
                continue;
            }

            array_push($newAry, $tmp);
            //echo $value. "\n";
        }

        return join("\n", $newAry);
    }

    public function toArray($classInstance, $methodName)
    {
        $str = $this->parse($classInstance, $methodName);

        $parser = $this->getParser();

        return $str;
    }

    private function getParser()
    {
        $element = ParserFactory::Many();

        $choice = ParserFactory::Choice();

        $element->add($choice);


        $jyutugo = ParserFactory::Seq();

        {// Key Settings
            $startToken = ParserFactory::Token("@");
            $key = ParserFactory::Regex('/[A-Za-z][A-Za-z0-9]+/i');

            $jyutugo->add($startToken);
            $jyutugo->add($key);
        }

        $value = ParserFactory::Choice();
        $whiteSpace = ParserFactory::Regex('/\s+/');

        $valueSegment = ParserFactory::Seq();
        $valueSegment->add($jyutugo);
        {// Blaced Parameter

        }

        {// Not Quoted Parameter
            $v0 = ParserFactory::Regex('/[^"]+/');

            $value->add($v0);
        }

        {// Quoted Parameter
            $quoteLeft = ParserFactory::Char("\"");
            $quoteRight = ParserFactory::Char("\"");
            $v0 = ParserFactory::Regex('/[^"]+/');

            $quoted = ParserFactory::Seq();
            $quoted->add($quoteLeft);
            $quoted->add($v0);
            $quoted->add($quoteRight);

            $value->add($quoted);
        }


    }

    public function getTest()
    {
        // 全体
        $seq = ParserFactory::Seq();

        // Keyの部分(@xxxxxの部分)
        $jyutugo = ParserFactory::Seq();
        $startToken = ParserFactory::Token("@");
        $startToken->setName("Prefix");

        $key = ParserFactory::Regex('/[A-Za-z][A-Za-z0-9]+/i');
        $key->setName("Target");


        $jyutugo->add($startToken);
        $jyutugo->add($key);
        $seq->add($jyutugo);

        // value部分(クオートされた単一値、クオートされない単一値、カッコ付き値)
        $chr = ParserFactory::Choice();
        $seq->add($chr);

        // カッコ付き値
        // 複数行
        $chr = $this->getCompositeMultiLineParser($chr);

        // 単一行
        //$chr = $this->getCompositeSingleLineParser($chr);


        // シングルクオートでクオートされた単一値
        $chr = $this->getSingleQuotedParser($chr);

        // ダブルクオートでクオートされた単一値
        $chr = $this->getQuotedParser($chr);

        // クオートされない単一値
        $chr = $this->getNotQuotedParser($chr);

        return $seq;
    }

    private function getCompositeSingleLineParser($choice)
    {
        $choice0 = $choice;
        $seq = ParserFactory::Seq();

        $whiteSpace = ParserFactory::Option()->add(ParserFactory::Regex('/\s+/'));
        $camma = ParserFactory::Option()->add(
            ParserFactory::Seq()->add(
                ParserFactory::Regex(',')
            )->add(
                ParserFactory::Option()->add(
                    ParserFactory::Regex('/\s+/')
                )
            )
        );

        $quoteLeft = ParserFactory::Char("(");

        $keyValue = null;

        $seq->add(
            ParserFactory::Char("("))->add($whiteSpace)->add($keyValue)->add($whiteSpace)->add(ParserFactory::Char(")")
        );


        $quoteRight = ParserFactory::Char(")");


        $v0 = ParserFactory::Regex('/[^"]+/i');
        $v0->setName("Context");


        return $choice;
    }

    private function getCompositeMultiLineParser($choice)
    {

        return $choice;
    }

    private function getQuotedParser($choice)
    {

        $seq = ParserFactory::Seq();
        $seq->add(ParserFactory::Regex('/\s+/'));

        $quoteLeft = ParserFactory::Char("\"");
        $quoteRight = ParserFactory::Char("\"");
        $v0 = ParserFactory::Regex('/[^"]+/i');
        $v0->setName("Context");

        $quoted = ParserFactory::Seq();
        $quoted->add($quoteLeft);
        $quoted->add($v0);
        $quoted->add($quoteRight);

        $seq->add($quoted);

        $choice->add($seq);

        return $choice;
    }

    private function getSingleQuotedParser($choice)
    {

        $seq = ParserFactory::Seq();
        $seq->add(ParserFactory::Regex('/\s+/'));

        $quoteLeft = ParserFactory::Char("'");
        $quoteRight = ParserFactory::Char("'");
        $v0 = ParserFactory::Regex('/[^\']+/i');
        $v0->setName("Context");

        $quoted = ParserFactory::Seq();
        $quoted->add($quoteLeft);
        $quoted->add($v0);
        $quoted->add($quoteRight);

        $seq->add($quoted);

        $choice->add($seq);

        return $choice;
    }

    private function getNotQuotedParser($choice)
    {
        $seq = ParserFactory::Seq();
        $seq->add(ParserFactory::Regex('/\s+/'));
        $v0 = ParserFactory::Regex('/[^"]+/i');
        $v0->setName("Context");

        $seq->add($v0);

        $choice->add($seq);

        return $choice;
    }

    private static $NUMBER = null;

    public static function getNumberParser()
    {
        if (self::$NUMBER === null) {
            self::$NUMBER = ParserFactory::Regex("/^[0-9]+/");
        }
        return self::$NUMBER;
    }

    private static $EMPTY = null;

    public static function getEmptyParser()
    {
        if (self::$EMPTY === null) {
            self::$EMPTY = new PImpl\EmptyParser;
        }
        return self::$EMPTY;
    }

    private static $CAMMA = null;
    public static function getCammaParser()
    {

        if (self::$CAMMA === null) {
            self::$CAMMA = ParserFactory::Token(",");
        }
        return self::$CAMMA;
    }
}