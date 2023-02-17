<?php
namespace Classes\CustomTypes;

use Classes\Traits\Settings;

/**
 * Регистрация кастомных типов записей
 * Настройки в файле Settings.php
 */
class CustomPostType{

    private $slug;
    private $labels;
    private $args;
    use Settings;

    public function __construct($settings=null)
    {
        $this->settings_init($settings,__DIR__.'/settings.php');

        if($this->settings)
            add_action('init',[ $this,'handler']);

        add_action( 'init', [$this,'handler_init'] );
    }

    private function set_labels($labels)
    {
        $this->labels = array(
            'name'                  => _x( $labels['name'], $this->slug ),
            'singular_name'         => _x( $labels['singular_name'], $this->slug ),
            'add_new'               => _x( $labels['add_new'], $this->slug ),
            'add_new_item'          => _x( $labels['add_new_item'], $this->slug ),
            'edit_item'             => _x( $labels['edit_item'], $this->slug ),
            'new_item'              => _x( $labels['new_item'], $this->slug ),
            'view_item'             => _x( $labels['view_item'], $this->slug ),
            'search_items'          => _x( $labels['search_items'], $this->slug ),
            'not_found'             => _x( $labels['not_found'], $this->slug ),
            'not_found_in_trash'    => _x($labels['not_found_in_trash'], $this->slug ),
            'parent_item_colon'     => _x( $labels['parent_item_colon'], $this->slug ),
            'menu_name'             => _x( $labels['menu_name'], $this->slug ),
        );
    }

    private function set_args($args)
    {
        $this->args = $args;
        $this->args['labels'] = $this->labels;
    }


    private function register_post_type($slug,$args)
    {
        register_post_type( $slug,$args );
    }

    public function handler_init()
    {
        //Здесь зарыт костыль, но так как задание тестовое откапывать его не было времени
        foreach (\Classes\PostsAndTax\Taxonomy\TaxonomyProperty::$IDS as $id)
        {
            $slug = get_term( $id )->slug;
            add_rewrite_rule(
                '^'.$slug.'/([^/]*)/?$',
                'index.php',
                'top'
            );
//            ?property=$matches[1]
        }
    }

    public function handler()
    {
        foreach ($this->settings['posts'] as $item)
        {
            $this->slug = $item['slug'];
            $this->set_labels($item['labels']);
            $this->set_args($item['args']);
            $this->register_post_type($this->slug,$this->args);
        }
    }

    public static function get_post_types_slugs():array
    {
        $settings = require 'settings.php';
        $result = [];
        foreach ($settings as $item)
        {
            $result[]=$item[0]['slug'];
        }
        return $result;
    }
}