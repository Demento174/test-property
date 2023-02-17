<?php

namespace Classes\Traits;

trait Single
{
    public  $initClass = null;



    public static function init()
    {
        if(null === self::$initClass)
        {
            self::$initClass =  new self();
            return self::$initClass;
        } else
            return self::$initClass;
    }
}