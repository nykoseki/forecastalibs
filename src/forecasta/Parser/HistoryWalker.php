<?php

namespace Forecasta\Parser;


class HistoryWalker implements HistoryWalkerBase {

    public function walk(HistoryEntry $entry) {
        //$childCount = $entry->

        $isRoot = $entry->isRoot() ? 1 : 0;
        $name = $entry->getName();
        $depth = $entry->getDepth();

        $parserType = $entry->getParserType();

        $length = $entry->length();
        $position = $entry->position();
        $parsed = $entry->parsed();

        $isSuccess = $entry->isSuccess() ? "Success" : "Failure";

        $indent = str_repeat("  ", $depth);

        if($entry->isSuccess()) {
            echo $indent. "<parse-entry name=${name} type=${parserType} length=${length} position=${position} state='${isSuccess}' root='${isRoot}'>". "\n";

            echo $indent. $indent. "<content><![CDATA[";

            $output = "";
            if(is_string($parsed)) {
                $ary = explode("\n", $parsed);

                for($i = 0; $i < count($ary); $i++) {
                    $line = $ary[$i];
                    $line = trim($line);
                    $output = $output . $line;
                }
            }

            echo "]]></content>\n";


            if($entry->hasMoreChildren()) {
                $children = $entry->children();
                foreach($children as $child) {
                    $child->walk($this);
                }
            }
            echo "\n". $indent. "</parse-entry>". "\n";

        } else {
            if($entry->hasMoreChildren()) {
                $children = $entry->children();
                foreach($children as $child) {
                    $child->walk($this);
                }
            }
        }


    }
}