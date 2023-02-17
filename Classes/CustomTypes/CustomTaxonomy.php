<?php
namespace Classes\CustomTypes;

use Classes\Traits\Settings;
/**
 * Регистрация кастомных Таксономи
 * Настройки в файле Settings.php
 */
class CustomTaxonomy{

    private $slug;
    private $postSlug;
    private $labels;
    private $args;
    use Settings;

    public function __construct($settings=null)
    {
        $this->settings_init($settings,__DIR__.'/settings.php');

        if(array_key_exists('taxonomy',$this->settings) && null !== $this->settings['taxonomy'])
            add_action('init', [$this,'handler']);

        // Отключение slug таксономии в URL
        add_filter('request', [$this,'handler_request'], 1, 1 );
        add_filter('term_link', [$this,'handler_term_link'], 10, 3 );
        add_action( 'init', [$this,'handler_init'] );

    }

    private function set_labels($labels)
    {
        $this->labels = array(
            'name'              => $labels['name'] ,
            'singular_name'     => $labels['singular_name'],
            'search_items'      => $labels['search_items'],
            'all_items'         => $labels['all_items'],
            'parent_item'       => $labels['parent_item'],
            'parent_item_colon' => $labels['parent_item_colon'],
            'edit_item'         => $labels['edit_item'],
            'update_item'       => $labels['update_item'],
            'add_new_item'      => $labels['add_new_item'],
            'new_item_name'     => $labels['new_item_name'],
            'menu_name'         => $labels['menu_name'],
        );
    }

    private function set_args($args)
    {
        $this->args = $args;
        $this->args['labels'] = $this->labels;
    }


    private function register_post_type()
    {
        register_taxonomy($this->slug, $this->postSlug , $this->args);
    }

    public function handler_init()
    {

        //Здесь зарыт костыль, но так как задание тестовое откапывать его не было времени
        foreach (\Classes\PostsAndTax\Taxonomy\TaxonomyProperty::$IDS as $id)
        {
            $slug = get_term( $id )->slug;
            add_rewrite_rule(
                '^'.$slug.'/page/([0-9]+)/?$',
                'index.php?taxonomy_property='.$slug.'&paged=$matches[1]',
                'top'
            );
            add_rewrite_rule(
                '^'.$slug.'/?$',
                'index.php?taxonomy_property='.$slug,
                'top'
            );

            add_rewrite_rule(
                '^'.$slug.'/([^/]*)/page/([0-9]+)/?$',
                'index.php?taxonomy_property=$matches[1]&paged=$matches[2]',
                'top'
            );

            add_rewrite_rule(
                '^'.$slug.'/([^/]*)/?$',
                'index.php?taxonomy_property=$matches[1]',
                'top'
            );


            add_rewrite_rule(
                '^'.$slug.'/([^/]*)/([^/]*)/?$',
                'index.php?taxonomy_property=$matches[2]',
                'top'
            );
            add_rewrite_rule(
                '^'.$slug.'/([^/]*)/([^/]*)/page/([0-9]+)/?$',
                'index.php?taxonomy_property=$matches[2]&paged=$matches[3]',
                'top'
            );
        }
    }

    public function handler_request($query)
    {


        if(is_admin())
            return $query;

        $tax_name = 'taxonomy_property';

        if( key_exists('taxonomy_property',$query))
        {
            $posts = get_posts(array(
                'name' => $query['taxonomy_property'],
                'posts_per_page' => 1,
                'post_type' => 'property',
                'post_status' => 'publish'
            ));

            if($posts)
            {


                return [
                    "page" => "",
                    "property" => $posts[0]->post_name,
                    "post_type" => "property",
                    "name" => $posts[0]->post_name,
                ];

            }

        }



        if( $query['attachment'] ) :
            $include_children = true;
            $name = $query['attachment'];
        else:
            $include_children = false;
            $name = $query['name'];
        endif;


        $term = get_term_by('slug', $name, $tax_name);

        if (isset($name) && $term && !is_wp_error($term)):

            if( $include_children ) {
                unset($query['attachment']);
                $parent = $term->parent;
                while( $parent ) {
                    $parent_term = get_term( $parent, $tax_name);
                    $name = $parent_term->slug . '/' . $name;
                    $parent = $parent_term->parent;
                }
            } else {
                unset($query['name']);
            }

            switch( $tax_name ):
                case 'category':{
                    $query['category_name'] = $name;
                    break;
                }
                case 'post_tag':{
                    $query['tag'] = $name;
                    break;
                }
                default:{
                    $query[$tax_name] = $name;
                    break;
                }
            endswitch;

        endif;


        return $query;
    }

    public function handler_term_link($url, $term, $taxonomy )
    {
        $taxonomy_name = 'taxonomy_property';
        $taxonomy_slug = 'taxonomy_property';

        if ( strpos($url, $taxonomy_slug) === FALSE || $taxonomy != $taxonomy_name )
            return $url;

        $url = str_replace('/' . $taxonomy_slug, '', $url);

        return $url;
    }

    public function handler()
    {
        if(null === $this->settings['taxonomy'])
            return;


        foreach ($this->settings['taxonomy'] as $item)
        {
            $this->slug = $item['slug'];
            $this->postSlug = $item['postSlug'];
            $this->set_labels($item['labels']);
            $this->set_args($item['args']);
            $this->register_post_type($this->slug,$this->args);
        }
    }
}