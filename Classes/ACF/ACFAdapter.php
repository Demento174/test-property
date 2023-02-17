<?php
namespace Classes\ACF;

use Classes\Traits\CheckPlugin;
use Classes\Traits\Settings;

class ACFAdapter{
    private static string $plugin_name =  'advanced-custom-fields/acf.php';

    use CheckPlugin;
    use Settings;

    public function __construct($settings=null)
    {
        $this->set_plugin(self::$plugin_name);
        if(false === $this->checkPlugin())
            throw new \Exception('Plugin advanced-custom-fields-pro/acf.php is not activate');

        $this->settings_init($settings,__DIR__.'/settings.php');
        $this->handler();
    }


    private function add_optionsPage($title,$slug,$position,$icon,$id)
    {
        $args = array(

            'page_title'        => $title,

            'menu_title'        => $title,

            'menu_slug'         => $slug,

            'position'          => $position,

            'parent_slug'       => '',

            'icon_url'          => $icon,

            'redirect'          => true,

            'post_id'           => $id,

            'autoload'          => false,

            'update_button'		=> __('Update', 'acf'),

            'updated_message'	=> __("Options Updated", 'acf')
        );

        acf_add_options_page( $args );
    }

    private function add_optionsSubPage($id,$title,$slug,$parentSlug)
    {
        $args = array(

            'post_id'       => $id,

            'page_title'    => $title,

            'menu_title'    => $title,

            'menu_slug'     => $slug,

            'parent_slug'   => $parentSlug,


        );

        acf_add_options_sub_page( $args );
    }

    private function handler()
    {
        if(true === function_exists('acf_add_options_page'))
        {
            if($this->settings['optionsPage'])
                foreach ($this->settings['optionsPage'] as $item)
                    $this->add_optionsPage($item['title'],$item['slug'],$item['position'],$item['icon'],$item['id']);

            if($this->settings['optionsSubPage'])
                foreach ($this->settings['optionsSubPage'] as $item)
                    $this->add_optionsSubPage($item['id'],$item['title'],$item['slug'],$item['parent_slug']);
        }

    }
}