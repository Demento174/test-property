<?php

namespace Classes\Blocks;

use Classes\Interfaces\RenderInterface;
use Classes\PostsAndTax\Posts\PostProperty;
use Classes\PostsAndTax\Posts\Query;
use Classes\PostsAndTax\Taxonomy\TaxonomyProperty;

class BlockCarousel2 extends BlockTwig implements RenderInterface
{
    public function __construct(bool $debug=false,callable|null $debug_function=null)
    {
        parent::__construct('blocks/sliders/slider2',null,$debug,$debug_function);
    }

    protected function set_data(array|null $input):null|array
    {
        $data = [];
        $buying = get_field('slider1',get_option('page_on_front'));
        $rent = get_field('slider2',get_option('page_on_front'));
        if($buying_items = TaxonomyProperty::query_buy($buying['count']))
            $data[]=array_merge($buying,['items'=>$buying_items]);

        if($rent_items = TaxonomyProperty::query_rent($rent['count']))
            $data[]=array_merge($rent,['items'=>$rent_items]);

        usort($data, function($a, $b)
        {
            if($a['check']  === $b['check'])
                return 0;
            return  $a['check']===true?-1:1;
        });


        return $data;
    }



    function render(): void
    {
        $arr = $this->data;
        foreach ($arr as $data)
        {

            $this->data = $data;
            parent::render();
        }

    }
}