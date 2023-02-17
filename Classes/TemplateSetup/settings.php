<?php

return
    [
        'supports'=>
            [
                [
                    'title'=>'post-thumbnails',
                    'options'=>
                        array_merge(['post',],\Classes\CustomTypes\CustomPostType::get_post_types_slugs()),
                ],
                [
                    'title'=>'post-formats',
                    'options'=>
                        [
                            'aside',
                            'image',
                            'video',
                            'quote',
                            'link',
                            'gallery',
                            'audio',
                        ],
                ],
                [
                    'title'=>'custom-background',
                    'options'=>
                        [
                            'default-color'          => '',
                            'default-image'          => '',
                            'wp-head-callback'       => '_custom_background_cb',
                            'admin-head-callback'    => '',
                            'admin-preview-callback' => ''
                        ],
                ],
                [
                    'title'=>'custom-header',
                    'options'=>
                        [
                            'default-image'          => '',
                            'random-default'         => false,
                            'width'                  => 0,
                            'height'                 => 0,
                            'flex-height'            => false,
                            'flex-width'             => false,
                            'default-text-color'     => '', // вызывается функций get_header_textcolor()
                            'header-text'            => true,
                            'uploads'                => true,
                            'wp-head-callback'       => '',
                            'admin-head-callback'    => '',
                            'admin-preview-callback' => '',
                            'video'                  => false, // с 4.7
                            'video-active-callback'  => 'is_front_page', // с 4.7
                        ],
                ],
            ],
        'menu'=>
            [
                'header_menu'   => 'Меню в шапке',
                'footer_menu'   => 'Меню в подвале',
            ],
        'other'=>
            [
                'svg'=>true
            ]
    ];