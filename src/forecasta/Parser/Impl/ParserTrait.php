<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
use Forecasta\Parser\HasMoreChildren;
use Forecasta\Parser\ParserContextFactory;

use Forecasta\Parser\Impl\Util\Cacheable;

/**
 * パーサに関する共通機能を集約したトレイトです
 * @author nkoseki
 *
 */
trait ParserTrait
{
    use Cacheable;

    /**
     * パーサ名です
     * @var
     */
    private $name;

    /**
     * パーサの説明文です
     * @var
     */
    private $description;

    /**
     * パーサの解析状態を詳細に出力する場合はtrueを設定します
     * @var bool
     */
    private $debugMode = false;

    /**
     * このパーサが解析した出力をスキップする場合はtrueを設定します
     * @var bool
     */
    private $skipFlg = false;

    /**
     * パーサの論理構文木を作成するために、内部的に使われるタグです
     * @var string
     */
    private $tag = "";

    /**
     * デバッグフラグを取得します
     * @return mixed
     */
    public function isDebugMode()
    {
        return $this->debugMode;
    }

    /**
     * デバッグフラグを設定します
     * @param $debugMode
     * @return $this
     */
    public function setDebug($debugMode)
    {
        $this->debugMode = $debugMode;

        return $this;
    }

    /**
     * タグを取得します
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * タグを設定します
     * @param $tag
     * @return $this
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * スキップフラグを取得します
     * @return mixed スキップフラグ
     */
    public function isSkip()
    {
        return $this->skipFlg;
    }

    /**
     * このパーサが抽象構文木として生成されるかを設定します.
     * setSkip(true)を設定した場合、抽象構文木としてのせいせいをスキップします.
     * @param $skipFlg
     * @return $this
     */
    public function skip($skipFlg)
    {
        $this->skipFlg = $skipFlg;

        return $this;
    }

    /**
     * 引数に指定した文字列を，このパーサで解析します
     * @param $target
     * @return ParserContext
     */
    public function invoke($target)
    {
        $param = CTX::create($target);

        return $this->parse($param);
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 詳細情報を設定します
     * @param $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * 詳細情報を取得します
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     *
     * @param ParserContext $context
     * @param int $depth
     * @param string $alias
     */
    public function onSuccess(ParserContext $context, $depth = 0, $alias = "")
    {
        if ($this->debugMode) {
            $className = get_class($this);
            applLog2("Parser:onSuccess", "[Name:$this->name] of <$className>");
        }

        $pad = str_repeat("    ", $depth);

        if ($this->debugMode === true) {

            $tab = str_repeat("  ", 1);
            $tab2 = str_repeat("  ", 2);
            $tabLen = mb_strlen($tab);

            $message = $context->toFlatString();

            foreach (explode("\n", $message) as $item) {
                $item = $tab2 . $item;
                //echo "\033[1;32m{$item}\033[0m" . "\n";
            }

            $message = $tab . "FinishParse: {$this->getName()}, Depth:{---}";
            //echo "\033[1;35m{$message}\033[0m" . "\n";

            $depth = str_pad($depth, "4", "0", STR_PAD_LEFT);

            //echo "{$pad}Close:Depth:{$depth} {$this->getName()}(Success)\n";
        }
    }

    /**
     *
     * @param ParserContext $context
     * @param int $depth
     * @param string $alias
     */
    public function onError(ParserContext $context, $depth = 0, $alias = "")
    {
        if ($this->debugMode) {
            $className = get_class($this);
            applLog2("Parser:onError", "[Name:$this->name] of <$className>");
        }

        $pad = str_repeat("    ", $depth);

        if ($this->debugMode === true) {

            $tab = str_repeat("  ", 1);
            $tabLen = mb_strlen($tab);

            $message = $tab . "ParseFail: {$this->getName()} try at. {$context->target()} (StartPos: {$context->current()})";
            $len = mb_strlen("ParseFail: {$this->getName()} try at. ");
            $prefix = str_repeat(" ", $len + $tabLen);
            $prefix = $prefix . str_repeat("+", $context->current()) . "^";
            $message = $message . "\n" . $prefix;

            //echo "\033[1;31m{$message}\033[0m" . "\n";
            //echo "ParseFail: {$this->getName()} try at. {$context->target()} (StartPos: {$context->current()})". "\n";

            $message = $tab . "FinishParse: {$this->getName()}, Depth:{---}";
            //echo "\033[1;35m{$message}\033[0m" . "\n";
            //echo "FinishParse: {$this->getName()}". "\n";

            $depth = str_pad($depth, "4", "0", STR_PAD_LEFT);

            //echo "{$pad}Close:Depth:{$depth} {$this->getName()}(Error)\n";
        }
    }

    /**
     *
     * @param int $depth
     * @param string $alias
     */
    public function onTry($depth = 0, $alias = "")
    {
        if ($this->debugMode) {
            $className = get_class($this);
            applLog2("Parser:onTry", "[Name:$this->name] of <$className>");
        }

        $pad = str_repeat("    ", $depth);

        if ($this->debugMode === true) {
            $tab = str_repeat("  ", 2);
            $tabLen = mb_strlen($tab);

            $message = $tab . "TryParse: {$this->getName()}, Depth:{---}";

            //echo "\033[1;34m{$message}\033[0m" . "\n";

            $depth = str_pad($depth, "4", "0", STR_PAD_LEFT);

            //echo "{$pad}Open :Depth:{$depth} {$this->getName()}\n";
            //echo "Close: {$this->getName()}";
        }
    }

    /**
     * パーサの出力結果を修飾します
     * @param $parsed
     * @return string
     */
    public function decolateParsed($parsed)
    {
        if (!empty($this->getName())) {
            $nm = $this->getName();
            $clsName = get_class($this);

            $intermediate = $parsed;
            $intermediate = preg_replace("/\s+/", "", $intermediate);
            $intermediate = str_replace("\r\n", "", $intermediate);
            $intermediate = str_replace("\r", "", $intermediate);
            $intermediate = str_replace("\n", "", $intermediate);

            if (empty($intermediate)) {
                //$parsed = "<WhiteSpace>";
                //return "<{$this->getName()}>";
                return "";
            }

            if ($parsed === "\"") {
                $parsed = '\"';
                //return "<{$this->getName()}>";
            }

            return "{\"{$nm}\" : \"{$parsed}\"}";
        } else {
            return $parsed;
        }
    }

    /**
     * 引数に指定したパーサにこのパーサを追加します.
     * @param P\Parser $parser
     * @return $this
     */
    public function addAt(Parser $parser)
    {
        if ($parser instanceof HasMoreChildren) {
            $parser->add($this);
        }
        return $this;
    }

    public function includedOn(Parser $pre, Parser $post) {
        if($pre == null) {
            $pre = ParserFactory::True();
        }

        if($post == null) {
            $post = ParserFactory::True();
        }

        $parser = ParserFactory::Seq()
            ->add($pre)
            ->add($this)
            ->add($post);

        return $parser;
    }
}

