<?php


$apps = cmf_scan_dir(APP_PATH . '*', GLOB_ONLYDIR);

$returnCommands = [];

foreach ($apps as $app) {
    $commandFile = APP_PATH . $app . '/command.php';

    if (file_exists($commandFile)) {
        $commands       = include $commandFile;

        $returnCommands = array_merge($returnCommands, $commands);
    }

}

return $returnCommands;