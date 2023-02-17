<?php


namespace Classes\Traits;

/**
 * Класс подключающий функционал включение настроек из файла
 */
Trait Settings
{
    protected $settings;
    protected $file;
    private $errors =
        [
            'empty' => 'Input data is null and file is null',
            'file'  => 'File not found',
            'type'  => 'returns type settings is not array'
        ];



    public function  settings_init($input=null,$file=null):void
    {
        if (empty($input) && empty($file))
            throw new \Exception($this->errors['empty']);

        if(empty($input))
        {
            $this->set_file($file);
            $data = $this->file;
        }else
            {
                $data = $input;
            }

        if($data === true)
            \Classes\Debugger\Debugger::debug($this->file);

        $this->set_settings($data);
    }


    private function set_file($path)
    {
        if(!file_exists($path))
            throw new \Exception($this->errors['file'].' '.$path);

        $this->file = require $path;
    }

    private function set_settings($data)
    {

        if (gettype($data) !== 'array')
            throw new \Exception($this->errors['type']);

        $this->settings = $data;
    }



}