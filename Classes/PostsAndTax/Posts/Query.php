<?php

namespace Classes\PostsAndTax\Posts;

class Query
{
    public static function all()
    {

        $args =
            [
                'post_type' => get_called_class()::$postType,
                'posts_per_page' => get_option('posts_per_page'),
                'paged'         => Pagination::get_current_page(),
            ];
        global $wp_query;
        query_posts( $args );
        $result = [];
        while ( $wp_query->have_posts() )
        {
            $wp_query->the_post();
            $result[]= $wp_query->post->ID;

        }
        return self::convert_post($result,get_called_class());
    }

    public static function include_posts($include)
    {
        return self::convert_post(
            get_posts(
                [
                    'post_type'=>get_called_class()::$postType,
                    'include'=>$include,'numberposts'=>-1
                ]),
            get_called_class()
        );


    }

    public static function taxonomy_posts($tax_id, $taxClass, array $filters=null):?Array
    {

        $input =  get_posts(
            [
                'post_type' => get_called_class()::$postType,
                'posts_per_page' => get_option('posts_per_page'),
                'paged'         => Pagination::get_current_page(),
                'tax_query' =>
                    [
                        [
                            'taxonomy' => get_called_class()::$taxClass::$taxType,
                            'field' => 'id',
                            'terms' => $tax_id
                        ]
                    ]
            ]
        );
        if (null===$filters)
            return self::convert_post($input,get_called_class());
        $result = [];
        foreach ($input as $_product)
        {
            $product = wc_get_product( $_product->ID );

            if(
                key_exists('from',$filters)
                and $filters['from'] !== '0'
                and  $product->get_price()>=$filters['from']
            ) $result[]=$product;

            if(
                key_exists('to',$filters)
                and
                $filters['to'] !== '0'
                and
                $product->get_price()<=$filters['to']
            ) $result[]=$product;
        }

        return self::convert_post($result,get_called_class());
    }

    public static function by_search(string $text):?array
    {
        global $wp_query;
        $input = [];
        query_posts(
            [
                'post_type'=>get_called_class()::$postType,
                'posts_per_page' => get_option('posts_per_page'),
                'paged'         => Pagination::get_current_page(),
                's'=>$text,
            ]);
        while ( $wp_query->have_posts() )
        {
            $wp_query->the_post();
            $input[]= $wp_query->post;

        }

        return self::convert_post($input,
            get_called_class());

    }

    /**
     * @param int|null $id
     * @return int|null
     * Запрос ID, если пришёл пустой параметр делает запрос к глобальному объекту $post
     */
    public static function id(int|null $id=null):?int
    {
        global $post;

        return null === $id?$post->ID:$id;
    }

    public static function meta_field(string $post_type, string $key,mixed $value,int $limit = -1,array|string|null $fields = null):null|array
    {
        return get_posts(
            [
                'numberposts'       => $limit,
                'post_type'         => $post_type,
                'fields'            => $fields,
                'meta_query'        =>
                    [
                        [
                            'relation'      => 'AND',
                            [
                                'key'       => $key,
                                'value'     => $value,
                                'compare'   => '='
                            ],
                        ]
                    ],
            ]
        );
    }

}