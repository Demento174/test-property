<?php

namespace Classes\AdminMenu;
/**
 * Класс адаптер для создания подменю
 */
class SubMenu extends MenuParent
{
    private $menu_parent = null;

    public function __construct(Menu $menu_parent,string $page_title, string $menu_title,string $capability,   $function,  int $position = null, string $menu_slug = null)
    {
        $this->menu_parent = $this->set_menu_parent($menu_parent);

        parent::__construct($page_title, $menu_title, $capability, $menu_slug, $function, null, $position);
    }

    private function set_menu_parent(Menu $menuParent)
    {
        return $menuParent->get_menu_slug();
    }


    public function handler()
    {

        add_submenu_page(
            $this->menu_parent,$this->page_title, $this->menu_title,$this->capability, $this->menu_slug,   $this->function, $this->position);
    }
}