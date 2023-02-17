<?php
namespace Controllers\PostsAndTax;

use Controllers\Translite\Translate;

class TaxCat extends TaxonomyAbstract {



    static string $taxType          = 'product_cat';
    static string $postClass        = '\Controllers\PostsAndTax\PostProduct';
    static private array $selectors =
        [
            'articles'=>
                [
                    'matrix'=>'article_matrix'
                ],
        ];

    private null|string|int $article_matrix     = null;
    private null|array $image                   = null;

    public function __construct($id)
    {
        parent::__construct($id);
//        $this->article_matrix = $this->set_article_matrix($this->term);
//        $this->image = $this->set_image($this->id);
//        $this->parent = $this->set_parent();
    }

    /**
     * -------------------------[ SETTERS ]-------------------------
     */
    private function set_image($id):?array
    {
        $thumbnail_id = get_term_meta( $id, 'thumbnail_id', true );

        if($thumbnail_id)
            return [
                'url'=>wp_get_attachment_url( $thumbnail_id ),
                'alt'=>$this->title
            ];

        return null;
    }

    private function set_article_matrix(\WP_Term $term):null|string|int
    {
        return get_field(self::$selectors['articles']['matrix'],$term);
    }

    /**
     * -------------------------[ GETTERS ]-------------------------
     */
    public function get_id():int
    {
        return (int) $this->id;
    }

    public function get_title():string
    {
        return $this->title;
    }

    public function get_article():?string
    {
        return (string) $this->article_matrix;
    }

    public function get_link():string
    {
        return $this->link;
    }

    public function get_image():?array
    {

        return null===$this->image?
            [
                'url'=>get_field('shop',ACF_PAGE_SLUG_OPTIONS)['default_image']['url'],
                'title'=>$this->title
            ]:
            $this->image;
    }

    public function get_children():?array
    {
        return $this->set_children();
    }

    /**
     * -------------------------[ UPDATE ]-------------------------
     */
    public function update_article_matrix(string|int|null $article=null)
    {

        if(null !== $article)
        {
            update_field(self::$selectors['articles']['matrix'], $article, $this->term);

            $this->article_matrix = $article;
        }

    }

    public function update(
        string|null $name = null,
        string|null $slug = null,
        string|null $description =null,
        int|null $parent=null)
    {
        $result = wp_update_term( $this->get_id(), self::$taxType, array(
            'name' => null===$name?$this->get_title():$name,
            'slug' => null===$slug?$this->slug:$slug,
            'description' => null===$description?$this->description:$description,
            'parent' => null===$parent?$this->parent:$parent,
        ));



        if('object' === gettype($result) && 'WP_Error' === get_class($result))
            throw new \Exception($result->get_error_message().' '.$name);
        $term = $this->set_term($result['term_id'],self::$taxType);
        $this->title = $this->set_title($term);
        $this->slug = $this->set_slug($term);
        $this->description = $this->set_description($term);

    }

    /**
     * -------------------------[ STATIC METHODS ]-------------------------
     */
    public static function existence_category_by_article_matrix(string|int $article):bool
    {
        $args = array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'meta_query' => array(
                array(
                    'key'       => self::$selectors['articles']['matrix'],
                    'value'     => $article,
                    'compare'   => '='
                )
            )
        );

        return count(get_terms($args))>0;
    }



    public static function get_termHierarchical(
                                                 $parent = 0,
                                                 $result=[],
                                                 $type='product_cat')
    {


        $children = get_terms( $type,
            [
                'hide_empty' => true,
                'parent'=>$parent,
                'fields'=>'ids'
            ]);

        if(empty($children))
            return null;


        foreach ($children as $child)
            $result[]=
                [
                    'term'=>new self($child),
                    'children'=>self::get_termHierarchical($child)
                ];

        return $result;
    }



    public static function create(
        string $title,
        int|null $id_matrix=null,
        int|null $parentID=null,
        string|null $slug = null,
        string|null $description = null):TaxCat
    {
        $parent = null===$parentID?null:new self($parentID);

        $response = wp_insert_term( $title, self::$taxType, array(
            'description'   => $description,
            'parent'        => null === $parent?null:$parent->get_id(),
            'slug'          => null===$slug?Translate::convertor($title):$slug,
        ) );

        if('object' === gettype($response) && 'WP_Error' === get_class($response))
            throw new \Exception($response->get_error_message().' '.$title);


        $tax =  new self($response['term_id']);

        $tax->update_article_matrix($id_matrix);

        return  $tax;
    }



    public static function get_by_article_matrix(int $article):?TaxCat
    {
        $args = array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'meta_query' => array(
                array(
                    'key'       => self::$selectors['articles']['matrix'],
                    'value'     => $article,
                    'compare'   => '='
                )
            )
        );
        $result = get_terms($args);
        return count($result)>0?new self($result[0]->term_id):null;
    }

    public static function delete_all_terms()
    {
        $terms = get_terms( array(
            'taxonomy' => self::$taxType,
            'hide_empty' => false
        ) );

        foreach ( $terms as $term )
        {

            wp_delete_term($term->term_id, self::$taxType);
        };
    }

}