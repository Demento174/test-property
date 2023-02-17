<?php

namespace Classes\DisableAdminMenu;

use Classes\Traits\Settings;

/**
 * Класс адаптер отключения пунктов меню в админ панели
 * Настройки в settings.php
 */
class DisableAdminMenu{

    use Settings;
    public function __construct($settings=null)
    {
        $this->settings_init(require_once __DIR__.'/settings.php',$settings);

        if(true === is_admin() and wp_get_current_user()->ID !== $this->settings['administrator'])
            add_action('admin_menu', [$this,'handler']);

    }

    public function handler()
    {
        foreach ($this->settings['pages'] as $item)
            remove_menu_page($item);
    }
}