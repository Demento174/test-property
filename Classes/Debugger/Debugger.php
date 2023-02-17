<?php

namespace Classes\Debugger;
/**
 * Класс обертка для дебагинга
 */
class Debugger{

    static $plugin_class_name = 'Symfony\Component\ErrorHandler\Debug';

    public function __construct()
    {
        if($this->check_plugin())
            $this->handler();

    }

    private function check_plugin()
    {
        return class_exists(self::$plugin_class_name);
    }

    private function handler()
    {
        /**
         * Dump variable.
         */
        if ( ! function_exists('d') ) {

            function d()
            {
                call_user_func_array( 'dump' , func_get_args() );
            }

        }

        /**
         * Dump variables and die.
         */
        if ( ! function_exists('dd') )
        {

            function dd()
            {
                call_user_func_array( 'dump' , func_get_args() );
                die();
            }

        }
    }

    public static function Index()
    {
        $classController = new self();
    }

    public static function debug($data,callable|null $debug_function=null):void
    {

        if(null !== $debug_function and function_exists($debug_function))
            $debug_function($data);
        elseif(function_exists('dd'))
            dd($data);
        else {
            /*---------------------------[ START ]---------------------------*/
            echo '<pre class="debug" style="
                                    background-color: rgba(0,0,0,0.8);
                                    display: inline-block;
                                    border: 5px solid springgreen;
                                    color: white;
                                    padding: 1rem;">';

            print_r($data);
            echo '</pre>';
            die;
            /*---------------------------[ END ]---------------------------*/
        }
    }


}