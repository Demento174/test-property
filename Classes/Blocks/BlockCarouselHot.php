<?php

namespace Classes\Blocks;

use Classes\Interfaces\RenderInterface;
use Classes\PostsAndTax\Posts\PostProperty;
use Classes\PostsAndTax\Posts\Query;

class BlockCarouselHot extends BlockTwig implements RenderInterface
{
    public function __construct(bool $debug=false,callable|null $debug_function=null)
    {
        parent::__construct('blocks/sliders/hot',null,$debug,$debug_function);
    }

    protected function set_data(array|null $input):null|array
    {

        return get_field('slider_hot',get_option('page_on_front'));
    }

    function render(): void
    {
        if(true === $this->data['switch'] and (0 < count($_posts = PostProperty::hot_posts())))
        {

            $this->data['items'] = $_posts;
            parent::render();
        }
    }
}