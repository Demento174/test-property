<?php

namespace Classes\Interfaces;

interface SimilarPosts
{
    function get_cross_sell():?array;

    function get_upsell():?array;
}