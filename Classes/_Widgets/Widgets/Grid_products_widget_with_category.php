<?php

namespace Controllers\Widgets\Widgets;

use Controllers\PostsAndTax\TaxCat;


class Grid_products_widget_with_category  extends WidgetAbstract implements WidgetInterface
{
    const TITLE='Сетка продуктов с категориями';
    const NAME='grid-products-with-categories';
    const SELECTOR='grid_products_with_categories';
    const NAMESPACE='Grids';


    private $fields =
        [
            'title' => null,
            'subtitle' => null,
            'categories' => null,
            'count_products' => null,
            'products' =>null
        ];

    private string|null $template = null;

    public function set_title($acf=null):?string
    {
        return null===$acf?null:$acf['title'];
    }

    private function set_subtitle($acf = null):?string
    {
        return null===$acf?null:$acf['subtitle'];
    }

    private function set_categories($acf):array
    {
        if(null === $acf)return [];

        return true=== $acf['categories']['all']?
            TaxCat::get_all():
            TaxCat::get_include_terms($acf['categories']['items']);

    }

    private function set_count_products($acf=null):int
    {

        return null===$acf?0:$acf['count'];
    }

    private function set_products_first_category(TaxCat $category)
    {
        return $category->get_posts_by_term();
    }

    public function set_products(array $products):array
    {
        return 0===$this->fields['count_products']?
            $products:
            array_slice($products,0,$this->fields['count_products']);
    }


    public function get_title():string
    {
        return self::TITLE;
    }

    public function get_name():string
    {
        return self::NAME;
    }

    public function get_namespace():string
    {
        return self::NAMESPACE;
    }

    public function get_selectors():array
    {
        return $this->fields;
    }

    public function render()
    {
        $acf = get_field(self::SELECTOR);

        $this->fields['title']=$this->set_title($acf);
        $this->fields['subtitle']=$this->set_subtitle($acf);
        $this->fields['categories']=$this->set_categories($acf);
        $this->fields['count_products']=$this->set_count_products($acf);


        $this->fields['products'] = $this->set_products($this->set_products_first_category($this->fields['categories'][0]));
        parent::render();
    }


}