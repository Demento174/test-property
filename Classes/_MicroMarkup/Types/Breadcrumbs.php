<?php

namespace Controllers\MicroMarkup\Types;

class Breadcrumbs extends MicroMarkupAbstract
{
    static $TYPE = 'BreadcrumbList';
    static $TYPE_ITEM = 'ListItem';

    protected  function set_type():string
    {
        return self::$TYPE;
    }

    protected  function add_data()
    {

        foreach (\BreadcrumbsYoast::DATA() as $key=>$item)
        {
            $this->data["itemListElement"][] =
                [
                    "@type"=> self::$TYPE_ITEM,
                    "position"=> $key+1,
                    "name"=> $item['text'],
                    "item"=> $item['url']
                ];
        }
    }

    public function get_data():array
    {
        return $this->data;
    }

    public static function index()
    {
        $controller  = new self();
        return $controller;
    }
}