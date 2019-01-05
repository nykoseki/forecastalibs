<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;
use Forecasta\Parser\ParserFactory;

/**
 * JSONパーサです
 * [Overview]
 * ======================================================
 * <LBrace> := "{"
 * <RBrace> := "}"
 * <LBracket> := "["
 * <RBracket> := "]"
 * <Primitive> := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
 * <Null> := null
 * <Camma> := ,
 * <Empty> := ""
 * <Number> := /^[0-9]+/
 * <Boolean> := /^true|^false|^TRUE|^FALSE/
 * <Value> := <Primitive> | <Element> | <Array> | <Null> | <Number>
 * <Joint> := "=>"
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
class JsonParser implements P\Parser
{
    use PST;

    //private $chars;

    private $parser = null;

    /**
     * パースメソッド
     * @param ParserContext $ctx
     * @return ParserContext コンテキスト
     */
    public function parse($context, $depth=0)
    {
        $depth = $depth + 1;

        $this->onTry($depth);

        $currentCtx = P\ParserContext::getBlank();

        $ctx = $this->parser->parse($context, $depth);

        if($ctx->result()) {
            $this->onSuccess($ctx, $depth);
        } else {
            $this->onError($ctx, $depth);
        }

        return $ctx;
    }

    public function __construct()
    {

        /*
         * [Parser Overview]
         * <LBrace> := "{"
         * <RBrace> := "}"
         * <LBracket> := "["
         * <RBracket> := "]"
         * <Primitive> := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
         * <Null> := null
         * <Camma> := ,
         * <Empty> := ""
         * <Number> := /^[0-9]+/
         * <Value> := <Primitive> | <Element> | <Array> | <Null> | <Number>
         * <Joint> := "=>"
         * <Key> := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
         * <Entry> := <Key> + <Joint> + <Value>
         * <Entries> := <Entry> | (<Entry> + <Camma> ) + <Entry>
         * <Element> := <LBrace> + <Entries> + <RBrace>
         * <Array> := <LBracket> + (<Value> | (<Value> + <Camma> ) + <Value>) + <RBracket>
         *
         */

        // 改行・ホワイトスペース
        $whiteSpace = new LbWsParser;

        // Bool値(true, false, TRUE, FALSE)
        $boolean = new BoolParser;

        // Joint := "=>"
        $joint = ParserFactory::Token(":")->setName("Joint");
        ;

        // ダブルクォート
        $quote = ParserFactory::Token("\"")/*->setName("Quote")*/;

        // Primitive := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
        //$primitive = ParserFactory::Regex("/^[A-Za-z]+/")->setName("Pr");
        $primitive = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^[A-Za-z0-9_\-,:     '';\/\+\*=]+/")->setName("Primitive"))->add($quote);

        // Number
        $number = ParserFactory::Regex("/^[0-9]+/")->setName("Number");

        // Key := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
        //$key = ParserFactory::Regex("/^[A-Za-z]+/")->setName("Key");
        $key = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^[A-Za-z_][A-Za-z_0-9\-]*/")->setName("Key"))->add($quote);

        // Value
        $value = ParserFactory::Forward()/*->setName("Value")*/;

        // Element
        $element = ParserFactory::Forward()/*->setName("Element")*/;

        // Array
        $array = ParserFactory::Forward()/*->setName("Array")*/;

        // Null
        $null = ParserFactory::Token("null")/*->setName("NULL")*/;

        // Empty
        //$empty = ParserFactory::Seq()->add($quote)->add($quote)->setName("Empty");
        $empty = new EmptyParser;
        //$empty->setName("Empty");

        // Entry := Key + Joint + Value
        $entry = ParserFactory::Seq()/*->setName("Entry")*/
            ->add($whiteSpace)
            ->add($key)
            ->add($whiteSpace)
            ->add($joint)
            ->add($whiteSpace)
            ->add($value)
            ->add($whiteSpace);

        // Entries := (Entry , ) + Entry | Entry
        $entries = ParserFactory::Choice()/*->setName("Entries")*/
            ->add(
                ParserFactory::Seq()->add(
                    ParserFactory::Any()->add(
                        ParserFactory::Seq()
                            ->add($entry)
                            //->add(ParserFactory::Option()->add($whiteSpace))
                            ->add($whiteSpace)
                            ->add(ParserFactory::Token(",")/*->setName("Comma")*/)
                            //->add(ParserFactory::Option()->add($whiteSpace))
                            ->add($whiteSpace)
                            ->add(
                                ParserFactory::Option()->add(ParserFactory::Token(",")/*->setName("Comma")*/)
                            )
                    )
                )->add(
                    $entry
                )
            )->add($entry);

        // Array := "[" + (Value | (Value , ) + Value) + "]"
        $array->forward(
            ParserFactory::Seq()
                ->add($whiteSpace)
                ->add(ParserFactory::Token("[")/*->setName("ArOpen")*/)
                ->add($whiteSpace)
                ->add(
                    ParserFactory::Choice()
                        ->add(
                            ParserFactory::Seq()->add(
                                ParserFactory::Any()->add(
                                    ParserFactory::Seq()
                                        ->add($value)
                                        //->add(ParserFactory::Option()->add($whiteSpace))
                                        ->add($whiteSpace)
                                        ->add(ParserFactory::Token(",")/*->setName("Comma")*/)
                                        //->add(ParserFactory::Option()->add($whiteSpace))
                                        ->add($whiteSpace)
                                )
                            )->add(
                                $value
                            )
                        )->add($value)
                )
                ->add($whiteSpace)
                ->add(ParserFactory::Token("]")/*->setName("ArClose")*/)
                ->add($whiteSpace)
        );

        // Value := Primitive | Element | Array | Null | Empty | Bool
        $value->forward(
            ParserFactory::Choice()/*->setName("ValueInner")*/
                ->add($null)
                ->add($primitive)
                ->add($element)
                ->add($array)
                ->add($empty)
                ->add($number)
                ->add($boolean)
        );



        // Element := "{" + Entries + "}"
        $element->forward(
            ParserFactory::Seq()
                ->add($whiteSpace)
                ->add(ParserFactory::Token("{")/*->setName("ElOpen")*/)
                ->add($whiteSpace)
                ->add($entries)
                ->add($whiteSpace)
                ->add(ParserFactory::Token("}")/*->setName("ElClose")*/)
                ->add($whiteSpace)
        );

        $this->parser = $element;

        $this->name = "Json";
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