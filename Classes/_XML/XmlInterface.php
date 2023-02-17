<?php

namespace Controllers\XML;



interface XmlInterface
{

    const ERROS =
        [
            'file_not_found'=>"XML file don't find"
        ];


    /**
     * @param array $array
     * @return array|null
     */
    function parse($array):?array;

    /**
     * @param $file
     * @return array|null
     * Парсит переданный файл XML
     */
    static function init_parse(string $file):?array;


    /**
     * @param string $nodeName
     * @return array|null
     * Парсит поле из переданного файла XML
     */
    static function init_unique_node(string $file,string $nodeName):?array;


}