<?php

namespace Controllers\DisableContentEditor;

class DisableContentEditorController{

    private $settings;
    private $id;
    private $postType;


    public function __construct($settings=null)
    {
        if(!is_admin())
        {
            return;
        }

        $this->settings = !$settings?require_once('settings.php'):$settings;

        $this->set_id();
        
        if($this->id)
        {

            add_action( 'admin_init', [$this,'handler'] );
        }


    }


    private function set_id()
    {
        if(isset($_GET['post']))
        {
           $this->id =  $_GET['post'];
        }elseif(isset($_POST['post_ID']))
            {
                $this->id =  $_POST['post_ID'];
            }else
                {
                    $this->id = null;
                }

    }

    private function set_postType()
    {
        $post = get_post($this->id);

        $this->postType = $post->post_type;
    }


    public function handler()
    {

        if(array_search($this->id,$this->settings['exception']) !== false)
        {
            return;
        }

        $this->set_postType();
       
        remove_post_type_support( $this->postType, 'editor' );
    }
}