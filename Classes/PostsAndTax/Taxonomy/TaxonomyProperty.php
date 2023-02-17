<?php

namespace Classes\PostsAndTax\Taxonomy;

use Classes\PostsAndTax\Taxonomy\ATaxonomy;
use Classes\PostsAndTax\Posts\PostProperty;

class TaxonomyProperty extends ATaxonomy
{
    private static string $typeTaxonomy = 'taxonomy_property';
    public static $IDS=
        [
            'rent'=>27,
            'buy'=>5
        ];
    private int $count =0;

    public function __construct( int $id)
    {
        parent::__construct(self::$typeTaxonomy, $id);
        $this->count = $this->set_count();
    }

    private function set_count():int
    {
        $query = new \WP_Query(
            array(
                'post_type'     => 'property', //post type, I used 'product'
                'post_status'   => 'publish', // just tried to find all published post
                'posts_per_page' => -1,  //show all
                'tax_query' => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => self::$typeTaxonomy,  //taxonomy name  here, I used 'product_cat'
                        'field' => 'id',
                        'terms' => array( $this->id )
                    )
                )
            )
        );
        return (int)$query->post_count;
    }

    public function get_id():int
    {
        return $this->id;
    }

    public function get_title():string
    {
        return $this->title;
    }
    public function get_link():string
    {
     return get_term_link($this->id);
    }

    public function get_count():int
    {
        return $this->count;
    }

    public function get_children()
    {
        $children = self::query()::children(self::$typeTaxonomy,$this->id);
        $result = [];

        foreach ($children as $child)
            $result[]= new self($child->term_id);
        return $result;
    }

    public  function get_posts($limit=-1):null|array
    {
        if(0>=count($_posts = self::query()::posts_by_term_id_with_pagination($this->id,self::$typeTaxonomy,PostProperty::$postType,$limit,'ids')))
            return null;
        $result=[];
        foreach ($_posts as $_post)
            $result[]=new PostProperty($_post);

        return $result;
    }

    public static function query_rent($limit=15):array|null
    {

        if(0>=count($_posts = self::query()::posts_by_term_id(self::$IDS['rent'],self::$typeTaxonomy,PostProperty::$postType,$limit,'ids')))
            return null;
        $result = [];
        foreach ($_posts as $_post)
            $result[]=new PostProperty($_post);
        return $result;
    }

    public static function query_buy($limit=15):array|null
    {

        if(0>=count($_posts = self::query()::posts_by_term_id(self::$IDS['buy'],self::$typeTaxonomy,PostProperty::$postType,$limit,'ids')))
            return null;

        $result = [];
        foreach ($_posts as $_post)
            $result[]=new PostProperty($_post);
        return $result;
    }


}
