<?php

require_once "../vendor/autoload.php";

use Forecasta\Comment\Processor\CommentParser;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserFactory;
use Forecasta\Parser\Impl\JsonParser;
use Forecasta\Parser\Impl\TokenParser;
use Forecasta\Parser\ParserContext;
use Forecasta\Parser\HistoryEntry;


class TestClass_001 {

    private $compositeTarget = "";

    public function __construct()
    {
        $this->compositeTarget = <<<EOF
                {
                    "aaa" : 
                           {
                        "_bbb" : "c_12_cc",
                        "_ccc" : null,
                        "Empty" : "",
                        "Empty_" : '',
                        "number" : 1234,
                        "number_" : 101234,
                         "hhhhh" : {
                            "i_1-1" : "j",
                            "i_1-2" : true,
                            "i_1-3" : false,
                            "i_1-4" : TRUE,
                            "i_1-5" : FALSE,
                            "i_1-6" : "abc ABC  AbC 999,:';/+*==="
                         },
                         "fff" :
                            "gggg"
                    },
"ddd" 
                : 
                            "ee"    
                                    ,
                    "xyz" : [
                        "xxa", 
                        "xxb", 
                        [
                            "a", 
                            "b"
                        ], 
                        {
                            "c" : 
                                        "d"
                        },

                        {
                            "c" : [
                                "e", 
                                "f",
                                [111,222,333,444,5555,{"Key":"Value"}],
                                [111,222,333,444,5555,{"Key":"a b c A B C ''--++*///,,,,:::;;;"}]
                            ]
                        }
                    ]
                }
EOF;
    }

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
    function testParse() {
        $parser = new CommentParser();

        $comment = CommentParser::createContext($this, __FUNCTION__);

        $ctx = ParserContext::create($comment);

        $history = HistoryEntry::createEntry("sample", $ctx, $parser);

        $target = $parser->parse($ctx, 0, $history);

        //$parsed = $target->parsed();

        $walker = new \Forecasta\Parser\HistoryWalker();

        $history->walk($walker);

        //echo print_r($history);
    }

    function testParse02() {
        $ctx = ParserContext::create($this->compositeTarget);

        $parser = new JsonParser();

        $history = HistoryEntry::createEntry("JSON", $ctx, $parser);

        $result = $parser->parse($ctx, 0, $history);

        $walker = new \Forecasta\Parser\HistoryWalker();

        $history->walk($walker);
    }

    function testParse03() {
        $ctx = ParserContext::create("abcdefghijk");

        $parser1 = new TokenParser("abc");
        $parser2 = new TokenParser("def");
        $parser3 = new TokenParser("ghi");
        $parser1->setName("TokenTest");

        $parser = ParserFactory::Many()->add(ParserFactory::Seq()->add($parser1)->add($parser2)->add($parser3));

        $history = HistoryEntry::createEntry("Token", $ctx, $parser);

        $result = $parser->parse($ctx, 0, $history);

        $walker = new \Forecasta\Parser\HistoryWalker();

        $history->walk($walker);

        echo print_r($result, true);
    }
}


class CommentContext {
    private $contextName = "";

    public function startContext() {

    }
    public function endContext() {

    }
    public function isContext() {

    }
}
class CommentSubject {
    private $value = null;

    public function startSubject() {

    }
    public function endSubject() {

    }
    public function isSubject() {

    }
}


(new TestClass_001())->testParse03();
