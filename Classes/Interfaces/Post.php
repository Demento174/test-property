<?php

namespace Classes\Interfaces;

interface Post
{
    function get_link():string;
    function get_title():string;
    function get_content():?string;
}