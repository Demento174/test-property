<?php

namespace Classes\Debugger;


/**
 * Класс кастомного вывода ошибок
 */
class ErrorHandler
{


    public function __construct()
    {


        set_error_handler([$this,"admin_alert_errors"],
            E_ERROR ^
            E_WARNING ^
            E_CORE_ERROR ^
            E_COMPILE_ERROR ^
            E_USER_ERROR ^
            E_RECOVERABLE_ERROR ^
            E_CORE_WARNING ^
            E_COMPILE_WARNING ^
            E_USER_WARNING ^
            E_NOTICE ^
            E_USER_NOTICE ^
            E_DEPRECATED    ^
            E_USER_DEPRECATED    ^
            E_PARSE ^
            E_STRICT);

        register_shutdown_function([$this,'shutdown_function']);

    }

    private function dump($message):void
    {
        \Classes\Debugger\Debugger::debug($message,'dump');
    }

    public function admin_alert_errors($errno, $errstr, $errfile, $errline)
    {
        if(WP_DEBUG === false)
            return;

        $errorType = array (
            E_ERROR                => 'ERROR',
            E_CORE_ERROR           => 'CORE ERROR',
            E_COMPILE_ERROR        => 'COMPILE ERROR',
            E_USER_ERROR           => 'USER ERROR',
            E_RECOVERABLE_ERROR    => 'RECOVERABLE ERROR',
            E_WARNING              => 'WARNING',
            E_CORE_WARNING         => 'CORE WARNING',
            E_COMPILE_WARNING      => 'COMPILE WARNING',
            E_USER_WARNING         => 'USER WARNING',
            E_NOTICE               => 'NOTICE',
            E_USER_NOTICE          => 'USER NOTICE',
            E_DEPRECATED           => 'DEPRECATED',
            E_USER_DEPRECATED      => 'USER_DEPRECATED',
            E_PARSE                => 'PARSING ERROR',
            E_STRICT               => 'E_STRICT',
            E_ALL                  => 'E_ALL'
        );

        $errname = array_key_exists($errno, $errorType)?$errorType[$errno]:'UNKNOWN ERROR';


        $this->dump(["$errname: $errno"=>"$errstr - $errfile  - on line $errline"]);
    }

    public function shutdown_function()
    {
        $error = error_get_last();
        if ($error['type'] === E_ERROR)
            $this->dump($error);
    }


}



