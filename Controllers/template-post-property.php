<?php
global $post;
$property = \Classes\PostsAndTax\Posts\PostProperty::init_full($post->ID);
$currencyClass = new \Classes\Currency();

get_header();
renderBlock('cart',
    [
        'title'             => $property->get_title(),
        'parking'           => $property->get_parking(),
        'hot'               => $property->get_hot(),
        'price'             => $property->get_price(),
        'address'           => $property->get_address(),
        'image'             => $property->get_image(),
        'bads'              => $property->get_bads(),
        'baths'             => $property->get_baths(),
        'square'            => $property->get_square(),
        'dateCreate'        => $property->get_dateCreate(),
        'unit_reference'    => $property->get_unit_reference(),
        'permit_number'     => $property->get_permit_number(),
        'currency'          =>
            'en'===WPGlobus::Config()->language?
                $currencyClass->usd($property->get_price()):
                $currencyClass->ru($property->get_price()),
        'similar'=>$property->get_cross_sell(),
        'categories'=>
            [
                'first'=>$property->get_termFirstLevel(),
                'second'=>$property->get_termSecondLevel(),
            ],
        'square_unity'=>'en'===WPGlobus::Config()->language?'ft':'km'


    ]);

get_footer();