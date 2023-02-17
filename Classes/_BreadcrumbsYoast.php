<?php
namespace Controllers;
use Controllers\Interfaces\InitInterface;
use Controllers\Interfaces\RenderInterface;

class BreadcrumbsYoast implements RenderInterface,InitInterface {
    private $classYoast         = null;
    private $links              = null;
    private $main_server_name   = null;
    private $server_name        = null;
    private $before             = null;
    private $after              = null;
    private $arrow              = null;

    public function __construct(string $server_name,string $before='',string $after='',string $arrow='')
    {
        $this->before = $before;
        $this->after=$after;
        $this->arrow=$arrow;

        $this->classYoast = $this->set_classYoast();

        $this->server_name = $server_name;

        $this->main_server_name = $this->set_main_server_name($this->server_name);//before set links

        $this->links = $this->set_links($this->classYoast);
    }

    public static function check()
    {
        return function_exists('yoast_breadcrumb');
    }

    private function set_classYoast()
    {
        return new \WPSEO_Breadcrumbs();
    }

    private function set_links($classYoast)
    {

        foreach ($classYoast->get_links() as $key=>$item)
        {

            $result[$key]=
                [
                    'text'=>'Home'===$item['text']?'Главная':$item['text'],
                    'id'=>key_exists('term_id',$item)?$item['term_id']:$item['id']
                ];

            if(strripos($item['url'],$this->main_server_name) !== false)
            {
                $protocol = strripos($item['url'],'https://') !== false?'https://':'http://';

                $convert = explode('/',str_replace($protocol,'',$item['url']));

                $convert[0] = $this->server_name;

                $result[$key]['url'] = $protocol.implode('/',$convert);

            }else
            {
                $result[$key]['url']=$item['url'];
            }
        }

        return $result;
    }

    private function set_main_server_name($server_name)
    {
        if(count(explode('.',$server_name))>2)
        {
            $arr = explode('.',$server_name);

            return implode('.',[$arr[1],$arr[2]]);
        }else
        {
            return $server_name;
        }
    }

    public function get_links()
    {
        return $this->links;
    }

    public function render()
    {

        renderBlock('Breadcrumbs',['data'=>['links'=>$this->get_links()]]);
    }

    public static function init($server_name=null,$before='<div id="breadcrumbs">',$after='</div>',$arrow='»')
    {
        if(!self::check())
        {
            return;
        }

        $server_name= !$server_name?$_SERVER['SERVER_NAME']:$server_name;

        $classController = new self($server_name,$before,$after,$arrow);
        $classController->render();
    }

    public static function DATA($server_name=null)
    {
        if(!self::check())
        {
            return;
        }
        $server_name= !$server_name?$_SERVER['SERVER_NAME']:$server_name;

        $classController = new self($server_name);

        return $classController->get_links();

    }

}

