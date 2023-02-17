<?php

namespace Classes\PostsAndTax\Interfaces;

interface  Price
{
    function get_currency():?string;

    function get_price():?string;

    function get_regular_price():?int;

    function get_sale_price():?int;

    function check_sale():bool;
}