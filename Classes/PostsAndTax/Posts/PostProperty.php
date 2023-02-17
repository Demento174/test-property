<?php

namespace Classes\PostsAndTax\Posts;

use Classes\Interfaces\Post;
use Classes\Interfaces\SimilarPosts;
use Classes\Interfaces\Images;
use Classes\PostsAndTax\Taxonomy\TaxonomyProperty;

class PostProperty extends APost implements Post,SimilarPosts,Images
{
    private static $fields =
        [
            'hot',
            'square',
            'price',
            'facilities'=>['no_of_bathroom','bedrooms','parking'],
            'date_create',
            'unit_reference',
            'permit_number'
        ];

    public static $postType = 'property';
    private static $taxonomyType = 'taxonomy_property';

    private bool|null    $hot = null;
    private int|null     $square;
    private int|null     $price;
    private array|null   $facilities;
    private string|null  $dateCreate;
    private string|null  $unit_reference;
    private string|null  $permit_number;
    private string|null  $property_name;
    private array|null   $similar;
    private TaxonomyProperty|null $term_firstLevel = null;
    private TaxonomyProperty|null $term_secondLevel = null;
    private TaxonomyProperty|null $term_thirdLevel = null;

    public function __construct(int|null $id = null)
    {
        parent::__construct(self::$postType, $id);
        $this->price = $this->set_price();
        $this->facilities = $this->set_facilities();
        $this->square = $this->set_square();
    }


    protected function set_link($id):string
    {

        if($this->get_termFirstLevel())
            return '/'.$this->get_termFirstLevel()->slug.'/'.get_post_field( 'post_name', $this->id );

        return parent::set_link($id);

    }

    public function set_hot():bool
    {
        return get_field('hot',$this->id);
    }
    private function set_square():int|null
    {
        return get_field('square',$this->id);
    }
    private function set_price():int|null
    {
        return get_field('price',$this->id);
    }
    private function set_facilities():array|null
    {
        return get_field('facilities',$this->id);
    }

    private function set_dateCreate():string|null
    {
        return get_field('date_create',$this->id);
    }
    private function set_unit_reference():string|null
    {
        return get_field('unit_reference',$this->id);
    }
    private function set_permit_number():string|null
    {
        return get_field('permit_number',$this->id);
    }
    private function set_property_name():string|null
    {

        return get_field('property_name',$this->id);
    }
    private function set_similar():array|null
    {


        if(!$posts = self::query()::meta_field(self::$postType,'property_name',$this->property_name,-1,'ids'))
            return null;

        $result = [];
        foreach ($posts as $post)
            $result[]=new self($post);
        return $result;
    }



    function get_link():string
    {
        return $this->link;
    }

    function get_title():string
    {
        return $this->title;
    }

    function get_content():?string
    {
        return null;
    }

    function get_cross_sell():?array
    {
        return $this->similar;
    }

    function get_upsell():?array
    {
        return $this->similar;
    }

    function get_image(): ?array
    {

        if($url = get_the_post_thumbnail_url( $this->id, 'full' ))
            return
                [
                    'url'=>$url,
                    'alt'=>$this->title
                ];
        return null;
    }

    function get_hot():bool
    {
        if(null === $this->hot)
            $this->hot = $this->set_hot();
        return $this->hot;
    }

    function get_price():int
    {
        return $this->price;
    }

    function get_address():string|null
    {
        return get_field('address',$this->id);
    }

    function get_bads():int
    {
        return $this->facilities['bedrooms'];
    }

    function get_baths():int
    {
        return $this->facilities['no_of_bathroom'];
    }

    function get_parking():string|int|null
    {
        return $this->facilities['parking'];
    }

    function get_square():int|float
    {
        return'en'===\WPGlobus::Config()->language?
            $this->square:
            (float)$this->square*0.0003048;
    }

    function get_dateCreate():string|null
    {
        return $this->dateCreate;
    }

    function get_unit_reference():string|null
    {
        return $this->unit_reference;
    }

    function get_permit_number():string|int|null
    {
        return $this->permit_number;
    }

    function get_termFirstLevel():TaxonomyProperty|null
    {
        foreach (get_the_terms($this->id,self::$taxonomyType) as $term)
            if(0 === $term->parent)
            {
               $result = new TaxonomyProperty($term->term_id);
                $this->term_firstLevel = $result;
                return $result;
            }
        return null;
    }

    function get_termSecondLevel():TaxonomyProperty|null
    {
        if(null === $this->term_firstLevel)
            $this->get_termFirstLevel();
                if(null === $this->term_firstLevel)
                    return null;

        foreach (get_the_terms($this->id,self::$taxonomyType) as $term)
            if($this->term_firstLevel->get_id() === $term->parent)
            {
                $result = new TaxonomyProperty($term->term_id);
                $this->term_secondLevel = $result;
                return $result;
            }
        return null;
    }

    public static function hot_posts($limit=15):array|null
    {
        $_posts = self::query()::meta_field(self::$postType,'hot',true,$limit,'ids');

        if(0 < count($_posts))
            return self::convert_post($_posts,get_called_class());
        return null;
    }

    public static function init_full(int $id):PostProperty
    {
        $self = new self($id);

        $self->hot              = $self->set_hot();
        $self->square           = $self->set_square();
        $self->price            = $self->set_price();
        $self->facilities       = $self->set_facilities();
        $self->dateCreate       = $self->set_dateCreate();
        $self->unit_reference   = $self->set_unit_reference();
        $self->permit_number    = $self->set_permit_number();
        $self->property_name    = $self->set_property_name();
        $self->similar          = $self->set_similar();
        return $self;
    }
}