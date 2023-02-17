<?php

namespace Controllers\MicroMarkup\Types;

abstract class MicroMarkupAbstract
{
    private static $CONTEXT =
        [
            'key'=>'@context',
            'value'=>'https://schema.org',
        ];

    private static $_TYPE =
        [
            'key'=>'@type'
        ];
    protected $data = null;
    protected $type = null;
    protected function __construct()
    {
        $this->type = $this->set_type();
        $this->data =$this->set_data();
        $this->add_data();
    }





    protected function set_data():array
    {

        if($this->type === null)
        {
            throw  new \Exception('microdata type not declared');
        }
        $result =
            [
                self::$CONTEXT['key']=>self::$CONTEXT['value'],
                self::$_TYPE['key']=>$this->type
            ];
        return $result;
    }



//    protected abstract function set_type():string;
    protected abstract function add_data();
    public abstract function get_data():array;


}