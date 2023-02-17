<?php

namespace Controllers\Widgets\Widgets;

class Form_row_widget
    extends WidgetAbstract
    implements WidgetInterface
{
    const TITLE       = 'Форма: телефон + сообщение';
    const NAME        = 'row';
    const NAMESPACE   = 'forms';

    private array|null $fields = null;

    private function set_fields()
    {
        return get_field('forms',ACF_PAGE_SLUG_COMMON)['row'];
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