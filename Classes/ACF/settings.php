<?php
const ACF_PAGE_SLUG_OPTIONS = 'options';
const ACF_PAGE_SLUG_BACKEND = 'backend';
const ACF_PAGE_SLUG_COMMON = 'common';
return
    [
        'optionsPage'=>
            [
                [
                    'title'=>'Настройка Backend',
                    'slug'=>'backend',
                    'position' => 2,
                    'icon' => 'dashicons-admin-tools',
                    'id' => ACF_PAGE_SLUG_BACKEND,
                ],

                [
                    'title'=>'Настройка Сайта',
                    'slug'=>'option_site',
                    'position' => 3,
                    'icon' => 'dashicons-admin-tools',
                    'id' => ACF_PAGE_SLUG_OPTIONS,
                ],

                [
                    'title'=>'Настройка блоков',
                    'slug'=>'common',
                    'position' => 4,
                    'icon' => 'dashicons-admin-generic',
                    'id' => ACF_PAGE_SLUG_COMMON,
                ],
            ],
        'optionsSubPage'=>
            [
//                [
//                    'id' => 'options_post-portfolio',
//                    'slug'=>'option_post-portfolio',
//                    'title'=>'Настройка блоков "Выполненые работы"',
//                    'parent_slug'=> 'edit.php?post_type=portfolio',
//                ],
            ],

    ];