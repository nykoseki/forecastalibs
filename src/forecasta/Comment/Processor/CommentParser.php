<?php

namespace Forecasta\Comment\Processor;

use Forecasta\Common\Historical;
use Forecasta\Parser\Impl\ParserTrait;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
use Forecasta\Parser\Impl\EmptyParser;
use Forecasta\Parser\ParserFactory;
use Forecasta\Parser\HistoryEntry;

class CommentParser implements Parser
{
    use ParserTrait;
    use Historical;

    private $classInstance;
    private $methodName;

    private $parser = null;

    public function parse($context, $depth=0, HistoryEntry $currentEntry = null)
    {
        /*
        $rc = new \ReflectionClass(get_class($classInstance));
        $m = $rc->getMethod($methodName);

        $comment = $m->getDocComment();
        $comment = self::normalizeComment($comment);
        */
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
        $childHistory = HistoryEntry::createEntry($this->getName(), $context->copy(), $this);
        $currentEntry->addEntry($childHistory);

        $result = $this->parser->parse($context, $depth, $childHistory);

        if($result->result()) {
            $this->onSuccess($result, $depth);

            // 履歴leave処理
            $currentEntry->enter($this, $result->copy(), true);
        } else {
            $this->onError($result, $depth);

            // 履歴leave処理
            $currentEntry->enter($this, $result->copy(), false);
        }

        return $result;
    }

    public function __construct()
    {
        //サブジェクト := "@" + (文字列 | 数値 | アンダースコア) + (文字列 | 数値 | アンダースコア | ハイフン)*
        $subject = ParserFactory::Seq()
            ->add(ParserFactory::Token("@")->setName("SubjectToken"))
            ->add(ParserFactory::Regex("/^[A-Za-z0-9_][A-Za-z0-9_\-]+/")->setName("Subject"));

        // プリミティブ := ダブルクォート + (ダブルクォート以外)+ + ダブルクォート
        $primitive = ParserFactory::Seq()
            ->add(ParserFactory::Token("\"")->setName("TokenLeft"))
            ->add(
                ParserFactory::Choice()
                    ->add(ParserFactory::Regex("/^[1-9]|([0-9][1-9]+)/")->setName("Number"))// 数値
                    ->add(ParserFactory::Bool()->setName("Bool"))// Bool値
                    ->add(ParserFactory::Regex("/^[^\"]+/")->setName("Character"))// 文字列(ダブルクォート以外)
            )
            ->add(ParserFactory::Token("\"")->setName("TokenRight"))
            ->setName("Primitive");

        $keyPairs = ParserFactory::Seq()
            ->add(
                ParserFactory::Many()
                    ->add(
                        ParserFactory::Seq()
                            ->add(ParserFactory::KeyPair()->setName("KeyPair"))
                            ->add(ParserFactory::LbWs()->skip(true))
                            ->add(ParserFactory::Token(",")->setName("Comma"))
                    )
            )
            ->add(ParserFactory::KeyPair()->setName("KeyPair"))
            ->setName("KeyPairs");


        // 設定値 := プリミティブ | 設定値配列
        $confValue = ParserFactory::Choice()
            ->add($primitive)
            ->add(
                ParserFactory::Seq()
                    ->add(ParserFactory::Regex("/^\s*/")->skip(true))
                    ->add(ParserFactory::Token("(")->setName("Bra"))
                    ->add(ParserFactory::LbWs()->skip(true))
                    ->add($keyPairs)
                    ->add(ParserFactory::LbWs()->skip(true))
                    ->add(ParserFactory::Token(")")->setName("Ket"))
            )->setName("ConfValue");

        // 設定エントリ
        $definition = ParserFactory::Choice()

            ->add(
                ParserFactory::Seq()
                    ->add(ParserFactory::LbWs()->skip(true))
                    ->add($subject)
                    ->add(ParserFactory::LbWs()->skip(true))
                    ->add($confValue)
                    ->add(ParserFactory::LbWs()->skip(true))
            )->setName("Definition")

            ->add(
                ParserFactory::Seq()
                    ->add($subject)
                    ->add(ParserFactory::LbWs()->skip(true))
                    ->add(ParserFactory::Regex("/^[A-Za-z0-9_\-]+/")->setName("Character"))// 文字列
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
        $definitions = ParserFactory::Any()
            ->add($definition)->setName("Definitions");

        $this->parser = $definitions;
    }

    public static function createContext($classInstance, $methodName) : ParserContext {
        $rc = new \ReflectionClass(get_class($classInstance));
        $m = $rc->getMethod($methodName);

        $comment = $m->getDocComment();
        $comment = self::normalizeComment($comment);

        return ParserContext::create($comment);
    }

    /**
     * コメント先頭の不要な文字列を削除して、きれいなコメント文字列として返す。
     * @param $comment
     * @return string
     */
    public static function normalizeComment($comment)
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

        $output = join("\n", $newAry);
        $output = trim($output);

        return $output;
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
        $chr = $this->getCompositeSingleLineParser($chr);


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
            self::$EMPTY = new EmptyParser;
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

    public function isResolved()
    {
        return true;
    }

    public function outputRecursive($searched)
    {
        $className = get_class($this);
        applLog2("outputRecursive", $searched);
        $searched[] = $this->name;

        $className = str_replace("\\", "/", $className);

        $name = $this->name;
        $param = $this->str;
        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":\"$param\"}";

        return $message;
    }
}