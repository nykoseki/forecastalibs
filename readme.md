Forecastalib
====

Overview

## Description
様々な文字列を柔軟にパースするためのパーサコンビネータ集を提供します。
また、各種パーサコンビネータの作成を容易にするためのユーティリティも含みます。


## Demo

## VS. 

## Requirement
composer.jsonを参照してください
## Usage

    $parser = ParserFactory::Seq()
        ->add(ParserFactory::Token("<title>"))
        ->add(ParserFactory::Regex("/^[^<>]+/"))
        ->add(ParserFactory::Token("</title>"));
    $target = "<title>Hello World</title>";
    
    $context = $parser->parse(ParserContext::create($target));
    $parsed = $context->parsed();
    
    echo print_r($parsed);

----
    Array
    (
        [0] => <title>
        [1] => Hello World
        [2] => </title>
    )
    
        

## Install

    composer require nykoseki/forecastalibs

## Contribution

## Licence

[MIT](https://github.com/tcnksm/tool/blob/master/LICENCE)

## Author

[nykoseki/forecastalibs](https://github.com/nykoseki/forecastalibs)