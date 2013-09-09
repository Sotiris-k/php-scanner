<?php
function listdirs_safe($start)
{
    $dir  = $start;
    $dirs = array();
    $next = 0;

    while (true)
    {
        $_dirs = glob($dir.'{,.}/*.exe', GLOB_BRACE);

        if (count($_dirs) > 0)
        {
            foreach ($_dirs as $key => $_dir)
                $dirs[] = $_dir;
        }
        else
            break;
            
        $dir = $dirs[$next++];
    }
    
    return $dirs;
}

var_dump(listdirs_safe('/*'));
?>