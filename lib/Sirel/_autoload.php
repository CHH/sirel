<?php
namespace Sirel;

$_classMap = include __DIR__ . "/.classmap.php";

spl_autoload_register(function($class) use ($_classMap) {
    if (array_key_exists($class, $_classMap)) {
        require_once $_classMap[$class];
    }
});
