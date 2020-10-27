<?php

require_once "../vendor/autoload.php";

use Forecasta\Comment\Processor\CommentParser;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserFactory;
use Forecasta\Parser\Impl\JsonParser;
use Forecasta\Parser\Impl\TokenParser;
use Forecasta\Parser\ParserContext;
use Forecasta\Parser\HistoryEntry;


class TestClass_001
{

    public $compositeTarget0 = "";
    public $compositeTarget = "";
    public $compositeTarget2 = [];

    public $compositeTarget3 = "";
    public $compositeTarget4 = "";


    public function __construct()
    {
        $this->compositeTarget0 = <<<EOF
{"aaa":{"_bbb":"c_12_cc","_ccc":null,"Empty":"","Empty_":"","number":1234,"number_":101234,"hhhhh":{"i_1-1":"j","i_1-2":true,"i_1-3":false,"i_1-4":true,"i_1-5":false,"i_1-6":"abc ABC  AbC 999,:';\/+*==="},"fff":"gggg"},"ddd":"ee","xyz":["xxa","xxb",["a","b"],{"c":"d"},{"c":["e","f",[111,222,333,444,5555,{"Key":"Value"}],[111,222,333,444,5555,{"Key":"a b c A B C ''--++*\/\/\/,,,,:::;;;","a":{"aaaaaaaaaaaaaaaaaaaaaaa":"bbbbbbbbbbb"}},561612684841645]]}]}
EOF;

        $this->compositeTarget2 = [
            "aaa" => [
                "_bbb" => "c_12_cc",
                "_ccc" => null,
                "Empty" => "",
                "Empty_" => '',
                "number" => 1234,
                "number_" => 101234,
                "hhhhh" => [
                    "i_1-1" => "j",
                    "i_1-2" => true,
                    "i_1-3" => false,
                    "i_1-4" => TRUE,
                    "i_1-5" => FALSE,
                    "i_1-6" => "abc ABC  AbC 999,:';/+*==="
                ],
                "fff" => "gggg"],

                "ddd" => "ee",
                "xyz" => [
                    "xxa",
                    "xxb",
                    [
                        "a",
                        "b"
                    ],
                    [
                        "c" => "d"
                    ],

                    [
                        "c" => [
                            "e",
                            "f",
                            [
                                111,
                                222,
                                333,
                                444,
                                5555,
                                ["Key" => "Value"]
                            ],

                                111,
                                222,
                                333,
                                444,
                                5555,

                                ["Key" => "a b c A B C ''--++*///,,,,:::;;;"],

                                "a" =>
                                    [

                                        "aaaaaaaaaaaaaaaaaaaaaaa" => "bbbbbbbbbbb"
                                    ]
                                ,

                                561612684841645



                        ]
                    ]
                ]

        ];
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
                             "fff" :"gggg"
                           },
                "ddd"   : 
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
                                [111,222,333,444,5555,{"Key":"a b c A B C ''--++*///,,,,:::;;;","a":{
                                                    
                                                        "aaaaaaaaaaaaaaaaaaaaaaa"    :     "bbbbbbbbbbb"
                                                            }
                                    },
                                    
                                    
                                    
                                    
                                    561612684841645
                                    
                                    
                                    ]
                            ]
                        }
                    ]
                }
EOF;

        $this->compositeTarget3 = <<<EOF
        {"objectId":"b","ObjectParam":[1,2,
        
        {"val1":4564,"val2":616,"val3":5165, "remarks":[1,2]},
                {"testtest":123456789},
        3], 
        
        "aaa":                                                             
                                                                                     {
               "key"                 
                           :     
        {"a":"b","c":"d","e":"f","g":{"aa":123},"i":"j"}                 
                                }
        }
EOF;

        $this->compositeTarget4 = <<<EOF
(
    "a" => "b",
    "xxx" => ["a", "b", "c", ("aaa" => 123, "_" 
    
    
    =>
    
    
    
    "処理開始"), "JSONパーサを修正(AltJSON対応)・・・Valueに日本語を"],
    "key" => "仕訳明細トラン同期処理。"
)
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
    function testParse()
    {
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

    function testParse02()
    {
        //$target = $this->compositeTarget;
        //$target = $this->compositeTarget3;
        $target = $this->compositeTarget4;
        //$target = $this->compositeTarget2;
        //$target = json_encode($target);

        $ctx = ParserContext::create($target);

        //$parser = new JsonParser();
        $parser = ParserFactory::JSON("(", ")", "=>");

        $history = HistoryEntry::createEntry("JSON", $ctx, $parser);

        $result = $parser->parse($ctx, 0, $history);

        $walker = new \Forecasta\Parser\HistoryWalker();

        //$history->walk($walker);

        $filter = function ($value) {
            if (empty($value) || $value === null || empty(preg_replace("/\s+/", "", $value)) || $value == "<LbWs>") {
                return false;
            } else {
                return true;
            }
        };
        $flat_arr = array();

        $filterFunc = Y(function ($callback) {
            return function ($arg) use (&$callback) {
                switch ($arg) {
                    case is_array($arg) :

                        return array_filter($arg, function ($item) use (&$callback) {

                            switch ($item) {
                                case is_numeric($item) :
                                    return $item;
                                case is_string($item) :
                                    return $item != "<LbWs>";
                                case is_array($item) :
                                    return $item;

                                    return $callback($item);

                                default:
                                    return true;
                            }


                            return true;
                        });

                        return true;

                    //return $callback($arg);
                    case is_string($arg) :
                        if ($arg === "<LbWs>") {
                            return false;
                        } else {
                            return $arg;
                        }
                    case is_numeric($arg) :
                        return $arg;
                    default:
                        return $arg;
                }
            };
        });

        /*
        $parsed = array_filter($result->parsed(), function($item){
            switch($item) {
                case is_array($item) :return true;
                case is_string($item) :
                    if($item === "<LbWs>") {
                        return false;
                    } else {
                        return true;
                    }
                case is_numeric($item) :return true;
                default: return true;
            }

            //echo print_r($parsed);
        });
        */
        //$parsed = $filterFunc($result->parsed());

        $parsed = \Forecasta\Common\ArrayUtil::flatten($result->parsed());

        $parsed = implode($parsed);

        //$parsed = json_decode($parsed);

        $parsed = str_replace("<Empty>", "\"\"", $parsed);

        //$parsed = str_replace("<Empty>", "\"\"", $parsed);
        //$parsed = str_replace("<Empty>", "\"\"", $parsed);
        $parsed = str_replace("/", "\\/", $parsed);

        //$parsed = str_replace("\"", "\\\"", $parsed);
        //$parsed = json_decode($parsed);
        //$parsed = str_replace($parsed, "\\\"", "\"");

        //$val = \Forecasta\Common\ArrayUtil::compact($result->parsed());
        //echo "======================================================\n";
        //print_r($val);

        return $result->parsed();
        //echo print_r($result->parsed(), true) . "\n";
        //print_r(json_encode($this->compositeTarget2));
    }

    function testParse03()
    {
        $ctx = ParserContext::create("abcdefghiabcdefghi");

        $parser1 = (new TokenParser("abc"))->setName("Token01");
        $parser2 = (new TokenParser("def"))->setName("Token02");
        $parser3 = (new TokenParser("ghi"))->setName("Token03");
        //$parser1->setName("TokenTest");

        $parser = ParserFactory::Many()->add(ParserFactory::Seq()->add($parser1)->add($parser2)->add($parser3)->setName("Container"));

        $history = HistoryEntry::createEntry("Token", $ctx, $parser);

        $result = $parser->parse($ctx, 0, $history);

        $walker = new \Forecasta\Parser\HistoryWalker();

        $history->walk($walker);

        //echo print_r($result, true);
    }

    public function testParse04()
    {
        $forward = ParserFactory::Forward()->setName("Container");

        $lBlace = ParserFactory::Token("[")->setName("Left");

        // Number


        $rBlace = ParserFactory::Token("]")->setName("Right");
    }
}

class CommentContext
{
    private $contextName = "";

    public function startContext()
    {

    }

    public function endContext()
    {

    }

    public function isContext()
    {

    }
}

class CommentSubject
{
    private $value = null;

    public function startSubject()
    {

    }

    public function endSubject()
    {

    }

    public function isSubject()
    {

    }
}


$result = (new TestClass_001())->testParse02();

$f = Y(function($callback){
    return function($item) use(&$callback){
        if(is_array($item)) {
            $intermediate = array();

            $count = count($item);

            //$tmp = $item;
            $skipFlg = false;
            if($item[$count - 1] == ",") {
                $skipFlg = true;
                $count = $count - 1;
            }


            for($i = 0; $i < $count; $i++) {
                $tmp = $item[$i];

                $tmp = $callback($tmp);

                if($tmp != null) {
                    array_push($intermediate, $tmp);
                }
            }

            return $intermediate;

            //return  $intermediate;

        } else {
            if($item != ",") {
                return $item;
            } else {
                return null;
            }

        }
    };
});

$val = $result;
//$val = $f($result);

$val = \Forecasta\Common\ArrayUtil::reduction($val);
//$val = $f($val);
//$val = \Forecasta\Common\ArrayUtil::reduction($val);
print_r($val);
//return -1;

//echo "======================================================\n";
//print_r($val);

echo "======================================================\n";
$val = \Forecasta\Common\ArrayUtil::flatten($val);
$val = implode("", $val);
$val = str_replace("<Empty>", "\"\"", $val);
$val = str_replace("/", "\\/", $val);
echo($val). "\n";
echo "======================================================\n";
echo print_r(json_decode($val, true), true). "\n";
echo (json_decode($val))->key. "\n";
echo "↑↑↑\n";
echo "======================================================\n";
echo json_encode((new TestClass_001())->compositeTarget2);
exit();

$val = [[[
            [["abc"]],
            [
                "cc",
                [[[1],["dafdsfafasdf"]]],[],
                [[[[1234654]]]]
            ],[]
        ]]];
//$val = ["abc"];

$yF = Y(function($callback){
    return function($item) use(&$callback){
        if(is_array($item)) {
            if(count($item) > 0) {
                if(count($item) == 1) {
                    //echo "eeee\n";
                    return $callback($item[0]);
                } else {
                    $intermediate = [];

                    // 再帰
                    foreach($item as $key => $value) {

                        $result = null;
                        if(is_array($value)) {
                            if(count($value) == 0) {
                                //echo "dddd\n";
                            } else {
                                if(count($value) == 1) {
                                    //echo "cccc\n";
                                    $result = $callback($value[0]);
                                } else {
                                    //echo "bbbb\n";
                                    $result = $callback($value);
                                }
                            }
                        } else {
                             echo "aaaa\n";
                            $result = $value;
                        }

                        if($result != null) {
                            array_push($intermediate, $result);
                            //$intermediate[] = $result;
                        }
                    }

                    return $intermediate;
                }
            } else {
                // サイズ0
                return null;
            }
        } else {
            return $item;
        }
    };
});

$func = function($item){
    if(is_array($item)) {
        if(count($item) > 0) {
            if(count($item) == 1) {
                return $item[0];
            } else {
                // 再帰

            }
        } else {
            // サイズ0
        }
    } else {
        return $item;
    }
};


echo "======================================================\n";
print_r($val);

//$val = $yF($val);
//echo "======================================================\n";
//print_r($val);


$val = [["abc"],[[[[["abc"]],[[[[[[[[[[156464646165]]]]]]]]]],]]],[[[[new stdClass],[[[]]]]]]];
//$val = [new stdClass,[]];

$val = \Forecasta\Common\ArrayUtil::compact($val);
echo "======================================================\n";
print_r($val);
