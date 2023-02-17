<?php

namespace Controllers\MetaBox;


abstract class MetaBox
{
    protected $field_name = 'field_';
    protected $id = '';
    protected $posts = [];


    protected function __construct($id,$posts,$title)
    {
        $this->id = $id;
        $this->posts = $posts;
        $this->set_field_name($id);
        $this->add_meta_box($title);
        add_action( 'save_post', [$this,'save'] );
    }

    private function set_field_name($id)
    {
        $this->field_name.=$id;
    }

    public function add_meta_box($title)
    {

        add_action('add_meta_boxes',fn()=>add_meta_box( $this->id, $title,[$this,'callback'], $this->posts ));


    }

    public function save($post_id)
    {

        if ( ! isset( $_POST[$this->field_name] ) )
        {
            return;
        }

        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        {
            return;
        }


        update_post_meta( $post_id, $this->field_name, sanitize_text_field( $_POST[$this->field_name] ) );
    }

    abstract public function callback($post, $meta);

    abstract static function get_field($id,$params=[]);

}