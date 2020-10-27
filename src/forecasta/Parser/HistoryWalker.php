<?php

namespace Forecasta\Parser;


use Forecasta\Common\ArrayUtil;

class HistoryWalker implements HistoryWalkerBase {

    public function walk(HistoryEntry $entry, $depth=0) {
        //$childCount = $entry->

        $isRoot = $entry->isRoot() ? 1 : 0;
        $name = $entry->getName();
        //$depth = $entry->getDepth();
        $depth = $depth;

        $parserType = $entry->getParserType();

        $length = $entry->length();
        $position = $entry->position();
        $parsed = $entry->parsed();

        $parsed = ArrayUtil::reduction($parsed);

        $isSuccess = $entry->isSuccess() ? "Success" : "Failure";

        $indent = str_repeat("  ", $depth);
        $indent2 = str_repeat("  ", $depth + 1);
        $indent3 = str_repeat("  ", $depth + 2);


        echo $indent. "<parse-entry name=\"${name}\" type=\"${parserType}\" length=\"${length}\" position=\"${position}\" state=\"${isSuccess}\" root=\"${isRoot}\">". "\n";



        $content = print_r($parsed, true);
        //$content = $parsed;
        if(is_null($content)) {

        } else if(is_array($content)) {
            foreach($content as $item) {
                echo $indent2. $item. "\n";
            }
        } else {
            $content = explode("\n", $content);
            if(is_array($content)) {
                if(count($content) == 1) {
                    $content0 = $content[0];
                    echo $indent2."<content><![CDATA[${content0}]]></content>\n";
                } else {
                    //echo $indent2."<content><![CDATA[". "\n";
                    foreach($content as $item) {
                        echo $indent3. "<content><![CDATA[${item}]]></content>". "\n";
                    }
                    //echo $indent2."]]></content>". "\n";
                }
            } else {
                echo $indent2."<content><![CDATA[${content}]]></content>\n";
                //echo $indent2. $content. "\n";
            }
        }



        if($entry->hasMoreChildren()) {
            $children = $entry->children();
            foreach($children as $child) {
                $child->walk($this, $depth + 1);
            }
        }
        echo $indent. "</parse-entry>". "\n";


    }
}