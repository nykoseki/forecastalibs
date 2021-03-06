<?php

namespace Forecasta\Parser;

use Forecasta\Parser\Impl\CharParser;
use Forecasta\Parser\Impl\ChoiceParser;
use Forecasta\Parser\Impl\FalseParser;
use Forecasta\Parser\Impl\ForwardParser;
use Forecasta\Parser\Impl\ManyParser;
use Forecasta\Parser\Impl\AnyParser;
use Forecasta\Parser\Impl\OptionParser;
use Forecasta\Parser\Impl\RegexParser;
use Forecasta\Parser\Impl\SequenceParser;
use Forecasta\Parser\Impl\TokenParser;
use Forecasta\Parser\Impl\LbWsParser;
use Forecasta\Parser\Impl\TrueParser;
use Forecasta\Parser\Impl\EmptyParser;
use Forecasta\Parser\Impl\BoolParser;
use Forecasta\Parser\Impl\KeyPairParser;
use Forecasta\Parser\Impl\NumberParser;
use Forecasta\Parser\Impl\QuoteParser;
use Forecasta\Parser\Impl\JointParser;
use Forecasta\Parser\Impl\CommaParser;
use Forecasta\Parser\Impl\LeftBraceParser;
use Forecasta\Parser\Impl\RightBraceParser;
use Forecasta\Parser\Impl\JsonParser;
use Forecasta\Parser\Impl\ObjectLeftParser;
use Forecasta\Parser\Impl\ObjectRightParser;


/**
 * 各種パーサを生成・組み立てするためのユーティリティクラスです
 * @author nkoseki
 *
 */
class ParserFactory
{
    /**
     *
     * @var unknown
     */
    private static $hasChildrenParser = null;

    /**
     *
     * @var unknown
     */
    private static $referenceSearcher = null;

    /**
     * CharParserを生成します
     * @param string $char
     * @return Parser
     */
    public static function Char($char)
    {
        return new CharParser($char);
    }

    /**
     * ChoiceParserを生成します
     * @return Parser
     */
    public static function Choice()
    {
        return new ChoiceParser();
    }

    /**
     * FalseParserを生成します
     * @return Parser
     */
    public static function F()
    {
        return new FalseParser();
    }

    /**
     * ForwardParserを生成します
     * @return Parser
     */
    public static function Forward()
    {
        return new ForwardParser();
    }

    /**
     * ManyParserを生成します
     * @return Parser
     */
    public static function Many(/*Psr $parser*/)
    {
        return (new ManyParser);
    }

    /**
     * AnyParserを生成します
     * @return Any
     */
    public static function Any(/*Psr $parser*/)
    {
        return (new AnyParser);
    }

    /**
     * OptionParserを生成します
     * @param Parser $parser
     * @return Parser
     */
    public static function Option(/*Psr $parser*/)
    {
        return (new OptionParser);
    }

    /**
     * RegexParserを生成します
     * @param string $regex
     * @return Parser
     */
    public static function Regex($regex)
    {
        return new RegexParser($regex);
    }

    /**
     * SequenceParserを生成します
     * @return Parser
     */
    public static function Seq()
    {
        return new SequenceParser();
    }

    /**
     * TokenParserを生成します
     * @param string $token
     * @return Parser
     */
    public static function Token($token)
    {
        return new TokenParser($token);
    }

    /**
     * TrueParserを生成します
     * @return Parser
     */
    public static function T()
    {
        return new TrueParser();
    }

    /**
     * LbWsParserを生成します
     * @return Parser
     */
    public static function LbWs()
    {
        return new LbWsParser;
    }

    /**
     * TrueParserを生成します
     * @return Parser
     */
    public static function True()
    {
        return new TrueParser;
    }

    /**
     * FalseParserを生成します
     * @return Parser
     */
    public static function False()
    {
        return new FalseParser;
    }

    /**
     * EmptyParserを生成します
     * @return Parser
     */
    public static function Empty()
    {
        return new EmptyParser;
    }

    /**
     * BoolParserを生成します
     * @return Parser
     */
    public static function Bool()
    {
        return new BoolParser;
    }

    /**
     * KeyPairParserを生成します
     * @return Parser
     */
    public static function KeyPair()
    {
        return new KeyPairParser;
    }

    /**
     * NumberParserを生成します
     * @return Parser
     */
    public static function Number()
    {
        return new NumberParser;
    }

    /**
     * QuoteParserを生成します
     * @return Parser
     */
    public static function Quote()
    {
        return new QuoteParser;
    }

    /**
     * JointParserを生成します
     * @param string $joint 結合子
     * @return JointParser
     */
    public static function Joint($joint = ":")
    {
        return new JointParser($joint);
    }

    /**
     * CommaParserを生成します
     * @return Parser
     */
    public static function Comma()
    {
        return new CommaParser;
    }

    /**
     * LeftBraceParserを生成します
     * @return Parser
     */
    public static function Lbr()
    {
        return new LeftBraceParser();
    }

    /**
     * RightBraceParserを生成します
     * @return Parser
     */
    public static function Rbr()
    {
        return new RightBraceParser();
    }

    /**
     * ObjectLeftParserを生成します
     * @param string $objectLeft
     * @return ObjectLeftParser
     */
    public static function ObjLeft($objectLeft = "{")
    {
        return new ObjectLeftParser($objectLeft);
    }

    /**
     * ObjectRightParserを生成します
     * @param string $objectRight
     * @return ObjectRightParser
     */
    public static function ObjRight($objectRight = "}")
    {
        return new ObjectRightParser($objectRight);
    }

    /**
     * JSONParserを生成します
     * @param string $objLeftChar 左括弧(デフォルト"{")
     * @param string $objRightChar 右括弧(デフォルト"}")
     * @param string $joint オブジェクト結合子(デフォルト":")
     *
     * @return JsonParser
     */
    public static function JSON($objLeftChar = "{", $objRightChar = "}", $joint = ":")
    {
        return new JsonParser($objLeftChar, $objRightChar, $joint);
    }

    /**
     * 引数に指定されたパーサタイプと生成パラメータを用いて，パーサを生成します
     * @param string $type パーサタイプ
     * @param mixed $param 生成パラメータ
     *
     * @return Parser
     */
    public static function CreateFrom($type, $param)
    {
        if ($type === 'char') {
            return self::Char($param);
        } else if ($type === 'choice') {
            return self::Choice($param);
        } else if ($type === 'false') {
            return self::F($param);
        } else if ($type === 'forward') {
            return self::Forward();
        } else if ($type === 'many') {
            return self::Many();
        } else if ($type === 'option') {
            return self::Option();
        } else if ($type === 'regex') {
            return self::Regex($param);
        } else if ($type === 'sequence') {
            return self::Seq();
        } else if ($type === 'token') {
            return self::Token($param);
        } else if ($type === 'true') {
            return self::T();
        } else if ($type === 'any') {
            return self::Any();
        } else if ($type === 'str_true') {
            return self::True();
        } else if ($type === 'str_false') {
            return self::False();
        } else if ($type === 'empty') {
            return self::Empty();
        } else if ($type === 'bool') {
            return self::Bool();
        } else if ($type === 'keyPair') {
            return self::KeyPair();
        } else if ($type === 'number') {
            return self::Number();
        } else if ($type === 'quote') {
            return self::Quote();
        } else if ($type === 'joint') {
            return self::Joint();
        } else if ($type === 'comma') {
            return self::Joint();
        } else if ($type === 'lbr') {
            return self::Lbr();
        } else if ($type === 'rbr') {
            return self::Rbr();
        } else if ($type === 'objLeft') {
            return self::ObjLeft();
        } else if ($type === 'objRight') {
            return self::ObjRight();
        }
    }

    /**
     * 引数に指定されたパーサタイプが，子パーサを要求するかテストします
     * @param string $type パーサタイプ
     * @return true:子パーサを要求する/false:子パーサを要求しない
     */
    private static function hasChildren($type)
    {
        if (self::$hasChildrenParser == null) {
            self::$hasChildrenParser = self::Choice()->setName("HasChildren")
                ->add(self::Token('choice')->setName("isChoice"))
                ->add(self::Token('forward')->setName("isForward"))
                ->add(self::Token('many')->setName("isMany"))
                ->add(self::Token('any')->setName("isAny"))
                ->add(self::Token('option')->setName("isOption"))
                ->add(self::Token('sequence')->setName("isSequence"));
        }

        return self::$hasChildrenParser->invoke($type)->isFinished();
    }

    /**
     * 引数に指定したリファレンス参照文字列に紐付く抽象構文部分木を，ASTから検索して返します
     * 部分木が見つからない場合はnullを返します．
     * @param string $referenceName リファレンス参照文字列
     * @param AST $definition 抽象構文木
     * @return 抽象構文部分木
     */
    private static function searchReferenceByAST($referenceName, $definition)
    {
        if (self::$referenceSearcher == null) {
            // ASTからリファレンス検索するクロージャ
            self::$referenceSearcher = Y(function ($callback) {
                return function ($name, $definition) use (&$callback) {
                    //applLog2("reference-search", "start search at [$name]");
                    if (is_array($definition)) {
                        foreach ($definition as $child) {
                            if (is_object($child)) {
                                if (property_exists($child, "name")) {
                                    $aName = $child->name;
                                    if ($name === $aName) {
                                        return $child;
                                    } else {
                                        if (property_exists($child, "child") && is_array($child->child) && count($child->child) > 0) {
                                            foreach ($child->child as $child0) {
                                                $result = $callback($name, $child0);
                                                if (is_null($result)) {
                                                    continue;
                                                } else {
                                                    return $result;
                                                }
                                            }
                                        } else {
                                            continue;
                                        }
                                    }
                                }
                            }
                        }

                        return null;
                    } else if (is_object($definition)) {
                        if (property_exists($definition, "name")) {
                            $aName = $definition->name;
                            if ($name === $aName) {
                                //applLog2("search-success:object", $name);
                                return $definition;
                            } else {
                                if (property_exists($definition, "child") && is_array($definition->child) && count($definition->child) > 0) {
                                    foreach ($definition->child as $child0) {
                                        $result = $callback($name, $child0);
                                        if (is_null($result)) {
                                            continue;
                                        } else {
                                            return $result;
                                        }
                                    }
                                }
                                //applLog2("reference-search:single[$name != $aName]", "unmatch");
                                return null;
                            }
                        } else {
                            applLog2("reference-search:single[$name]", "name property not exists");
                            return null;
                        }
                    }
                };
            });
        }

        $result = self::$referenceSearcher->__invoke($referenceName, $definition);

        return $result;
    }

    /**
     * 引数に指定されたASTがForward型か検査します
     * @param AST-Branch $ast 抽象構文木(枝)
     */
    private static function isAstForward($ast, $strictry = FALSE)
    {
        if (is_array($ast)) {
            return false;
        } else {
            $isForward = property_exists($ast, "type") && ($ast->type === 'forward');

            if ($strictry) {
                $isForward = $isForward && property_exists($ast, "parser") && ($ast->perser instanceof Forward);
            }

            return $isForward;
        }
    }

    /**
     * 引数に指定されたASTがリファレンスを持つか検査します
     * @param AST-Branch $ast 抽象構文木(枝)
     */
    private static function hasAstReference($ast)
    {
        if (is_array($ast)) {
            return false;
        } else {
            return property_exists($ast, "ref") && !empty($ast->ref);
        }
    }

    /**
     * 引数に指定されたASTがtypeプロパティを持つか検査します
     * @param AST-Branch $ast 抽象構文木(枝)
     */
    private static function hasAstType($ast)
    {
        if (is_array($ast)) {
            return false;
        } else {
            return property_exists($ast, "type") && !empty($ast->type);
        }
    }

    /**
     * 引数に指定されたASTが解決済(resolved)か検査します
     * @param AST-Branch $ast 抽象構文木(枝)
     */
    private static function isAstResolved($ast)
    {
        if (is_array($ast)) {
            return false;
        } else {
            return property_exists($ast, "resolved") && ($ast->resolved === 'yes');
        }
    }

    /**
     * 引数に指定されたASTに対応するパーサが解決済みか検査します
     * @param AST-Branch $ast 抽象構文木(枝)
     */
    private static function isAstParserResolved($ast)
    {
        if (is_array($ast)) {
            return false;
        } else {
            return property_exists($ast, "parser") && ($ast->parser instanceof Psr) && ($ast->parser->isResolved());
        }
    }

    /**
     * 引数が，要素をもつ配列か検査します
     * @param mixed $value
     */
    private static function isValidArray($value)
    {
        return !is_null($value) && is_array($value) && count($value) > 0;
    }

    /**
     * 引数に指定されたASTが，子を持つASTであり，子を保持しているか検査します
     * @param AST-Branch $ast 抽象構文木(枝)
     */
    private static function hasAstValidChild($ast)
    {
        return self::hasAstType($ast) &&
            self::hasChildren($ast->type) &&
            property_exists($ast, "child") &&
            self::isValidArray($ast->child);
    }

    /**
     * 引数に指定されたASTが，パーサを保持しているか検査します
     * @param AST-Branch $ast 抽象構文木(枝)
     */
    private static function hasAstParser($ast)
    {
        if (is_array($ast)) {
            return false;
        } else {
            return property_exists($ast, "parser") && ($ast->parser instanceof Psr);
        }
    }

    /**
     * XML形式のパーサ定義ファイルよりパーサを作成します
     * @param string $path XML定義ファイルパス
     */
    public static function loadFromXML($path)
    {
        $handler = @simplexml_load_file($path);

        //$testary = [1, 2, 3];

        //return;
        if ($handler) {
            $ctx = [];

            // 解決済みパーサリスト
            $realParsers = array();

            // 未解決パーサリスト
            $unResolves = array();

            // 遅延評価パーサリスト
            $lazys = array();

            // ①XMLからASTに変換
            $definition = Y(function (callable $callback) use (&$realParsers, &$lazys, &$unResolves) {
                return function ($element) use (&$callback, &$realParsers, &$lazys, &$unResolves) {
                    $ctx0 = [];

                    // XMLElementのため，基本はforeachでトラバースできる
                    foreach ($element as $item) {

                        $context = new \stdClass();

                        // パーサ名が存在しない(タグ名称が"parser"の場合)は，"Anonymous_[ランダムハッシュ]"をパーサ名とする
                        $context->name = ($item->getName() == 'parser') ? 'Anonymous_' . md5(rand()) : $item->getName();

                        // ルートパーサフラグを設定
                        if ($context->name === 'root') {
                            $context->is_root_parser = 1;
                        } else {
                            $context->is_root_parser = 0;
                        }

                        // 要素から属性を取り出し，プロパティにセットする
                        $attrs = $item->attributes();
                        foreach ($attrs as $key => $val) {
                            $context->$key = (string)$val;
                        }

                        // typeプロパティをもつ場合は，基本的に解決済み扱いとする
                        // ForwardParserの場合は例外で，Parser自体は生成するが，resolved=falseのままとなる
                        if (self::hasAstType($context)) {
                            $parserType = $context->type;

                            $param = property_exists($context, "param") ? $context->param : null;

                            $description = property_exists($context, "description") ? $context->description : "";

                            $context->parser = self::CreateFrom($parserType, $param);

                            $context->parser->setDescription($description);
                            $context->parser->setName($context->name);

                            $context->resolved = (self::isAstParserResolved($context)) ? 'yes' : 'no';

                            if (self::isAstForward($context)) {
                                $lazys[] = $context->name;
                                $realParsers[$context->name] = $context->parser;
                            } else {
                                if (self::isAstResolved($context)) {
                                    $realParsers[$item->getName()] = $context->parser;
                                    unset($unResolves[$context->name]);
                                } else {
                                    $unResolves[$context->name] = $context;
                                }
                            }
                        }

                        // リファレンス参照の場合は
                        // 解決済みパーサから参照解決を試みる
                        if (self::hasAstReference($context)) {
                            $ref = $context->ref;

                            if (array_key_exists($ref, $realParsers)) {
                                $parser = $realParsers[$ref];

                                $context->parser = $realParsers[$ref];
                                $context->resolved = ($context->parser->isResolved()) ? 'yes' : 'no';
                                unset($unResolves[$context->name]);
                            }
                        }

                        // ここまでの処理でresolvedプロパティが設定されていない場合は
                        // 未解決(resolved=no)扱いとする
                        if (!property_exists($context, "resolved")) {
                            $context->resolved = 'no';

                            $unResolves[$context->name] = $context;
                        }

                        // 子要素を再帰的に解析する
                        $result = $callback($item);

                        if (self::isValidArray($result)) {
                            // 子要素が正常に取得できた場合は
                            // childプロパティにセット(FowardやSeqenceなど，子パーサを持つパーサの場合)
                            $context->child = $result;
                        }

                        // コンテキストオブジェクトを配列に加える
                        array_push($ctx0, $context);
                    }

                    // コンテキストが追加された配列を返却
                    // (再帰的に処理するため，配列自体が木構造:ASTとなる)
                    return $ctx0;
                };
            })->__invoke($handler);

            //$searchAst = ;

            $searchAstClosure = function ($tryCache = true) {
                $cache = array();
                return function ($referenceName, $definition) use (&$cache, &$tryCache) {
                    if ($tryCache) {
                        if (array_key_exists($referenceName, $cache)) {
                            applLog2("SearchAST", "cache hit <$referenceName>");
                            return $cache[$referenceName];
                        } else {
                            $result = self::searchReferenceByAST($referenceName, $definition);
                            $cache[$referenceName] = $result;

                            return $result;
                        }
                    } else {
                        return self::searchReferenceByAST($referenceName, $definition);
                    }
                };
            };

            $searchAst = $searchAstClosure->__invoke(false);

            //return;
            applLog2("ParserResolver", "== 処理開始 ============================================================");
            $benchMark = \BMBench::newInstance()->start();

            // ROOTパーサ解決を行う
            $resolvedParser = Y(function ($callback) use (&$definition, &$realParsers, &$searchAst, &$benchMark) {
                return function ($referenceName) use (&$callback, &$definition, &$realParsers, &$searchAst, &$benchMark) {
                    $benchMark->mark("[$referenceName] を処理開始");
                    // referenceNameに紐付くパーサがresolved=yesであれば
                    // そのパーサを返す．
                    // resolved=noであれば，resolveしたパーサを返す

                    // 指定した参照名でASTを探索し，branchASTを返す
                    //$branch = $searchAst->__invoke($referenceName, $definition);
                    $branch = $searchAst->__invoke($referenceName, $definition);

                    if (is_null($branch)) {
                        applLog2("ParserResolver", "[$referenceName] をASTから探索できませんでした");
                        return self::T();
                    }

                    if (self::isAstResolved($branch)) {
                        applLog2("ParserResolver", "[$referenceName] を解決");
                        return $branch->parser;
                    } else {
                        // 未解決の場合

                        if (self::isAstForward($branch)) {
                            // forwardパーサであれば，解決済扱いとしてそのまま返す
                            applLog2("ParserResolver", "[$referenceName] を解決（Forward）");
                            return $branch->parser;
                        } else {
                            if (self::hasAstReference($branch)) {
                                //applLog2("ParserResolver", "[$referenceName][Ref=$branch->ref] を解決します（AST再帰探索）");
                                // refプロパティが存在すれば，refプロパティの内容を再帰的に
                                // この関数に適用する
                                if (array_key_exists($branch->ref, $realParsers)) {
                                    applLog2("ParserResolver", "[$referenceName][Ref=$branch->ref] を解決（正則リファレンス参照）");
                                    return $realParsers[$branch->ref];
                                } else {
                                    applLog2("ParserResolver", "[$referenceName][Ref=$branch->ref] を解決します（AST再帰探索）");
                                    return $callback($branch->ref);
                                }
                            } else {
                                if (self::hasAstType($branch)) {
                                    applLog2("ParserResolver", "[$referenceName][Type=$branch->type] を解決します");
                                    $branchType = $branch->type;

                                    if (self::hasAstValidChild($branch)) {
                                        applLog2("ParserResolver", "[$referenceName][Type=$branch->type] を解決中（子探索）");

                                        foreach ($branch->child as $children) {
                                            $childrenName = $children->name;

                                            if (self::isAstResolved($children)) {
                                                applLog2("ParserResolver", "[$childrenName]->[$referenceName] を解決中（子を解決）");
                                                $branch->parser->add($children->parser);

                                            } else if (self::isAstForward($children)) {
                                                applLog2("ParserResolver", "[$childrenName][Type=$children->type]->[$referenceName] を解決中（子がForward）");
                                                $branch->parser->add($children->parser);
                                            } else {
                                                // 未解決

                                                if (self::hasAstReference($children)) {
                                                    applLog2("ParserResolver", "[$childrenName] を解決中（AST再帰探索）");
                                                    $searched = $callback($children->ref);

                                                    if (is_null($searched)) {
                                                        applLog2("ParserResolver", "[$childrenName] を解決できません(結果がnull");
                                                    } else {
                                                        if ($searched instanceof Psr) {
                                                            applLog2("ParserResolver", "[$childrenName]->[$referenceName][] を解決");

                                                            $branch->parser->add($searched);
                                                        } else {
                                                            applLog2("ParserResolver", "[$childrenName] をできません(パーサが無い/リファレンスもない)");
                                                            applLog2("ParserResolver:Error", $searched);
                                                        }


                                                    }
                                                } else {
                                                    if ($children instanceof Psr) {
                                                        applLog2("ParserResolver", "[$childrenName][Type=$children->type] を解決(直接参照)");
                                                        $branch->parser->add($children);
                                                    } else {

                                                        if (self::hasAstValidChild($children)) {

                                                            if (self::hasAstParser($children)) {
                                                                applLog2("ParserResolver", "[$childrenName][Type=$children->type] を解決中（子の解析の必要あり）");

                                                                foreach ($children->child as $children0) {
                                                                    $searched0 = $callback($children0->name);
                                                                    if (!is_null($searched0) && $searched0 instanceof Psr) {
                                                                        //applLog2("ParserResolver", "[$childrenName] を解決(直接参照)");
                                                                        $branch->parser->add($searched0);
                                                                        //applLog2("ParserResolver:Error", $searched);
                                                                    } else {
                                                                        applLog2("ParserResolver", "[$childrenName][Type=$children->type] を解決できません(子の解析結果=NULL)");
                                                                    }
                                                                }
                                                            } else {
                                                                applLog2("ParserResolver", "[$childrenName][Type=$children->type] を解決できません（子の解析の必要あり/パーサ作成の必要あり）");
                                                            }

                                                        } else {
                                                            applLog2("ParserResolver", "[$childrenName][Type=$children->type] を解決できません（refが存在しない）");
                                                        }

                                                    }

                                                }
                                            }
                                        }

                                        return $branch->parser;
                                    } else {
                                        // 子をもたない
                                        applLog2("ParserResolver", "[$referenceName][$branch->type] を解決できません(？？？)");
                                        applLog2("??????????", "?????");
                                        return self::T();
                                    }
                                } else {
                                    // typeもrefも持っていない
                                    applLog2("ParserResolver", "[$referenceName] を解決できません(typeもrefもなし)");
                                    applLog2("??????????", "?????");
                                    return self::T();
                                }
                            }
                        }
                    }
                };
            })->__invoke("root");

            $profile = $benchMark->stop()->profile();


            $jsonDecoded = json_decode($resolvedParser . '', true);
            //applLog2("ParserResolver", $jsonDecoded);
            //applLog2("ParserResolver", $profile);
            applLog2("ParserResolver", "== 処理終了 ============================================================");
            //applLog2("ParserResolver:result", $resolvedParser);

            // ③遅延評価対象のパーサに関して，forwardメソッドを用いて
            //   パーサの遅延提供を設定する
            Y(function ($callback) use (&$realParsers) {
                return function ($param) use (&$callback, &$realParsers) {
                    if ($param < 2) {
                        return 1;
                    } else {
                        if ($param == 5) {
                            $parser = $realParsers['parenthesis'];

                            // 本来はrealParsersからlazysの要素を遅延組み立てする
                            $parser->forward(self::Seq()
                                ->add(self::Token("("))
                                ->add(self::Token("xyz"))
                                ->add(self::Token(")"))
                            );
                        }
                        return $callback($param - 1) + $callback($param - 2);
                    }
                };
            })/*->__invoke(6)*/
            ;

//  			applLog2("AstParser:after", $definition);
//  			applLog2("AstParser:after", self::searchReferenceByAST("root", $definition));
//  			applLog2("AstParser:after", self::searchReferenceByAST("expression", $definition));
            //applLog2("AstParser:after", $unResolves);
            //applLog2("AstParser:after", $realParsers);
            return;

            // ======================================================================================================

// 			// 再帰的に参照解決を行い，結果をパーサで返す
// 			$referenceResolver = Y(function($callback) use(&$realParsers, &$hasChildren){
// 				return function($target) use (&$callback, &$realParsers, &$hasChildren){
// 					if(is_array($target)) {

// 						foreach($target as $item) {
// 							if($item instanceof Psr) {
// 								continue;
// 							}
// // 							applLog2("ParserResolver:multi", count($item));
// // 							applLog2("ParserResolver:multi", $item);
// 							if($item['resolved'] === 'no') {


// 								if(array_key_exists("ref", $item)) {
// 									// レファレンス参照を持っている場合

// 									$referene = array_key_exists($item["ref"], $realParsers) ? $realParsers[$item["ref"]] : null;

// 									if($referene != null) {
// 										$item["parser"] = $referene;
// 										$item["resolved"] = ($item["parser"]->isResolved()) ? 'yes' : 'no';
// 									}

// 								} else if(array_key_exists("type", $item)) {
// 									$parserType = $item["type"];

// 									$result = $hasChildren->invoke($parserType);

// 									if($result->isFinished()) {
// 										// 子をもつパーサの場合
// 										if(array_key_exists("child", $item)) {
// 											$children = $item["child"];

// 											// 子パーサを再帰的に解決する
// 											$parsers = array();
// 											foreach($children as $child) {
// 												if($child["resolved"] === 'yes') {
// 													array_push($parsers, $child['parser']);
// 												} else {
// 													$resolved = $callback($child);

// 													applLog2("ParserResolver", $resolved);

// 													if($resolved["resolved"] === 'yes') {
// 														array_push($parsers, $resolved['parser']);
// 													} else {
// 														//applLog2("ParserBuilder:Error", $resolved);
// 														//throw new \Exception("Type<$parserType>::子パーサを解決できませんでした");
// 													}
// 												}
// 											}

// 											if(count($parsers) > 0) {
// 												if($parserType === 'choice' || $parserType === 'sequence' ) {
// 													foreach($parsers as $parser) {
// 														$item["parser"]->add($parser);
// 													}
// 												} else if($parserType === 'forward') {
// 													$item["parser"]->forward($parsers[0]);
// 												} else if($parserType === 'option' || $parserType === 'many') {
// 													$item["parser"]->add($parsers[0]);
// 												} else {
// 													//applLog2("ParserBuilder:Error", $item);
// 													//throw new \Exception("Type<$parserType>::未対応のパーサを検出いたしました");
// 												}
// 											} else {
// 												//applLog2("ParserBuilder:Error", $item);
// 												//throw new \Exception("Type<$parserType>::子パーサが存在しないため，親パーサを解決できません");
// 											}
// 										} else {
// 											//applLog2("ParserBuilder:Error", $item);
// 											//throw new \Exception("Type<$parserType>::このパーサには子パーサが必要です");
// 										}
// 									} else {
// 										//applLog2("ParserBuilder:Error", $item);
// 										// 未解決の子をもたないパーサ
// 										//throw new \Exception("Type<$parserType>::未解決の単一パーサが存在します．リファレンス参照をチェックしてください");
// 									}
// 								}
// 							}
// 						}

// 						return $target;
// 					} else {
// 						applLog2("ParserResolver:single", $target["name"]);
// 						if($target['resolved'] === 'no') {
// 							if(array_key_exists("ref", $target)) {
// 								$ref = $target["ref"];
// 								if(array_key_exists($ref, $realParsers)) {
// 									$parser = $realParsers[$ref];
// 									$target["parser"] = $parser;
// 									if($target["parser"]->isResolved()) {
// 										$target["resolved"] = 'yes';

// 										unset($target["ref"]);
// 									} else {
// 										// リファレンス参照したパーサが不完全
// 										//applLog2("ParserBuilder:Error", $target);
// 										//throw new \Exception("Type<$parserType>::リファレンスで参照されたパーサが不完全なため，パーサを組み立てることができませんでした");
// 									}

// 								} else {
// 									//applLog2("ParserBuilder:Error", $target);
// 									//throw new \Exception("Type<$parserType>::名前解決済のパーサリポジトリに，該当するリファレンス参照が存在しません");
// 								}
// 							} else {
// 								//applLog2("ParserBuilder:Error", $target);
// 								//throw new \Exception("Type<$parserType>::未解決の単一パーサに対して，リファレンス参照が存在しないか，パラメータが不正です");
// 							}
// 						}
// 						return $target;
// 					}
// 				};
// 			});

// 			try {
// 				$resolved = $referenceResolver->__invoke($definition);
// 			} catch(Exception $e) {
// 				applLog2("ParserResolver:Error", $e);
// 				applLog2("ParserResolver:Error", $definition);
// 			}


            //applLog2("ParserBuilder", $definition);
            //applLog2("ParserBuilder:created", $realParsers);
            return;
// 			// 内部ツリー構造->中間体１
// 			$combinator = Y(function(callable $callback){
// 				return function($def) use(&$callback){
// 					$ctx = [];

// 					$convert = Ref(function($type){
// 								switch ($type) {
// 									case 'char':
// 										return 'Char';
// 										break;
// 									case 'choice':
// 										return 'Choice';
// 										break;
// 									case 'false':
// 										return 'False';
// 										break;
// 									case 'forward':
// 										return 'Forward';
// 										break;
// 									case 'many':
// 										return 'Many';
// 										break;
// 									case 'option':
// 										return 'Option';
// 										break;
// 									case 'regex':
// 										return 'Regex';
// 										break;
// 									case 'sequence':
// 										return 'Sequence';
// 										break;
// 									case 'token':
// 										return 'Token';
// 										break;
// 									case 'true':
// 										return 'Char';
// 										break;
// 									default:
// 										return 'Undefined';
// 								}
// 							});

// 					if(count($def) > 0) {
// 						$len = count($def);

// 						for($i = 0; $i < $len; $i++) {
// 							$ctx0 = [];
// 							$item = $def[$i];

// 							$name = ($item['name'] === 'parser') ? 'Anonymous_'. md5(rand()) : $item['name'];
// 							$ref = array_key_exists('ref', $item) ? $item['ref'] : '';
// 							$type = array_key_exists('type', $item) ? $item['type'] : '';
// 							$param = array_key_exists('param', $item) ? $item['param'] : '';

// 							$type = $convert->__invoke($type);

// 							$def0 = new \stdClass();
// 							$def0->name = $name;
// 							$def0->ref = $ref;
// 							$def0->type = $type;
// 							$def0->param = $param;

// 							$log = "Name:<$name>, Reference:<$ref>, Type:<$type>, Param:<$param>";

// 							//$ctx0['definition'] = $log;
// 							$ctx0['definition'] = $def0;

// 							if(array_key_exists("child", $item)) {
// 								$child = $item['child'];
// 								$result = $callback($child);
// 								$ctx0['child'] = $result;
// 							}

// 							array_push($ctx, $ctx0);
// 						}
// 					} else {
// 						$name = $def['name'];
// 						$ref = $def['ref'];
// 						$type = $def['type'];
// 						$param = $def['param'];

// 						if($name === 'parser') {
// 							$name = 'Anonymous_'. md5(rand());
// 						}

// 						$type = $convert->invoke($type);

// 						$def0 = new \stdClass();
// 						$def0->name = $name;
// 						$def0->ref = $ref;
// 						$def0->type = $type;
// 						$def0->param = $param;

// 						//$log = "Name:$name, Reference:$ref, Type:$type, Param:$param";

// 						$ctx['definition'] = $def0;
// 					}

// 					return $ctx;
// 				};
// 			})->__invoke($definition);

// 			applLog2("ParserFactory:combinator", $combinator);

// 			// 中間体１->生成体
// 			$builder = Y(function($callback){
// 				return function($ary, $parsers, $parent, $level) use (&$callback){
// 					foreach($ary as $item) {
// 						$definition = $item['definition'];

// 						$parserName = $definition->name;
// 						//applLog2("ParserBuilder:operate", "Name:$parserName, Type:$definition->type, Ref:$definition->ref");
// 						if(!empty($definition->ref)) {
// 							applLog2("ParserBuilder:operate<Level=$level> of parent:$parent", "Name:$parserName, Type:$definition->type, Ref:$definition->ref -> create forwarder");
// 							$parser = new \stdClass();
// 							$parser->ref = $definition->ref;
// 							$parser->parser = self::Forward();

// 							$parsers[$parserName] = $parser;
// 						} else if($definition->type) {
// 							applLog2("ParserBuilder:operate<Level=$level> of parent:$parent", "Name:$parserName, Type:$definition->type, Ref:$definition->ref -> create realtype");
// 							if(array_key_exists("child", $item) && count($item['child']) > 0) {
// 								$children = $item['child'];
// 								$children0 = array();

// 								$res = $callback($children, $children0, $parserName, $level + 1);

// 								$parser = new \stdClass();

// 								$parser->parser = "Parser<$parserName><$definition->type>";

// 								$parser->param = $res;
// 								if(array_key_exists($parserName, $parsers)) {
// 									$parsers[$parserName. '_'. md5(rand())] = $parser;
// 								} else {
// 									$parsers[$parserName] = $parser;
// 								}

// 							} else {
// 								$parser = new \stdClass();

// 								if($definition->type === 'Token') {
// 									$parser->parser = self::Token($definition->param);
// 								} else if($definition->type === 'Char') {
// 									$parser->parser = self::Char($definition->param);
// 								} else if($definition->type === 'Regex') {
// 									$parser->parser = self::Regex($definition->param);
// 								} else {
// 									$parser->parser = "Parser<$parserName><$definition->type>";
// 								}

// 								$parser->param = $definition->param;

// 								if(array_key_exists($parserName, $parsers)) {
// 									$parsers[$parserName. '_'. md5(rand())] = $parser;
// 								} else {
// 									$parsers[$parserName] = $parser;
// 								}
// 							}
// 							//applLog2("ParserBuilder:type:$parserName:", $definition->type. "<Param:$definition->param>");
// 						} else {
// 							applLog2("ParserBuilder:type:$parserName:", "Illegal!");
// 						}


// 					}

// 					return $parsers;
// 				};
// 			});

// 			$parsers = array();
// 			$res = $builder->__invoke($combinator, $parsers, "<root>", 1);


// 			$createParser = Y(function(callable $callback){
// 				return function($parsers, $ctx) use(&$callback){
// 					if(is_array($ctx)) {
// 						foreach($ctx as $key => $value) {
// 							// $value = stdclass
// 							if(!empty($value->param) && is_array($value->param)) {
// 								$parsers = $callback($parsers, $value->param);
// 							}
// // 							if(array_key_exists("param", $value) && is_array($value['param'])) {
// // 								$parsers = $callback($parsers);
// // 							}

// 							// リファレンスの存在確認
// 							if(!empty($value->ref) && array_key_exists($value->ref, $parsers)) {
// 								applLog2("CreateParser:multi:ref:". $value->ref, 'real parser exists');

// 								if($value->parser instanceof Forward && $parsers[$value->ref] instanceof Psr) {
// 									applLog2("CreateParser:multi:ref:". $value->ref, "forward-parser");
// 									$value->parser->forward($parsers[$value->ref]);
// 								} else {
// 									applLog2("CreateParser:multi:ref:". $value->ref, "not-forward or ref is broken");
// 								}
// 								//applLog2("CreateParser:multi:ref:". $value->ref, $value->parser);
// 								// forwardに実態を設定
// 							} else {
// 								applLog2("CreateParser:multi:other:", "");
// 							}
// 						}

// 						return $parsers;
// 					} else {
// 						// stdclass
// 						// リファレンスの存在確認
// 						applLog2("CreateParser:single:", $ctx);

// 						$value = $ctx;
// 						if(!empty($value->param) && is_array($value->param)) {
// 							$parsers = $callback($parsers, $value->param);
// 						}

// 						// リファレンスの存在確認
// 						if(!empty($value->ref) && array_key_exists($value->ref, $parsers)) {
// 							applLog2("CreateParser:ref:". $value->ref, 'real parser exists');
// 							// forwardに実態を設定
// 						} else {
// 							applLog2("CreateParser:other:", "not reference");
// 						}

// 						return $parsers;
// 					}


// 				};
// 			});

// 			//applLog2("ParserBuilder", $res);
// 			$ctx99 = array();
// 			$created = $createParser->__invoke($res, $res);

            //applLog2("CreateParser:created", $created);

            return $definition;
        }
    }
}