<?php

namespace Classes\PostsAndTax\Taxonomy;

use Classes\Traits\CheckStaticVariables;
use Controllers\Pagination;

class Query
{

    use CheckStaticVariables;
    /**
     * @return array
     * @throws \Exception
     * Получает таксономию в виде дерева
     */
    public static function taxonomyHierarchical()
    {
        if(!self::check_static_variables(get_called_class()))
            throw new \Exception(get_called_class()." don't have required static variable");
        $type=get_called_class()::$taxType;
        $className=get_called_class();

        $result = [];

        foreach (get_terms( $type,['hide_empty' => true,'parent'=>0,'exclude'=>15]) as $key=>$item)
        {
            $parent = new $className($item->term_id);
            $result[$key]['term'] = $parent;
            foreach (get_terms( $type,['hide_empty' => false,'parent'=>$item->term_id]) as $keyKey=>$tax)
            {
                $children = new $className($tax->term_id);
                $result[$key]['children'][$keyKey]['term'] = $children;

                foreach (get_terms( $type,['hide_empty' => false,'parent'=>$tax->term_id]) as $taxTax)
                {

                    $childrenChildren = new $className($taxTax->term_id);

                    $result[$key]['children'][$keyKey]['children'][]['term']=$childrenChildren;
                }
            }
        }

        return $result;
    }

    public static function all()
    {
        if(!self::check_static_variables(get_called_class()))
            throw new \Exception(get_called_class()." don't have required static variable");
        $type=get_called_class()::$taxonomyType;
        $className=get_called_class();

        $result = [];

        foreach (get_categories( ['taxonomy'=>$type, 'hide_empty'   => false] ) as $item)
        {
            $result[] = new $className($item->term_id);
        }

        return $result;
    }

    public static function include_terms(array $include):null|array
    {

        if(!self::check_static_variables(get_called_class()))
            throw new \Exception(get_called_class()." don't have required static variable");

        $type=get_called_class()::$taxonomyType;
        $className=get_called_class();

        $result = [];

        foreach (get_categories( ['taxonomy'=>$type, 'hide_empty'=>false,'include'=>$include] ) as $item)
            $result[] = new $className($item->term_id);

        return $result;
    }

    public static function children(string $taxonomy,int $parent_ID,array $fields = ['ids']):array|null
    {
        return get_categories( ['taxonomy'=>$taxonomy, 'hide_empty'=> true,'parent'=>$parent_ID] );
    }

    public static function term_by(string $field,string $value,string $taxonomy):null|int
    {
        if(false === ($query = get_term_by( $field, $value,$taxonomy)))
            return null;
        return $query->term_id;

    }

    public static function posts_by_term_id(int $term_id,string $taxonomy_slug,string $post_type,$limit=-1,$fields = null):null|array
    {
                return get_posts(
            [
                'numberposts' => $limit,
                'post_type'   => $post_type,
                'fields'      => $fields,
                'tax_query'   => [
                    [
                        'taxonomy'  => $taxonomy_slug,
                        'field'     => 'term_id',
                        'terms'     => $term_id
                    ]
                ]
            ]
        );

    }

    public static function posts_by_term_id_with_pagination(int $term_id,string $taxonomy_slug,string $post_type,$limit=-1,$fields = null):null|array
    {
        global $wp_query;
        $input =[];

        query_posts(
            [
                'numberposts'=>2,
                'post_type' => $post_type,
                'posts_per_page' => get_option('posts_per_page'),
                'paged'         => \Classes\Pagination::get_current_page(),
                'fields'      => $fields,
                'meta_key'          => 'hot',
                'orderby'           =>  [
                    'meta_value'=>'DESC',
                    'date'=>'DESC',
                ],
                'order'             => 'DESC',
                'tax_query' =>
                    [
                        [
                            'taxonomy' => $taxonomy_slug,
                            'field' => 'id',
                            'terms' => $term_id
                        ]
                    ]
            ]
        );
        while ( $wp_query->have_posts() )
        {
            $wp_query->the_post();
            $input[]= $wp_query->post;
        }

        return $input;
    }

}