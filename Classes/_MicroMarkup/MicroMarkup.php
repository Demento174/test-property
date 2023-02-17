<?php

namespace Controllers\MicroMarkup;
use Controllers\MicroMarkup\Types\MicroMarkupAbstract;
use Controllers\MicroMarkup\MicroMarkupFactory;

class MicroMarkup
{
    private static $tag = 'script';
    private static $type = 'application/ld+json';

    private $classType = null;
    private $script = null;

    public function __construct($queryObject)
    {
        $this->classType = $this->set_classType($queryObject);

        $this->script = $this->set_script($this->classType?$this->classType->get_data():null);
    }

    private function set_classType($queryObject):?MicroMarkupAbstract
    {
        return MicroMarkupFactory::index($queryObject);
    }

    private function set_script($data=null):string
    {
        if($data === null)
        {
            return  '';
        }
        $tag =  self::$tag;
        $type =  self::$type;
        $result = "<$tag type='$type'>";
        $result .= json_encode($data);
        $result .= "</$tag>";


        return $result;
    }

    public function get_script():string
    {
        return $this->script;
    }

    public static  function render($queryObject)
    {
        $classController = new self($queryObject);

        echo $classController->get_script();
    }
}