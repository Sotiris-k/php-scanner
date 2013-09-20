<?php
//solution to return *.exe files
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
//print_r(recursiveGlob("C:\Windows\*","html"));

if ($argv[1] == "help" || $argv[1] == null) {
    echo "-- SCAN MODE --\r\n";
    echo "Usage: php run.php scan --path=path_to_check --extension=extension_to_filter --log_file=path_and_name_of_log_file \r\n";
    echo "Example: php run.php scan --path=\"C:\myfolder\\\" --extension=\"exe\" --log_file=\"C:\mydocuments\md5_log.txt\" \r\n";
    echo "Default values: --path=\"C:\Windows\" --extension=\"exe\" --path_to_log_file=\"C:\\md5_log_file.txt\" \r\n";
    echo "\r\n";
    echo "-- COMPARE MODE --\r\n";
    echo "Usage: php run.php compare --log_file=path_and_name_of_log_file --md5_values_file=path_and_name_of_md5_values_file\r\n";
    echo "Example: php run.php compare --log_file=\"C:\\log_file_after_scan.txt\" --md5_values_file=\"C:\\file_with_clean_md5_values.txt \r\n";
    die();
}

if ($argv[1] == "scan") {
    for ($i=2; $i <= $argc ; $i++) { 
        $tempvar = explode("=", $argv[i]);
        switch ($tempvar[0]) {
            case '--path':
            $directory = $tempvar[1]."*";
            break;
            case '--extension':
            $extension = $tempvar[1];
            break;
            case '--log_file':
            $log_file = $tempvar[1];
            break;
            default:
            if (!$directory) {
                $directory = "C:\Windows\\*";
            }
            if (!$extension) {
                $extension = "exe";
            }
            if (!$log_file) {
                $log_file = "C:\\md5_log_file.txt";
            }
            break;
        }
    }

    $files = recursiveGlob($directory,$extension);

    $fh = fopen($log_file, "w") or die("can't open file");

    foreach ($files as $file) {
        $stringData = "Found file: ".$file."\r\n";
        $stringData .= "Hash: ".md5_file($file)."\r\n";
        fwrite($fh, $stringData);
    }

    fclose($fh);

}

if ($argv[1] == "compare") {
 for ($i=2; $i <= $argc ; $i++) { 
    $tempvar = explode("=", $argv[i]);
    switch ($tempvar[0]) {
        case '--md5_values_file':
        $md5_values_file = $tempvar[1];
        break;
        case '--log_file':
        $log_file = $tempvar[1];
        break;
        default:
        echo "error: loop defaulted\r\n";
        break;
    }
}
$scanned = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$clean_md5 = file($md5_values_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($scanned as $line_num => $line) {
    $check = explode(" ", $line);
    if ($check[0] == "Hash:") {
        foreach ($clean_md5 as $clean_line_num => $clean_line) {
            if ($check[1] == $clean_line) {
                    $clean = true;
                    break;
            } else {
                echo "Suspicious file found -> ".$pathname[1];
                unset($clean);
            }
        }
    } else {
        $pathname = explode(": ", $line);
    }
}
}

?>