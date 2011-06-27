<?php

require __DIR__ . "/../lib/Sirel/_autoload.php";

spl_autoload_register(function($class) {
    $class = str_replace('Sirel\\Test', '', $class);

    $file = __DIR__ . DIRECTORY_SEPARATOR . "Sirel" . DIRECTORY_SEPARATOR
        . str_replace('\\', DIRECTORY_SEPARATOR, $class) . ".php";

    if (!file_exists($file)) {
        return false;
    }

    require $file;
});
