<?php

namespace ForecastaTest\Comment\Processor;

use PHPUnit\Framework\TestCase;

use Forecasta\Parser as P;
use Forecasta\Parser\ParserContext as CTX;

use Forecasta\Parser\ParserFactory;
use Forecasta\Comment\Processor\CommentParser;
use Forecasta\Parser\Impl\JsonParser;
use Forecasta\Parser\Impl\FalseParser;

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
     * @bbb("aaaaa" => "bbbbb", "ccccc" => "dddddd")
     * @xyz(
     *     "aaa" => "bbb",
     *     "ccc" => (
     *          "ddd" => "eee",
     *          "fff" => "gggg",
     *          "hhh" => (
     *            "iii" => "jjj",
     *            "kkk" => "lll",
     *          )
     *      )
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
@bbb("aaaaa" => "bbbbb", "ccccc" => "dddddd")
@xyz(
    "aaa" => "bbb",
    "ccc" => (
         "ddd" => "eee",
         "fff" => "gggg",
         "hhh" => (
           "iii" => "jjj",
           "kkk" => "lll",
         )
     )
)
@return string
EOF;

        $parser = new CommentParser();
        $target = $parser->normalizeComment($comment);

        $targetCtx = CTX::create($target);
        $message = "Expected:\n{$test}\nActual\n{$target}\nEnd";

        $this->assertEquals($test, $target, $message);
        //$this->assertTrue(true, $message);
    }

    /**
     * @aaa "aaaaa"
     * @Test(
     *     "aaa"=>"bbb",
     *     "ccc"=>"ddd"
     * )
     * @bbb("aaaaa" => "bbbbb", "ccccc" => "dddddd")
     * @xyz(
     *     "aaa" => "bbb",
     *     "ccc" => (
     *          "ddd" => "eee",
     *          "fff" => "gggg",
     *          "hhh" => (
     *            "iii" => "jjj",
     *            "kkk" => "lll",
     *          )
     *      )
     * )
     * @return string
     */
    public function testParse() {
        $parser = new CommentParser();


        $target = $parser->parse($this, __FUNCTION__);




        $this->assertTrue(false, print_r($target, true));
    }
}