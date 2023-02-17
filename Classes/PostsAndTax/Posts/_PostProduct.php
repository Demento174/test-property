<?php


namespace Classes\PostsAndTax\Posts;

use Classes\Pagination;
use Classes\PostsAndTax\Interfaces\Content;
use Classes\PostsAndTax\Interfaces\Images;
use Classes\PostsAndTax\Interfaces\Post;
use Classes\PostsAndTax\Interfaces\Price;
use Classes\PostsAndTax\Interfaces\SimilarPosts;

use Classes\PostsAndTax\Posts\Post as AbstractPost;

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';


class PostProduct extends AbstractPost implements Price,
    SimilarPosts,
    Content,
    Images,Post {

    static $postType = 'product';
    static $taxClass = '\Controllers\PostsAndTax\TaxCat';
    static $fields =
        [

        ];
    static $selectors  =
        [
            'articles'=>
                [
                    'matrix'=>'article_matrix'
                ]
        ];

    private \WC_Product_Simple|null $product            = null;
    public      PostAttachment|null $attachment              = null;
    private string|null $article                        = null;
    private  float|null $price                          = null;
    public  array|null $gallery                        = null;
    private string|null $short_description              = null;
    public string|null $description                    = null;
    public  array|null $attributes                     = null;
    public  array|null $cross_sell                     = null;
    public  array|null $upsell                         = null;
    private string|null $currency                       = null;
    public   bool|null $new                            = null;
    private null|string|int $article_matrix             = null;

    public function __construct($id=null)
    {
        parent::__construct(self::$postType,$id);

        $this->product              = $this->set_product($this->id);

        $this->attachment           = $this->set_attachment();

        $this->image                = $this->set_image($this->product->get_id() );

        $this->article              = $this->set_article();

        $this->price                = $this->set_price();

        $this->currency             = $this->set_currency();

        $this->short_description    = $this->set_short_description($this->product);

//        $this->taxonomies           = $this->set_taxonomy(self::$taxClass::$taxType,self::$taxClass);
//        $productClass->article_matrix       = get_field(self::$selectors['articles']['matrix'],$this->id);
    }

    /**
     * ---------------------[ SETTERS ]------------------------
     */
    private function set_attachment():?PostAttachment
    {
        $attachment_id = (int) $this->product->get_image_id();
        if(0===$attachment_id)
            return null;
        return new PostAttachment($attachment_id);
    }

    private function set_currency($data=null):?string
    {

        return  get_woocommerce_currency_symbol('RUB');

    }

    public function set_new($id):?bool
    {
        return get_field('new',$id);
    }

    private function set_product($id):?\WC_Product_Simple
    {

        return wc_get_product($id);
    }

    private function set_article():?string
    {

        return  $this->product->get_sku();
    }

    private function set_price():?float
    {

        return (float) $this->product->get_price();
    }

    public function set_gallery():?array
    {
        if(!$this->product->get_gallery_image_ids()) return null;

        $result = [];
        foreach ($this->product->get_gallery_image_ids() as $item)
        {
            $result[] =
                [
                    'url'=>wp_get_attachment_url($item),
                    'alt'=>$this->title,
                ];
        }
        return $result;

    }

    protected function set_image($id):?array
    {

        $src = wp_get_attachment_image_src(
            get_post_thumbnail_id( $id),
            'single-post-thumbnail'
        );
        if(false === $src)
        {
            $gallery =$this->set_gallery();
            if(!$gallery) return null;
            return $gallery[0];
        }
        return
            [
                'url'=>$src[0],
                'alt'=>$this->product->get_name()
            ];

    }

    public function set_description(int $id):?string
    {
        return get_the_content(null,null,$id);
    }

    public function set_cross_sell():?array
    {
        $product= $this->product;
        if(!$product->get_cross_sell_ids()) return null;
        $result = [];
        foreach ($product->get_cross_sell_ids() as $item)
        {
            $result[] = new $this($item);
        }

        return $result;
    }

    public function set_upsell():?array
    {
        $product = $this->product;
        if(!$product->get_cross_sell_ids()) return null;
        $result = [];
        foreach ($this->product->get_upsell_ids() as $item)
        {
            $result[] = new $this($item);
        }
        return $result;
    }

    private function set_short_description():?string
    {
        return $this->product->get_data()['short_description'];
    }

    public function set_attributes():?array
    {
        return $this->product->get_attributes();
    }


    /**
     * ---------------------[ GETTERS ]------------------------
     */
    public function get_id():?int
    {
        return $this->id;
    }

    public function get_title():string
    {
        return $this->title;
    }

    public function get_link(): string
    {
        return $this->link;
    }

    public function get_article():?string
    {
        return $this->article;
    }

    public function get_price():?string
    {

        return 0===$this->get_sale_price() ?
            $this->get_regular_price():
            $this->get_sale_price();
    }

    public function get_currency():?string
    {
        return $this->currency;
    }

    public function check_sale():bool
    {
        return !empty($this->get_sale_price());
    }

    public function get_regular_price():int
    {
        return (int) $this->product->get_regular_price();
    }

    public function get_sale_price():int
    {
        return (int) $this->product->get_sale_price();
    }

    public function get_discount():?int
    {
        if(!$this->get_sale_price())
            return null;
        return $this->get_regular_price() - $this->get_sale_price();
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

    public function get_gallery():?array
    {
        return $this->gallery;
    }

    public function get_content():?string
    {
        return $this->description;
    }

    public function get_cross_sell():?array
    {
        return $this->cross_sell;
    }

    public function get_upsell():?array
    {
        return $this->upsell;
    }

    public function get_description():?string
    {

        return $this->description;
    }

    public function get_short_description():?string
    {

        return $this->short_description;
    }

    public function get_attributes($count=0):?array
    {
        $result = [];

        foreach ($this->attributes as $attribute)
        {
            $result[]=
                [
                    'title'=>wc_attribute_label($attribute['name']),
                    'value'=>$this->product->get_attribute($attribute['name'])
                ];
        }

        return $count===0?$result:array_slice($result,0,$count);
    }


    public function get_new():bool
    {
        return $this->new;
    }



    /**
     * ---------------------[ UPDATE ]------------------------
     */
    private function update_meta(string $key,mixed $value):int|bool
    {
        return update_post_meta( $this->id, $key, $value );
    }
    private function update_acf(string|int|null $key=null,mixed $value = null)
    {

        if(null !== $value || null !== $key)
        {
            return update_field($key, $value, $this->id);

        }
        return false;
    }
    public function update_visible($value):int|bool
    {
        if(null === $value)
            return false;
        return $this->update_meta('_visibility',$value);
    }
    public function update_stock_status($value):int|bool
    {
        if(null === $value)
            return false;
        return $this->update_meta('_stock_status',$value);
    }
    public function update_regular_price($value):int|bool
    {
        if(null === $value)
            return false;

        return
            $this->update_meta('_regular_price', (float) $value)
            and
            $this->update_meta('__price',(float) $value);
    }
    public function update_price($value):int|bool
    {
        if(null === $value)
            return false;
        return $this->update_meta('_price',$value);
    }
    public function update_sale_price($value):int|bool
    {
        if(null === $value)
            return false;
        return $this->update_meta('_sale_price',$value);
    }
    public function update_article($value):int|bool
    {
        if(null === $value)
            return false;
        return $this->update_meta('_sku',$value);
    }
    public function update_attributes(array $attributes):int|bool
    {
        if(null === $attributes)
            return false;

        $_attributes = get_post_meta( $this->id, '_product_attributes' );
        foreach ($attributes as $key=>$item)
        {
            $_attributes[ sanitize_title( $key ) ] = array(
                'name'          => wc_clean( $key ),
                'value'         => $item,

                'is_visible'    => true, // this is the one you wanted, set to true
                'is_variation'  => false, // set to true if it will be used for variations
                'is_taxonomy'   => false // set to true
            );
        }
        return update_post_meta( $this->id, '_product_attributes', $_attributes );

    }
    public function update_weight($value):int|bool
    {
        if(null === $value)
            return false;
        return $this->update_meta('_weight',$value);
    }
    public function update_length($value):int|bool
    {
        if(null === $value)
            return false;
        return $this->update_meta('_length',$value);
    }
    public function update_width($value):int|bool
    {
        if(null === $value)
            return false;
        return $this->update_meta('_width',$value);
    }
    public function update_height($value):int|bool
    {
        if(null === $value)
            return false;
        return $this->update_meta('_height',$value);
    }
    public function update_description(string $description=null)
    {

        if(null !== $description)
            wp_update_post( array('ID' => $this->id, 'post_excerpt' => $description ) );
    }
    public function update_acf_matrix(string $value):bool
    {
        return $this->update_acf(self::$selectors['articles']['matrix'],$value);
    }
    public function update_attachment(string $url,string|null $description = null):?PostAttachment
    {

        $id = media_sideload_image(
            $url,
            $this->get_id(),
            $description,
            'id' );
        $this->attachment = $this->set_attachment($this->product);

        if(null ===  $this->attachment)
        {
            $this->product->set_image_id($id);
            $this->attachment = $this->set_attachment($this->product);
        }

        $this->product->save();
        return  new PostAttachment($id);
    }
    public function update_term(array|string $tags,bool $append=false):array
    {
        $result = wp_set_post_terms( $this->id, $tags, self::$taxClass::$taxType,$append);
        if('object' === gettype($result) && 'WP_Error' === get_class($result))
            throw new \Exception($result->get_error_message().' '.$this->title);
        $arr = [];

        foreach ($result as $item)
        {

            $arr[]= new self::$taxClass($item);
        }

        return $arr;
    }
    public function update(string $title = null,
                           string $article = null,
                           float $price = null,
                           string $description =null,
                           float $weight = null,

                           array $attributes =null,
                           array $acf = null)
    {
        $data  =
            [
                'ID'=>$this->id,
                'post_title'=>$title
            ];
        $result = wp_update_post( $data, true, true);

        if('object' === gettype($result) && 'WP_Error' === get_class($result))
            throw new \Exception($result->get_error_message().' '.$title);

        $this->title = $this->set_title($this->id);
        $this->update_article($article);
        $this->update_regular_price($price);
        $this->update_description($description);
        $this->update_weight($weight);
        $this->update_attributes($attributes);

        if('array' === gettype($acf) and true === key_exists(self::$selectors['articles']['matrix'],$acf))
        {
            $this->update_acf_matrix($acf[self::$selectors['articles']['matrix']]);
        }
    }


    /**
     * ---------------------[ STATIC METHODS ]------------------------
     */

    public static function get_product_by_article($article):?PostProduct
    {
        $id = wc_get_product_id_by_sku($article);
        if($id === 0)
            return null;

        return new self($id);
    }

    public static function get_product_by_article_matrix($article):?PostProduct
    {
        $args = array(
            'post_type'   => self::$postType,
            'hide_empty' => false,
            'meta_query' => array(
                array(
                    'key'       => self::$selectors['articles']['matrix'],
                    'value'     => $article,
                    'compare'   => '='
                )
            )
        );
        $result = get_posts($args);

        return []===$result?null:new self(get_posts($args)[0]->ID);

    }

    public static function create(
        string $title,
        string $article,
        float $price,
        string $description =null,
        float $weight = null,

        array $attributes =null,
        array $acf = null):PostProduct
    {
        $post_id = wp_insert_post(
            [
                'post_title' => $title,
                'post_type' => self::$postType,
                'post_status' => 'publish'
            ]);

        wp_set_object_terms( $post_id, 'simple', 'product_type' );
        update_post_meta( $post_id, '_visibility', 'visible' );

        $product = new self($post_id);
        $product->update_article($article);
        $product->update_regular_price($price);
        $product->update_description($description);
        $product->update_weight($weight);
        $product->update_attributes($attributes);

        if('array' === gettype($acf) and true === key_exists(self::$selectors['articles']['matrix'],$acf))
        {
            $product->update_acf_matrix($acf[self::$selectors['articles']['matrix']]);
        }


        return $product;
    }

    public static function existence_product_by_article_matrix(string|int $article):bool
    {
        $args = array(
            'post_type'   => self::$postType,
            'hide_empty' => false,
            'meta_query' => array(
                array(
                    'key'       => self::$selectors['articles']['matrix'],
                    'value'     => $article,
                    'compare'   => '='
                )
            )
        );

        return count(get_posts($args))>0;
    }

    public static function delete_all(int|null $numberposts=-1)
    {
        $allposts= get_posts( array('post_type'=>self::$postType,'numberposts'=>-$numberposts) );
        foreach ($allposts as $eachpost)
        {
            wp_delete_post( $eachpost->ID, true );
        }
    }

    public static function get_taxonomy_posts($tax_id,array $filters=null):?Array
    {
        global $wp_query;
        $input =[];
            query_posts(
            [
                'post_type' => self::$postType,
                'posts_per_page' => get_option('posts_per_page'),
                'paged'         => Pagination::get_current_page(),
                'tax_query' =>
                    [
                        [
                            'taxonomy' => self::$taxClass::$taxType,
                            'field' => 'id',
                            'terms' => $tax_id
                        ]
                    ]
            ]
        );
        while ( $wp_query->have_posts() )
        {
            $wp_query->the_post();
            $input[]= $wp_query->post;

        }

        if (null===$filters)
            return self::convert_post($input,get_called_class());
        $result = [];

        if(key_exists('to',$filters['price']) and key_exists('from',$filters['price']))
        {

            if(key_exists('from',$filters['price'])
                and
                $filters['price']['from'] !== '0'
            and
                !empty($filters['price']['from']))
            {

                foreach ($input as $_product)
                {
                    $product = wc_get_product( $_product->ID );

                    if($product->get_price()>=$filters['price']['from']) $result[]=$product;
                }
            }else
                {
                    $result = $input;
                }

            if(key_exists('to',$filters['price'])
                and
                $filters['price']['to'] !== '0'
            and
                !empty($filters['price']['to']))
            {
                dump(2);
                foreach ($result as $key=>$_product)
                {

                    if('integer'===gettype($_product))
                    {
                        $product = wc_get_product($_product);
                    }elseif('object'===gettype($_product))
                        {
                            $product = "WC_Product_Simple"===get_class($_product)
                             ?
                            $_product
                            :
                            wc_get_product( $_product->ID );
                        }else
                            {
                                throw new \Exception('undenfined type product '.$_product);
                            }


                    if($product->get_price()>$filters['price']['to'])
                        unset($result[$key]);
                }
            }

        }else
            {
                $result = $input;
            }

        if(key_exists('sorting',$filters)
            and
            (!empty($filters['sorting']['price'])
            or
            !empty($filters['sorting']['title']))
        )
        {

            if('abc' === $filters['sorting']['title'])
            {
                usort($result, function ($a, $b)
                {
                    return strcmp($a->post_title, $b->post_title);
                });
            }

            if('cba' === $filters['sorting']['title'])
            {
                usort($result, function ($a, $b)
                {
                    return strcmp($a->post_title, $b->post_title);
                });
                $result = array_reverse($result);

            }

            if('abc' === $filters['sorting']['price'])

                usort($result, function ($a, $b)
                {
                    $a_product =  wc_get_product( $a->ID );
                    $b_product = wc_get_product( $b->ID );

                    $result = 0;
                    if ($a_product->get_price() > $b_product->get_price()) {
                        $result = 1;
                    } else if ($a_product->get_price() < $b_product->get_price())
                    {
                        $result = -1;
                    }
                    return $result;
                });

            if('cba' === $filters['sorting']['price'])
            {


                usort($result, function ($a, $b)
                {
                    $a_product =  wc_get_product( $a->ID );
                    $b_product = wc_get_product( $b->ID );

                    $result = 0;
                    if ($a_product->get_price() < $b_product->get_price()) {
                        $result = 1;
                    } else if ($a_product->get_price() > $b_product->get_price())
                        {
                            $result = -1;
                        }
                    return $result;
                });
            }


        }

        return self::convert_post($result,get_called_class());
    }

    public static function init(int $ID):self
    {
        $productClass = new self($ID);

        $productClass->gallery              = $productClass->set_gallery();

        $productClass->description          = $productClass->set_description($productClass->get_id());

        $productClass->attributes           = $productClass->set_attributes();

        $productClass->cross_sell           = $productClass->set_cross_sell();

        $productClass->upsell               = $productClass->set_upsell();

        $productClass->new                  = $productClass->set_new($productClass->get_id());

       return $productClass;
    }
}