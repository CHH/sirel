<?php

namespace Sirel;

require __DIR__ . "/Sirel.php";

spl_autoload_register(function($class) {
    $file = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR 
          . str_replace('\\', DIRECTORY_SEPARATOR, $class) . ".php";

    if (!realpath($file)) {
        return false;
    }

    require $file;

    if (!class_exists($class, false) and !interface_exists($class, false)) {
        trigger_error("$class was not found in file $file", E_USER_WARNING);
        return false;
    }
    return true;
});
