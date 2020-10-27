<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Common\Historical;
use Forecasta\Parser\HistoryEntry;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
use Forecasta\Parser\ParserFactory;

/**
 * Alt-JSONパーサです
 *
 * [Overview]
 * ======================================================
 * <LBrace> := "{"
 * <RBrace> := "}"
 * <LBracket> := "["
 * <RBracket> := "]"
 * <Primitive> := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
 * <Null> := null
 * <Comma> := ,
 * <Empty> := ""
 * <Number> := /^[0-9]+/
 * <Boolean> := /^true|^false|^TRUE|^FALSE/
 * <Value> := <Primitive> | <Element> | <Array> | <Null> | <Number>
 * <Joint> := ":"
 * <Key> := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
 * <Entry> := <Key> + <Joint> + <Value>
 * <Entries> := <Entry> | (<Entry> + <Camma> ) + <Entry>
 * <Element> := <LBrace> + <Entries> + <RBrace>
 * <Array> := <LBracket> + (<Value> | (<Value> + <Camma> ) + <Value>) + <RBracket>
 * ======================================================
 *
 * @author nkoseki
 *
 */
class JsonParser implements Parser
{
    use ParserTrait;
    use Historical;

    private $parser = null;

    /**
     * パースメソッド
     * @param $context 解析コンテキスト
     * @param int $depth 解析深度
     * @param HistoryEntry|null $currentEntry 履歴エントリ
     * @return ParserContext 解析コンテキスト
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

        $currentCtx = ParserContext::getBlank();

        // 履歴エントリ作成
        $childHistory = HistoryEntry::createEntry($this->parser->getName(), $context->copy(), $this->parser);
        $currentEntry->addEntry($childHistory);

        $ctx = $this->parser->parse($context, $depth, $childHistory);

        if($ctx->result()) {
            $this->onSuccess($ctx, $depth);

            // 履歴leave処理
            $currentEntry->leave($this, $ctx->copy(), true);
        } else {
            $this->onError($ctx, $depth);

            // 履歴leave処理
            $currentEntry->leave($this, $ctx->copy(), false);
        }

        return $ctx;
    }

    /**
     * JSONParserを生成します
     * @param string $objLeftChar 左括弧(デフォルト"{")
     * @param string $objRightChar 右括弧(デフォルト"}")
     * @param string $joint オブジェクト結合子(デフォルト":")
     *
     */
    public function __construct($objLeftChar = "{", $objRightChar = "}", $joint = ":")
    {

        /*
         * [Parser Overview]
         * <LBrace> := "{"
         * <RBrace> := "}"
         * <LBracket> := "["
         * <RBracket> := "]"
         * <Primitive> := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
         * <Null> := null
         * <Comma> := ,
         * <Empty> := ""
         * <Number> := /^[0-9]+/
         * <Value> := <Primitive> | <Element> | <Array> | <Null> | <Number>
         * <Joint> := "=>"
         * <Key> := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
         * <Entry> := <Key> + <Joint> + <Value>
         * <Entries> := <Entry> | (<Entry> + <Comma> ) + <Entry>
         * <Element> := <LBrace> + <Entries> + <RBrace>
         * <Array> := <LBracket> + (<Value> | (<Value> + <Comma> ) + <Value>) + <RBracket>
         *
         */

        // 改行・ホワイトスペース
        $whiteSpace = new LbWsParser;
        $whiteSpace->skip(true);

        // Bool値(true, false, TRUE, FALSE)
        $boolean = new BoolParser;

        // Joint := "=>"
        $joint = ParserFactory::Joint($joint)->setName("Joint");
        //$joint = ParserFactory::Token(":")->setName("Joint");

        $objectLeft = ParserFactory::ObjLeft($objLeftChar);
        $objectRight = ParserFactory::ObjRight($objRightChar);

        // ダブルクォート
        //$quote = ParserFactory::Token("\"")->setName("Quote");
        $quote = ParserFactory::Quote()->setName("Quote");
        //$quote->skip(true);


        // LeftBrace("[")
        $leftBrace = ParserFactory::Lbr();

        // RightBrace("]")
        $rightBrace = ParserFactory::Rbr();

        // カンマ
        $comma0 = ParserFactory::Comma();

        // カンマ１(Option)
        $commaOption = ParserFactory::Option()->add($comma0/*->setName("Comma")*/);

        // カンマ２(None-Option)
        $commaNoneOption = $comma0;

        // Primitive := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
        //$primitive = ParserFactory::Regex("/^[A-Za-z]+/")->setName("Pr");
        //$primitive = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^[A-Za-z0-9_\-,:. #@\`';\/\+\*=]+/")->setName("Primitive"))->add($quote);
        //$primitive = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^([A-Za-z0-9_\-,:. #@\`';\/\+\*=]|[\u3400-\u4DBF])+/")->setName("Primitive"))->add($quote);
        //$primitive = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^([A-Za-z0-9_\(\)\-,:. #@\`';\/\+\*=]|([\u3400-\u4DBF\u4E00-\u9FFF\uF900-\uFAFF]|[\uD840-\uD87F][\uDC00-\uDFFF]|[ぁ-んァ-ヶ]|[^\x{01}-\x{7E}]))+/")->setName("Primitive"))->add($quote);
        //$primitive = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^([A-Za-z0-9_\(\)\-,:. #@\`';\/\+\*=]|([\u3400-\u4DBF\u4E00-\u9FFF\uF900-\uFAFF]|[\uD840-\uD87F][\uDC00-\uDFFF]|[ぁ-んァ-ヶ]|[^\x{01}-\x{7E}]))+/")->setName("Primitive"))->add($quote);
        //$primitive = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^([A-Za-z0-9_\(\)\-,:. #@\`';\/\+\*=]|[^\x{01}-\x{7E}])+/")->setName("Primitive"))->add($quote);
        $primitive = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^([A-Za-z0-9_\(\)\-,:. #@\`';\/\+\*=]|[^\x{01}-\x{7E}])+/")->setName("Primitive"))->add($quote);
        //$primitive = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^([A-Za-z0-9_\(\)\-,:. #@\`';\/\+\*=]|([\u3400-\u4DBF\u4E00-\u9FFF\uF900-\uFAFF]|[\uD840-\uD87F][\uDC00-\uDFFF]|[ぁ-んァ-ヶ]))+/")->setName("Primitive"))->add($quote);
        //$primitive = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^([A-Za-z0-9_\(\)\-,:. #@\`';\/\+\*=]|([\x{3400}-\x{4DBF}\x{4E00}-\x{9FFF}\x{F900}-\x{FAFF}]|[\x{D840}-\x{D87F}][\x{DC00}-\x{DFFF}]|[ぁ-んァ-ヶ]|[^\x01-\x7E]))+/")->setName("Primitive"))->add($quote);
        //$primitive = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^[A-Za-z0-9_\-,:     '';\/\+\*=]+/")->setName("Primitive"))->add($quote);
        //$primitive = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/")->setName("Primitive"))->add($quote);

        // Number
        //$number = ParserFactory::Regex("/^[0-9]+/")->setName("Number");
        $number = ParserFactory::Number()->setName("Number");

        // Key := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
        //$key = ParserFactory::Regex("/^[A-Za-z]+/")->setName("Key");
        $key = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^[A-Za-z_0-9][A-Za-z_0-9\-]*/")->setName("Key"))->add($quote);

        // Value
        $value = ParserFactory::Forward()->setName("Value");

        // Element
        $element = ParserFactory::Forward()->setName("Element");

        // Array
        $array = ParserFactory::Forward()->setName("Array");

        // Null
        $null = ParserFactory::Token("null")->setName("NULL");

        // Empty
        //$empty = ParserFactory::Seq()->add($quote)->add($quote)->setName("Empty");
        $empty = new EmptyParser;
        $empty->setName("Empty");

        // Entry := Key + Joint + Value
        $entry = ParserFactory::Seq()/*->setName("Entry")*/
            ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
            ->add($key)
            ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
            ->add($joint)
            ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
            ->add($value)
            ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
            ->setName("Entry");

        // Entries := (Entry , ) + Entry | Entry
        $entries = ParserFactory::Choice()/*->setName("Entries")*/
            ->add(
                ParserFactory::Seq()->add(
                    ParserFactory::Any()->add(
                        ParserFactory::Seq()
                            ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                            ->add($entry)
                            //->add(ParserFactory::Option()->add($whiteSpace))
                            ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                            ->add($commaNoneOption)
                            //->add(ParserFactory::Option()->add($whiteSpace))
                            ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                            ->add($commaOption)
                            ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                    )
                )->add(
                    $entry
                )
            )->add($entry)->setName("Entries");

        // Array := "[" + (Value | (Value , ) + Value) + "]"
        $array->forward(
            ParserFactory::Seq()
                ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                //->add(ParserFactory::Token("[")->setName("ArOpen"))
                ->add($leftBrace->setName("ArOpen"))

                ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                ->add(
                    ParserFactory::Choice()
                        ->add(
                            ParserFactory::Seq()->add(
                                ParserFactory::Any()->add(
                                    ParserFactory::Seq()
                                        ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                                        ->add($value)
                                        //->add(ParserFactory::Option()->add($whiteSpace))
                                        ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                                        ->add($commaNoneOption)
                                        //->add(ParserFactory::Option()->add($whiteSpace))
                                        ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                                )
                            )->add(
                                ParserFactory::Seq()
                                ->add($whiteSpace)
                                ->add($value)
                                ->add($whiteSpace)

                            )
                        )->add(
                            ParserFactory::Seq()
                                ->add($whiteSpace)
                                ->add($value)
                                ->add($whiteSpace)
                        )

                )
                ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                ->add($rightBrace->setName("ArClose"))
                //->add(ParserFactory::Token("]")->setName("ArClose"))
                ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)

        )->setName("Array");

        // JSONと異なり、ValueにEntry({Key : Value})を許可する
        // Value := Primitive | Element | Array | Null | Empty | Bool | Entry
        $value->forward(
            ParserFactory::Choice()/*->setName("ValueInner")*/
                ->add($null)
                ->add($primitive)
                ->add($element)
                ->add($array)
                ->add($empty)
                ->add($number)
                ->add($boolean)

                // 拡張JSONとしてValueにもEntry(KeyPair)を許可する
                ->add(
                    ParserFactory::Seq()
                        ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                        ->add($objectLeft->setName("ElOpen"))
                        ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                        ->add($entry)
                        ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                        ->add($objectRight->setName("ElClose"))
                        ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                )
        )->setName("Value");



        // Element := "{" + Entries + "}"
        $element->forward(
            ParserFactory::Seq()
                ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                ->add($objectLeft->setName("ElOpen"))
                ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                ->add($entries)
                ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
                ->add($objectRight->setName("ElClose"))
                ->add(/*ParserFactory::Option()->add($whiteSpace)*/$whiteSpace)
        )->setName("Element");

        $this->parser = $element;

        $this->name = "Json";
    }

    /**
     * 解析済みのコンテキストからPHPで参照可能な形式に変換します
     * @param ParserContext $context 解析コンテキスト(解析済み)
     * @param bool $ASSOC　配列形式で返す場合はtrue/それ以外はstdClass形式で返却される
     */

    /**
     * 解析済みのコンテキストからPHPで参照可能な形式に変換します
     * @param ParserContext $context 解析コンテキスト(解析済み)
     * @param bool $ASSOC 配列形式で返す場合はtrue/それ以外はstdClass形式で返却される
     * @return array|mixed|string PHP-JSON形式
     */
    public function contextToObject(ParserContext $context, $ASSOC=true) {
        $parsed = $context->parsed();

        // 縮約処理
        $result = \Forecasta\Common\ArrayUtil::reduction($parsed);

        // フラット化処理
        $result = \Forecasta\Common\ArrayUtil::flatten($result);

        $result = implode("", $result);
        $result = str_replace("<Empty>", "\"\"", $result);
        $result = str_replace("/", "\\/", $result);

        // JSONデコード処理
        $result = json_decode($result, $ASSOC);

        //echo "処理終了\n";

        return $result;
    }

    public function isResolved()
    {
        return true;
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