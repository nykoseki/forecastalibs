<?php

namespace Forecasta\Comment\Processor;

use Forecasta\Parser as P;
use Forecasta\Parser\ParserContext as CTX;
use Forecasta\Parser\Impl as PImpl;
use Forecasta\Parser\ParserFactory;

class CommentParser
{
    public function parse($classInstance, $methodName) {
        $rc = new \ReflectionClass(get_class($classInstance));
        $m = $rc->getMethod($methodName);

        $comment = $m->getDocComment();

        $ary = explode("\n", $comment);

        $newAry = array();
        foreach($ary as $value) {
            $ptn01 = '/\s*?\\/\\*\\*/i';
            $rep01 = '';
            $tmp = preg_replace($ptn01, $rep01, $value);

            $ptn02 = '/\s*?\\*\\//i';
            $rep02 = '';
            $tmp = preg_replace($ptn02, $rep02, $tmp);

            $ptn03 = '/\s*?\\*\s+?(.+)/i';
            $rep03 = '${1}';
            $tmp = preg_replace($ptn03, $rep03, $tmp);

            if(empty($tmp)) {
                continue;
            }

            array_push($newAry, $tmp);
            //echo $value. "\n";
        }

        return join("\n", $newAry);
        //echo print_r(join("\n", $newAry), true). "\n";
    }

    public function toArray($classInstance, $methodName) {
        $str = $this->parse($classInstance, $methodName);

        $parser = $this->getParser();

        return $str;
    }

    private function getParser() {
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

    public function getTest() {
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

    private function getCompositeSingleLineParser($choice) {
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

    private function getCompositeMultiLineParser($choice) {

        return $choice;
    }

    private function getQuotedParser($choice) {

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

    private function getSingleQuotedParser($choice) {

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

    private function getNotQuotedParser($choice) {
        $seq = ParserFactory::Seq();
        $seq->add(ParserFactory::Regex('/\s+/'));
        $v0 = ParserFactory::Regex('/[^"]+/i');
        $v0->setName("Context");

        $seq->add($v0);

        $choice->add($seq);

        return $choice;
    }
}