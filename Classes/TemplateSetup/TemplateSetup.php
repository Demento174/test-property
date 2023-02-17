<?php

namespace Classes\TemplateSetup;

use Classes\Traits\Single;

class TemplateSetup{
    use Single;

    private $settings;

    public function __construct($settings=null)
    {
        $this->settings = !$settings?require_once('settings.php'):$settings;
        $this->handler();
    }

    private function handler()
    {

        foreach ($this->settings["supports"] as $item)
        {
            add_theme_support( $item['title'], isset($item['options'])?$item['options']:null );
        }
        foreach ($this->settings["menu"] as $key=>$item)
        {
            add_action( 'init', fn()=>register_nav_menu($key,$item));
        }

        if($this->settings['other']['svg'])
                add_filter( 'upload_mimes',function ($mimes)
                {
                    $mimes['svg']  = 'image/svg+xml';

                    return $mimes;
                } );



    }
}