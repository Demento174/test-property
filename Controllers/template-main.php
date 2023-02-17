<?php
/*
Template Name: Очень крутой дизайн
*/

get_header();

renderBlock('blocks/intro',
    [
        'title'=>get_field('title'),
        'classes'=>'front'
    ]);

$block_carousel_hot = new \Classes\Blocks\BlockCarouselHot();
$block_carousel_hot->render();

$block_carousel_2 = new \Classes\Blocks\BlockCarousel2();
$block_carousel_2->render();

get_footer();