<?php

// $isMatched = preg_match('#(\w+)\s+(\w+)\s+(\S+)#i', "Documents                Linked    /gpfs1/Documents", $matches);
// var_dump($matches);
// if ($isMatched == 1) {
//                 $fileset["name"] = $matches[1];
//                 $fileset["status"] = $matches[2];
//                 $fileset["path"] = $matches[3];
// }

// $isMatched = preg_match('#(\w+.[^:]*):\s*(.*)\s*#i', "flags:  ", $matches);

// $fileset = array();
// if ($isMatched == 1) {
//                 $fileset["key"] = str_replace(" ", "_", strtolower($matches[1]));
//                 $fileset["value"] = $matches[2];
// }
// var_dump($fileset);

$tmp = explode("/", trim("/gpfs1/Documents/ICOS Entry.pptx"));
$filename = array_pop($tmp);
$dirpath = implode("/", $tmp);
var_dump($filename);
var_dump($dirpath);
?>