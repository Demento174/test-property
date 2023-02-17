<?php
namespace Classes\LogsClass;
use \Classes\Settings as Settings;



class Logs
{

    private $directory;
    private $fileName;
    private $path;
    private $date;
    private $data;
    private $errors =
        [
            'settings'=>'Not fount in setting '
        ];
    use Settings;



    public function __construct($txt,$directory=null,$settings=null)
    {
        $this->settings_init($settings,__DIR__.'/settings.php');


        $this->set_directory($directory);
        $this->set_path($this->settings);
        $this->set_date();
        $this->set_fileName($this->settings);
        $this->set_data($txt);
    }


    private function set_directory($directory=null)
    {
        $this->directory = !$directory?'':$directory.'/';
    }

    private function set_path($settings)
    {
        if(!isset($settings['path']))
        {
            throw new \Exception($this->errors['settings'].'path');
        }

        if (!file_exists($settings['path']))
        {
            mkdir($settings['path'], 0777, true);
            return false;
        }

        $this->path = $settings['path'];
    }

    private function set_date()
    {

        $this->date =date("d-m-Y");
    }

    private function set_fileName($settings)
    {

        if(!isset($settings['fileName']))
        {
            throw new \Exception($this->errors['settings'].'file name');
        }
        $this->fileName = $this->date.'___'.$settings['fileName'].'.log';
    }

    private function set_data($data)
    {
        $this->data = is_array($data)?
            "\n". date("d-m-Y_H:i:s")." -- > ".print_r($data,TRUE):
            "\n". date("d-m-Y_H:i:s")." -- > ".$data;
    }

    private function check_directory()
    {
        if(!$this->directory)
            return true;

        $fullPath = $this->path.$this->directory;

        if (!file_exists($fullPath))
        {
            mkdir($fullPath, 0777, true);
            return false;
        }
        return true;
    }


    public function handler()
    {
        try{

            $this->check_directory();

            $fullPath = $this->path.$this->directory.$this->fileName;

            $fp = fopen($fullPath, "a"); // Открываем файл в режиме записи

            fwrite($fp,$this->data);

            fclose($fp); //Закрытие файла

        }catch (Exception $e) { return false; }
    }


    static function write($txt,$directory=null,$settings=null)
    {
        $classLog = new self($txt,$directory,$settings);

        $classLog->handler();

    }
}