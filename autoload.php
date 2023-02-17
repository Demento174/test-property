<?php
function autoLoad($className) {
    $classFilePath = get_template_directory( __FILE__ ).'/' . str_replace('\\','/',$className) . '.php';

    if (file_exists($classFilePath)) {
        require_once $classFilePath;
    }
    return false;
}

spl_autoload_register('autoLoad');