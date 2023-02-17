<?php

namespace Classes\DisableContentEditor;

use Classes\Traits\Settings;

class DisableContentEditor{


    private int|null $id;
    private string $postType;
    use Settings;

    public function __construct($settings=null)
    {
        if(!is_admin())
            return;


        $this->settings_init($settings,__DIR__.'/settings.php');


        $this->id= $this->set_id();

        if($this->id)
            add_action( 'admin_init', [$this,'handler'] );


    }


    private function set_id():int|null
    {

        if(isset($_GET['post']) and 'integer'===gettype((int)$_GET['post']))
            return $_GET['post'];

        elseif(isset($_POST['post_ID']) and 'integer'===gettype((int)$_POST['post_ID']))
            return $_POST['post_ID'];

        return null;

    }

    private function set_postType():string
    {
        $post = get_post($this->id);

        return $post->post_type;
    }


    public function handler()
    {

        if(array_search($this->id,$this->settings['exception']) !== false)
            return;

        $this->postType = $this->set_postType();
       
        remove_post_type_support( $this->postType, 'editor' );
    }
}