<?php

namespace Classes\Traits;

trait CheckStaticVariables
{
    private static function check_static_variables($called_class):bool
    {
        foreach (self::$required_variables as $variable)
            if(!property_exists($called_class,$variable))
                return false;

        return true;
    }
}