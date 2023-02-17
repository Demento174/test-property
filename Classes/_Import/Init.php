<?php

namespace Controllers\Import;

use Controllers\Import\Suppliers\Matrix;

class Init
{
    public function __construct()
    {
        $this->add_menu();
    }


    private function add_menu()
    {
        $menu = new \Controllers\Menu\Menu('Импорт товаров','Импорт товаров','administrator',[$this,'handler'],'',2);
        new \Controllers\Menu\SubMenu($menu,'Matrix','Matrix','administrator',[$this,'handler']);
    }

    public function handler()
    {
        \Controllers\Import\Suppliers\Matrix::init();
    }
}