<?php
namespace Controllers\WC\UpdatingThumbnailsThroughTheLibrary;

use \Controllers\PostsAndTax\PostAttachment as PostAttachment;
use \Controllers\Settings as Settings;

class UpdatingThumbnailsThroughTheLibrary extends Settings
{
    private static $errors =
        [
            'plugin'=>'plugin is not enabled',
            'folder'=>'folder not found',
            'settings'=>'not found settings '
        ];

    private static $settings_file = __DIR__.'/settings.php';


    private $regex_thumbnail;
    private $regex_draw;
    private $regex_product_image;
    private $key_draw;

    private $folder;
    private $product;

    public function __construct($settings = null)
    {
        parent::__construct($settings,static::$settings_file);

        $this->check_plugin_mediaLibrary($this->settings);

        add_filter('add_attachment',[$this,'handler'],10,2);

    }

    private function set_regex_product_image($settings)
    {

        if(!$settings['regex_product_image'])
        {
            exit(logs(self::$errors['settings'].' regex_product_image',static::class));
        }
        $this->regex_product_image = $settings['regex_product_image'];

    }

    private function set_regex_thumbnail($settings)
    {
        if(!$settings['regex_thumbnail'])
        {
            exit(logs(self::$errors['settings'].'regex_thumbnail',static::class));
        }

        $this->regex_thumbnail = $settings['regex_thumbnail'];
    }

    private function set_regex_draw($settings)
    {
        if(!$settings['regex_draw'])
        {
            exit(logs(self::$errors['settings'].'regex_draw',static::class));
        }

        $this->regex_draw = $settings['regex_draw'];
    }

    private function set_folder($settings)
    {
        if(!$settings['folder'])
        {
            exit(logs(self::$errors['settings'].'folder',static::class));
        }
        $this->folder = $settings['folder'];
    }

    private function set_key_draw($settings)
    {
        if(!$settings['key_draw'])
        {
            exit(logs(self::$errors['settings'].'key_draw',static::class));
        }
        $this->key_draw = $settings['key_draw'];
    }


    private function set_product($article)
    {

        $this->product = \Controllers\PostsAndTax\PostProduct::get_product_by_article($article);
    }



    private function check_plugin_mediaLibrary($settings)
    {
        if(!$settings['plugin'])
        {
            exit(logs(self::$errors['settings'].'plugin',static::class));
        }
        $plugin = $settings['plugin'];

        if(!is_plugin_active( $plugin ))
        {
            throw new \Exception(static::$errors['plugin'].' '.$plugin);
        }
        return is_plugin_active( $plugin );
    }

    private function check_exist_image($number)
    {

        foreach ($this->product->get_images() as $item)
        {
            $attachment = new PostAttachment($item['id']);
            if((int) $this->get_number_image($this->product->get_article(),$attachment->get_title()) === (int) $number)
            {
                return $attachment->get_id();
            }
        }
        return true;

    }

    private function check_draw($number)
    {
        return trim((string)$number) === '0';
    }

    private function get_number_image($article,$title)
    {
        if(stripos($title,$article.'_')===false)
        {
            return false;
        }


        return str_replace($article.'_','',$title);
    }

    public function handler($attachmentID)
    {

        $attachment = new PostAttachment($attachmentID);

        $this->set_folder($this->settings);

        if ( ! file_is_displayable_image( $attachment->get_path() ) ||
            !checkAttachmentInFolder($attachment,$this->folder))
        {
            logs('here',static::class);
            return;
        }




        if($attachment->get_title())
        {

            $this->set_regex_product_image($this->settings);

            $this->set_regex_draw($this->settings);

            $this->set_regex_thumbnail($this->settings);

            $this->set_key_draw($this->settings);



            $matches = [];

            $preg_match =preg_match_all($this->regex_product_image,$attachment->get_title(),$matches);

            if(!$preg_match)
            {
                logs('file '.$attachment->get_title().' does not match regex '.$this->regex_product_image,static::class);
                return;
            }

            $this->set_product(str_replace('_','',$matches[0])[0]);

            if( !$this->product)
            {
                logs('Product with article '.str_replace('_','',$matches[0][0]).' not found',static::class);
                return;
            }


            $number_image = $this->get_number_image($this->product->get_article(),$attachment->get_title());

            if($number_image === false)
            {
                logs($attachment->get_title().' не правильное названи файла',static::class);
            }

            if($this->check_exist_image($number_image) !== true)
            {
                $old_attachment = new PostAttachment($this->check_exist_image($number_image));
                $old_attachment->delete();
            }

            $this->check_draw($number_image)?
                $this->update_draw($attachment->get_id()) :
                $this->update_product_image($attachment->get_id());
        }

        return $attachment;
    }



    private function update_product_image($idAttachment)
    {

        $images ='';
        foreach ($this->product->get_images() as $item)
        {
            $images.=$item['id'].',';
        }

        update_post_meta($this->product->get_id(), '_product_image_gallery',  $images.$idAttachment.',');
    }

    private function update_draw($idAttachment)
    {

        update_field($this->key_draw, $idAttachment, $this->product->get_id());
    }

}