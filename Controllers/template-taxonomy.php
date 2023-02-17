<?php

$taxonomy = new \Classes\PostsAndTax\Taxonomy\TaxonomyProperty(get_queried_object()->term_id);

get_header();
renderBlock('blocks/intro',
    [
        'title'=>get_queried_object()->name,
        'classes'=>'catalog'
    ]);

renderBlock('catalog',
    [
        'children'=>$taxonomy->get_children(),
        'items'=>$taxonomy->get_posts()
    ]);

get_footer();