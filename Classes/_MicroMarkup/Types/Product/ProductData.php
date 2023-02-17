<?php

namespace Controllers\MicroMarkup\Types\Product;

class ProductData
{
    static public $TYPE = 'Product';
    public  $additionalProperty = null;
    public $exifData = null;
    public $category = null;
    public $color = null;
    public $weight = null;
    public $width = null;
    public $depth = null;
    public $height = null;
    public $logo = null;
    public $description = null;
    public $name = null;
    public $image = null;
    public $offers = null;

    protected  function set_type():string
    {
        return self::$TYPE;
    }

    /***
     * @param $properties [key=>is title property],except $color $weight $width $depth $height;
     * @return array|null
     */
    public function set_additionalProperty(array $properties):?array
    {
        $result = [];
        foreach ($properties as $key=>$item)
        {
            if(array_search($key,['color' ,'weight' ,'width' ,'depth' ,'height']) !== false)
            {
                continue;
            }
            $result[]=
                    [
                        "@type"=> "PropertyValue",
                        "name"=> $key,
                        "value"=> $item
                    ];
        }
        return $result;
    }

    /**
     * @param $breadcrumbs , delimiter /
     * @return string|null
     */
    public function set_category(array $breadcrumbs):?string
    {
        array_shift($breadcrumbs);
        array_pop($breadcrumbs);
        $str ='';

        foreach ($breadcrumbs as $item)
        {
            $str .= !next($breadcrumbs)?$item['text']:$item['text'].'/';
        }
        return $str;
    }

    /**
     * @param $color
     * @return string|null
     */
    public function set_color($color):?string
    {
        return '';
    }

    /**
     * @param $weight
     * @return int|null
     */
    public function set_weight($weight):?int
    {
        return 0;
    }

    /**
     * @param $width
     * @return int|null
     */
    public function set_width($width):?int
    {
        return 0;
    }

    /**
     * @param $depth
     * @return int|null
     */
    public function set_depth($depth):?int
    {
        return 0;
    }

    /**
     * @param $height
     * @return int|null
     */
    public function set_height($height):?int
    {
        return 0;
    }

    /**
     * @param $logo
     * @return string (url) |null
     */
    public function set_logo():?string
    {
        return get_template_directory_uri().'/img/rsu_logo.svg';
    }

    /**
     * @param $image
     * @return string (url) |null
     */
    public function set_image(int $ID):?string
    {
        return wp_get_attachment_url( get_post_thumbnail_id($ID), 'thumbnail' );
    }

    /**
     * @param $description
     * @return string (WISWYIW) |null
     */
    public function set_description($description):?string
    {
        return '';
    }

    /**
     * @param $name
     * @return string|null
     */
    public function set_name(int $ID):?string
    {
        return get_the_title($ID);
    }

    /**
     * @param $offers
     * @return array|null
     */
    public function set_offers($offers):?array
    {
        return [];
    }



}