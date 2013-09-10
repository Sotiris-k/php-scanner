<?php

function recursiveGlob($dir, $ext) {
    $globFiles = glob("$dir/*.$ext");
    $globDirs  = glob("$dir/*", GLOB_ONLYDIR);

    foreach ($globDirs as $dir) {
        recursiveGlob($dir, $ext);
    }

    foreach ($globFiles as $file) {
        print "$file\n"; // Replace '\n' with '<br />' if outputting to browser
    }
}


//only use in CLI mode because of extra large buffer and execution time
recursiveGlob("/*","exe");

?>