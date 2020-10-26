<?php

namespace ForecastaTest\Comment\Processor;

use PHPUnit\Framework\TestCase;

//use Forecasta\Parser as P;
use Forecasta\Parser\ParserContext;

//use Forecasta\Parser\ParserFactory;
use Forecasta\Comment\Processor\CommentParser;
//use Forecasta\Parser\Impl\JsonParser;
//use Forecasta\Parser\Impl\FalseParser;

class CommentParserTestCase extends TestCase
{

    /**
     * コメントアノテーションパーサによるコメントアノテーションパースが成功するかテストする
     */
    public function testNormalizeComment()
    {
        /*
         * パース対象文字列
         */
        $comment = <<<EOF
    /**
     * @aaa "aaaaa"
     * @Test(
     *     "aaa"=>"bbb",
     *     "ccc"=>"ddd"
     * )
     * @bbb("aaaaa" => "bbbbb", "ccccc" => "ddddddd")
     * @xyz(
     *     "aaa" => "bbb",
     *     "ccc" => (
     *          "ddd" => "eee",
     *          "fff" => "gggg",
     *          "hhh" => (
     *            "iii" => "jjj",
     *            "kkk" => "lll",
     *          )
     *      ),
     *      "value" => 123, "aaa" => 12000, [123, 456, 7890, ("a" => "b")]
     * )
     * @return string
     */
EOF;
        $test = <<<EOF
@aaa "aaaaa"
@Test(
    "aaa"=>"bbb",
    "ccc"=>"ddd"
)
@bbb("aaaaa" => "bbbbb", "ccccc" => "ddddddd")
@xyz(
    "aaa" => "bbb",
    "ccc" => (
         "ddd" => "eee",
         "fff" => "gggg",
         "hhh" => (
           "iii" => "jjj",
           "kkk" => "lll",
         )
     ),
     "value" => 123, "aaa" => 12000, [123, 456, 7890, ("a" => "b")]
)
@return string
EOF;

        //$parser = new CommentParser();
        $target = CommentParser::normalizeComment($comment);

        $targetCtx = ParserContext::create($target);
        $message = "Expected:\n{$test}\nActual\n{$target}\nEnd";

        $this->assertEquals($test, $target, $message);
        //$this->assertTrue(true, $message);
    }

    /**
     * @bbb(
     *     "aaaaa" => "bbbbb",
     *     "ccccc" => "dddddd"
     * )
     * @xyz(
     *     "aaa" => "bbb",
     *     "ccc" => (
     *          "ddd" => "eee",
     *          "fff" => "gggg",
     *          "hhh" => (
     *            "iii" => "jjj",
     *            "kkk" => "lll"
     *          )
     *      )
     * )
     * @return string
     * @throws
     */



    /**
     *
     * ドキュメントコメントパースのテスト
     * 標準ドキュメントコメントに加え、設定エントリをパースできるかをテストする。
     *
     * @TestTest "testCaseABC"
     * @bbb(
     *     "aaaaa" => "bbbbdfdfdfdb",
     *     "ccccc" =>"dddddd"
     * )
     * @xy-z   (
     *     "aaa" => "bbb",
     *     "ccc" => (
     *          "ddd" => "eee",
     *          "fff" => "gggg",
     *          "hhh" => (
     *            "iii" => "jjj",
     *            "kkk" => "lll"
     *          )
     *      )
     * )
     *
     * @return void
     *
     * @throws
     *
     * @throws \Exception
     *
     * あああ
     * えええ
     * おおお
     *
     * @subject "abvc"
     *
     */
    public function testParse() {
        /*
        $rc = new \ReflectionClass(get_class($this));
        $m = $rc->getMethod(__FUNCTION__);

        $comment = $m->getDocComment();
        */

        $parser = new CommentParser();
        //$comment = CommentParser::normalizeComment($comment);
        $comment = CommentParser::createContext($this, __FUNCTION__);

        $ctx = ParserContext::create($comment);

        //$target = $parser->parse($this, __FUNCTION__);
        $target = $parser->parse($ctx);

        $parsed = $target->parsed();

        $parsed = print_r($parsed, true);

        //$this->assertEquals("", $parsed, "-----------------------------------\n". $parsed. "\n-----------------------------------");

        //$this->assertTrue(!$target->isFinish(), $target. '', $comment);
        $this->assertTrue($target->isFinished(), print_r($parsed, true));
        //$this->assertTrue(false, $target. '');
        //$this->assertTrue(false, print_r($target, true));
    }
}