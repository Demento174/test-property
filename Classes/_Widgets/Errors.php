<?php

namespace Controllers\Widgets;

class Errors extends \Exception
{
    public const ERROR =
        [
            'name'=>'field name is required',
            'title'=>'field title is required',
            'template'=>'field template is required',
            'category'=>'field category is required',
            'template_directory'=>'template directory does not exist',
            /**
             * Errors in template
             */

            'template_title'=>'field title in template twig is required',
            'template_namespace'=>'field namespace in template twig is required',
        ];


    public static function init(string $message)
    {
        wp_die($message);
    }
}
