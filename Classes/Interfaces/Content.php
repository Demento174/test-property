<?php

namespace Classes\PostsAndTax\Interfaces;

interface Content
{
    function get_title():?string;
    function get_content():?string;
    function get_description():?string;
}