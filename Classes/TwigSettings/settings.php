<?php
use Controllers\Blocks\Blocks\Breadcrumbs;
use Controllers\Blocks\Blocks\SearchField;

return
    [
        'dirname'=>'Views',
        'functions'=>
            [
                'get_field'=>'get_field',
                'wp_head'=>'wp_head',
                'wp_footer'=>'wp_footer',
                'get_permalink'=>'get_permalink',
                'public_dir'=>function(){return get_template_directory_uri().'/public/';},
                'current_url'=>function(){return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";},


                'pagination_render'=>function()
                {
                    $pagination = \Classes\Pagination::init();
                    $pagination->render();
                },

            ],

    ];