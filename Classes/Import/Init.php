<?php

namespace Classes\Import;

//use Controllers\Import\Suppliers\Matrix;

class Init
{
    public function __construct()
    {
        $this->add_menu();
    }


    private function add_menu()
    {
        $menu = new \Classes\AdminMenu\Menu('Импорт объектов из фида','Импорт объектов','administrator',[$this,'handler'],'',2);

        new \Classes\AdminMenu\SubMenu($menu,'Matrix','Matrix','administrator',[$this,'handler']);
    }

    public function handler()
    {
        echo 'Текст заглушка';

    }

}