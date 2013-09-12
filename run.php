<?php
//solution to return *.html files
function recursiveGlob($dir, $ext) {
    $array = array();
    $globFiles = glob("$dir/*.$ext");
    $globDirs  = glob("$dir/*", GLOB_ONLYDIR);

    foreach ($globDirs as $dir) {
        recursiveGlob($dir, $ext);
    }

    foreach ($globFiles as $file) {
        if(!in_array($file,$array)) {
            array_push($array,$file);
        }
    }
    return $array;
}
//only use in CLI mode because of extra large buffer and execution time
//print_r(recursiveGlob("/home/sotiris-k/*","html"));

$files = recursiveGlob("/home/sotiris-k/*","html");

$myFile = "testFile.txt";

$fh = fopen($myFile, 'w') or die("can't open file");

foreach ($files as $file) {
    $stringData = md5_file($file)."\n";
    fwrite($fh, $stringData);
}

fclose($fh);

?>