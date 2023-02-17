<?php

namespace Controllers\Widgets\Widgets;

class Grid_services_widget
    extends WidgetAbstract
    implements WidgetInterface
{
    const TITLE='Сетка услуг';
    const NAME='grid-services';
    const SELECTOR='grid_services';
    const NAMESPACE='Grids';


    private $fields =
        [
            'title' => null,
            'subtitle' => null,
            'services' =>null
        ];

    private string|null $template = null;

    private function set_title($acf,$common):?string
    {
        return $acf['switch_title']?$common['title']:$acf['title'];
    }

    private function set_subtitle($acf,$common):?string
    {
        return $acf['switch_subtitle']?$common['subtitle']:$acf['subtitle'];
    }

    private function set_services($acf,$common):?array
    {
        $items = $acf['switch_services']?$common['services']:$acf['services'];
        $result = [];
        foreach ($items as $key=>$service)
        {
            $result[]= new \Controllers\PostsAndTax\PostService($service);
        }

        return $result;
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
        $common = get_field('grid_services',ACF_PAGE_SLUG_COMMON);
        $this->fields['title']=$this->set_title($acf,$common);
        $this->fields['subtitle']=$this->set_subtitle($acf,$common);
        $this->fields['services'] = $this->set_services($acf,$common);

        parent::render();
    }


}