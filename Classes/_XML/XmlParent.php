<?php

namespace Controllers\XML;

abstract class XmlParent implements XmlInterface
{
    protected $file = null;
    protected $controller = null;

    protected function check_file_exist_by_url(string $url):bool
    {
        $urlHeaders = @get_headers($url);
        return strpos($urlHeaders[0], '200');
    }


    protected abstract function set_file(string $file);

    protected abstract function set_controller($class);

    function parse($array): ?array
    {
        // TODO: Implement parse() method.
        return [];
    }

    public static function init_parse($file): ?array
    {
        // TODO: Implement init_parse() method.
        return [];
    }

    public static function init_unique_node(string $file,string $nodeName): ?array
    {
        // TODO: Implement init_unique_node() method.
        return [];
    }
}