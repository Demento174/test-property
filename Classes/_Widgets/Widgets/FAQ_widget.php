<?php

namespace Controllers\Widgets\Widgets;

class FAQ_widget
    extends WidgetAbstract
    implements WidgetInterface
{
    const TITLE       = 'FAQ';
    const NAME        = 'faq';
    const NAMESPACE   = 'Blocks';

    private array|null $fields = null;

    private function set_fields()
    {
        return get_field('faq',ACF_PAGE_SLUG_COMMON);
    }


    public function get_title():string
    {
        return self::TITLE;
    }
    public function get_name():string
    {
        return self::NAME;
    }
    public function get_namespace():?string
    {
        return self::NAMESPACE;
    }
    public function get_selectors():null|array
    {


        return $this->fields;
    }

    public function render()
    {
        $this->fields = $this->set_fields();
        parent::render();
    }

}