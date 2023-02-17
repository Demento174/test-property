<?php

namespace Classes;

use Classes\Interfaces\InitInterface;
use Classes\Interfaces\RenderInterface;

class Pagination implements InitInterface,RenderInterface
{


    private function set_links(array $input_args=[]):?array
    {

        $args =
            [
                'type'=>'array',
                'format'=>'page/2/',
                'total'=>3,
                'current'=>self::get_current_page()
            ];

        return paginate_links(array_merge($input_args,$args));
    }




    public  function render()
    {
        echo '<nav class="pagination">';
        wp_pagenavi();
        echo '</nav>';
//        echo $this->add_styles();
        wp_reset_postdata();

    }


    private function add_styles()
    {
        return "
        <style>
        .wp-pagenavi>a, .wp-pagenavi>span{
            border:none;
   
        }
        .wp-pagenavi>a>span, .wp-pagenavi>span>span{
        border: 1px solid #E1D5EC;
        }
        .wp-pagenavi .pagination__page.current{
        border-color: #E1D5EC;
        }
        </style>
        ";
    }

    public static function init(array $args = [])
    {
        $controller = new self($args);
        return $controller;
    }

    public static function get_current_page()
    {
        ;
        return ( get_query_var('paged') ? get_query_var('paged') : 1 );
    }
}