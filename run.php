<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
//solution to return *.exe files
$array = array();
function recursiveGlob($dir, $ext) {
    global $array;
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


if ($argv[1] == "help" || $argv[1] == null) {
    echo "-- SCAN MODE --\r\n";
    echo "Usage: php run.php scan --path=path_to_check --extension=extension_to_filter --log_file=path_and_name_of_log_file \r\n";
    echo "\r\n";
    echo "Example: php run.php scan --path=\"C:\myfolder\\\" --extension=\"exe\" --log_file=\"C:\mydocuments\md5_log.txt\" \r\n";
    echo "\r\n";
    echo "Default values: --path=\"C:\Windows\" --extension=\"exe\" --log_file=\"C:\\md5_log_file.txt\" \r\n";
    echo "\r\n";
    echo "-- COMPARE MODE --\r\n";
    echo "Usage: php run.php compare --log_file=path_and_name_of_log_file --md5_values_file=path_and_name_of_md5_values_file --results_file=path_of_results_file \r\n";
    echo "\r\n";
    echo "Example: php run.php compare --log_file=\"C:\\log_file_after_scan.txt\" --md5_values_file=\"C:\\file_with_clean_md5_values.txt --results_file=\"C:\\file_with_results.txt \r\n";
    echo "\r\n";
    die();
}

if ($argv[1] == "debug") {

    for ($i=2; $i < $argc; $i++) { 
       $tempvar = explode("=", $argv[$i]);
       echo $tempvar[1]."\r\n";
   }
}

if ($argv[1] == "scan") {
    echo "Starting scan. Please wait...\r\n";
    for ($i=2; $i < $argc ; $i++) { 
        $tempvar = explode("=", $argv[$i]);
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
        }
    }
    if ($argc < 5) {
        if (!$directory) {
            $directory = "C:\Windows\\*";
        }
        if (!$extension) {
            $extension = "exe";
        }
        if (!$log_file) {
            $log_file = "C:\\md5_log_file.txt";
        }
    }

    echo "the directory is ".$directory."\r\n";
    echo "the extension is ".$extension."\r\n";
    echo "the log file is ".$log_file."\r\n";

    $files = recursiveGlob($directory,$extension);
    $fh = fopen($log_file, "w") or die("can't open file \r\n");
    var_dump($files);
    foreach ($files as $file) {
        $stringData = "Found file: ".$file."\r\n";
        $stringData .= "Hash: ".md5_file($file)."\r\n";
        fwrite($fh, $stringData);
    }

    fclose($fh);

}

if ($argv[1] == "compare") {
    echo "Starting compare. Please wait...\r\n";
    for ($i=2; $i < $argc ; $i++) { 
        $tempvar = explode("=", $argv[$i]);
        switch ($tempvar[0]) {
            case '--md5_values_file':
            $md5_values_file = $tempvar[1];
            break;
            case '--log_file':
            $log_file = $tempvar[1];
            break;
            case '--results_file':
            $results_file = $tempvar[1];
            break;
            default:
            echo "error: loop defaulted\r\n";
            break;
        }
    }

    if ($argc < 4) {
        echo "Wrong count of parameters. Please type php run.php help for more info";
        die();
    }

    $scanned = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $clean_md5 = file($md5_values_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $clean_md5_size = count($clean_md5);

    $fh = fopen($results_file, "w") or die("can't open file \r\n");
    foreach ($scanned as $line_num => $line) {
        $check = explode(" ", $line);
        if ($check[0] == "Hash:") {
            foreach ($clean_md5 as $clean_line_num => $clean_line) {
                if ($check[1] == $clean_line) {
                    break;
                }
                if (($clean_line_num + 1) == $clean_md5_size) {
                    echo "Suspicious file found -> ".$pathname[1]."\r\n";
                    $stringData = "Suspicious file found -> ".$pathname[1]."\r\n";
                    fwrite($fh, $stringData);
                }
            }
        }
        else {
            $pathname = explode(": ", $line);
        }
    }
    fclose($fh);
}

?>
