<?php

namespace Controllers\Widgets\Widgets;

abstract class WidgetAbstract
{
    const TITLE     = null;
    const NAME      = null;
    const SELECTOR  = null;
    const NAMESPACE = null;

    private string|null $template = null;

    public function __construct(string $template=null)
    {
        $this->template = $template;
    }

    abstract public function get_title():string;

    abstract public function get_name():string;

    abstract public function get_namespace():?string;

    abstract public function get_selectors():?array;

    public function render()
    {
        renderAcfBlock($this->template,$this->get_selectors(),false,$debug=false);
    }
}

