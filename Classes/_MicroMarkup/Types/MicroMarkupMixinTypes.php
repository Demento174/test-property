<?php

namespace Controllers\MicroMarkup\Types;

class MicroMarkupMixinTypes extends MicroMarkupAbstract
{
    private $classes = null;
    public function __construct(array $classes=[MicroMarkupAbstract::class])
    {
        $this->classes = $classes;
        $this->type = $this->set_type();
        $this->add_data();
    }

    protected function set_type():string
    {
        return 'mixin';
    }
    protected  function add_data()
    {

        foreach ($this->classes as $class)
        {
            $this->data[]=$class->get_data();
        }
    }
    public function get_data():array
    {

        return $this->data;
    }

}