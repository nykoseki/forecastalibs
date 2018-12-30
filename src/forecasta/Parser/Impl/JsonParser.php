<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;
use Forecasta\Parser\ParserFactory;

/**
 * JSONパーサです
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
    public function parse($context)
    {
        return $this->parser->parse($context);
    }

    public function __construct()
    {
        $whiteSpace = ParserFactory::Option()->add(ParserFactory::Regex("/^\s+/"));
        $lineBreak = ParserFactory::Option()->add(ParserFactory::Token("\n"));

        $whiteSpace = ParserFactory::Seq()->add($whiteSpace)->add($lineBreak)->add($whiteSpace);


        // Joint := "=>"
        $joint = ParserFactory::Token("->")/*->setName("Joint")*/
        ;

        $quote = ParserFactory::Token("\"");

        // Primitive := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
        //$primitive = ParserFactory::Regex("/^[A-Za-z]+/")->setName("Pr");
        $primitive = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^[A-Za-z0-9_]+/")->setName("Pr"))->add($quote);

        // Key := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
        //$key = ParserFactory::Regex("/^[A-Za-z]+/")->setName("Key");
        $key = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^[A-Za-z_]+/")->setName("Key"))->add($quote);

        // Value
        $value = ParserFactory::Forward();

        // Element
        $element = ParserFactory::Forward();

        // Array
        $array = ParserFactory::Forward();


        // Entry := Key + Joint + Value
        $entry = ParserFactory::Seq()
            ->add($whiteSpace)
            ->add($key)
            ->add($whiteSpace)
            ->add($joint)
            ->add($whiteSpace)
            ->add($value)
            ->add($whiteSpace);

        // Entries := (Entry , ) + Entry | Entry
        $entries = ParserFactory::Choice()
            ->add(
                ParserFactory::Seq()->add(
                    ParserFactory::Any()->add(
                        ParserFactory::Seq()
                            ->add($entry)
                            //->add(ParserFactory::Option()->add($whiteSpace))
                            ->add($whiteSpace)
                            ->add(ParserFactory::Token(","))
                            //->add(ParserFactory::Option()->add($whiteSpace))
                            ->add($whiteSpace)
                    )
                )->add(
                    $entry
                )
            )->add($entry);

        // Array := "[" + (Value | (Value , ) + Value) + "]"
        $array->forward(
            ParserFactory::Seq()
                ->add($whiteSpace)
                ->add(ParserFactory::Token("["))
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
                                        ->add(ParserFactory::Token(","))
                                        //->add(ParserFactory::Option()->add($whiteSpace))
                                        ->add($whiteSpace)
                                )
                            )->add(
                                $value
                            )
                        )->add($value)
                )
                ->add($whiteSpace)
                ->add(ParserFactory::Token("]"))
                ->add($whiteSpace)
        );

        // Value := Primitive | Element | Array
        $value->forward(
            ParserFactory::Choice()
                ->add($primitive)
                ->add($element)
                ->add($array)
        );

        // Element := "{" + Entries + "}"
        $element->forward(
            ParserFactory::Seq()
                ->add($whiteSpace)
                ->add(ParserFactory::Token("{"))
                ->add($whiteSpace)
                ->add($entries)
                ->add($whiteSpace)
                ->add(ParserFactory::Token("}"))
                ->add($whiteSpace)
        );

        $this->parser = $element;
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
        /*
        $className = get_class($this);
        applLog("outputRecursive", $searched);
        $searched[] = $this->name;

        $className = str_replace("\\", "/", $className);

        $name = $this->name;
        $param = $this->chars;
        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":\"$param\"}";
        */

        return $this->parser->outputRecursive($searched). "";
    }
}