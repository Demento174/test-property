<?php

namespace Classes\PostsAndTax\Taxonomy;

use Carbon\Exceptions\Exception;
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

    protected function set_link(int $ID, $type):string
    {
        $link = parent::set_link($ID,$type);


            $query_array = array_values(
                array_filter(
                    explode('/',parent::set_link($ID,$type)),
                    fn($element)=>!empty($element)
                )
            );

            unset($query_array[0]);//Удаляем http:
            unset($query_array[1]);//Удаляем domain
            // Если в запросе есть данные о странице

            if($key_page=array_search('page',$query_array))
            {
                unset($query_array[$key_page++]);
                unset($query_array[$key_page]);
            }
            $query_array = array_values($query_array);
            if(1===count($query_array))
                return parent::set_link($ID,$type);

            $child_uri_array = explode('_',$query_array[1]);
            $child = count($child_uri_array)>1?$child_uri_array[1]:$child_uri_array[0];

            if(2===count($query_array))
                return str_replace($query_array[1],$child,parent::set_link($ID,$type));

            $child_child_uri_array = explode('_',$query_array[2]);

            $child_child = count($child_child_uri_array)>1?$child_child_uri_array[1]:$child_child_uri_array[0];

            return str_replace($query_array[2],$child_child,
                str_replace($query_array[1],$child,parent::set_link($ID,$type))
            );

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
        return $this->link;
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

    public static function get_query(array $query_array):string|Exception
    {
        // Если в запросе есть данные о странице
        if($key_page=array_search('page',$query_array))
        {
            unset($query_array[$key_page++]);
            unset($query_array[$key_page]);
        }


        // Если это главная таксономия (Buy или Rent)
        if(1===count($query_array))
            return $query_array[0];

        $parent = new self(self::query()::term_by('slug',$query_array[0],self::$typeTaxonomy));
        $child = null;

        foreach ($parent->get_children() as $item)
            if(stripos($item->slug,$query_array[1]) or 0 === stripos($item->slug,$query_array[1]))
                $child = $item;

        if(null===$child)
            throw new \Exception($query_array[1].'(2 level) not found in main category');
        // Если это дочерняя таксономия
        if(2===count($query_array)):
            return $child->slug;
        elseif (3===count($query_array)):

            $child_child = null;
            foreach ($child->get_children() as $item)
                if(stripos($item->slug,$query_array[2]) or 0 ===stripos($item->slug,$query_array[2]))
                    $child_child = $item;
            if(null===$child_child)
                throw new \Exception($query_array[2].'(3 level) not found in main category');
            return $child_child->slug;
        endif;

        throw new \Exception('wrong number of elements in taxonomy query TaxonomyProperty::get_query');


    }
}
