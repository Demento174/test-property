<?php

namespace Classes\AdminMenu;
/**
 * Класс Адаптер для создания Меню в административной части
 */
class Menu extends MenuParent
{


    public function __construct(
        string $page_title,
        string $menu_title,
        string $capability,
        $function = null,
        string $icon_url = null,
        int $position = null,
        string $menu_slug = null)
    {
        parent::__construct($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);
    }

    public function get_menu_slug()
    {
        return $this->menu_slug;
    }



    public function handler()
    {

        add_menu_page($this->page_title, $this->menu_title,$this->capability, $this->menu_slug,   $this->function, $this->icon_url, $this->position);
    }
}