<?php


namespace Controllers\PostsAndTax;


class PostAttachment extends Post
{
    static private $postType = 'attachment';
    private null|string $path;
    private int|null  $filesize;

    public function __construct(int $id)
    {

        parent ::__construct(static::$postType, $id);

        $this->path =  $this->set_path($this->id);
        $this->filesize = $this->set_filesize($this->id);

    }


    private function set_path($id)
    {
        return get_attached_file($id);
    }

    private function set_filesize($id)
    {
        return filesize( get_attached_file( $id ) );
    }

    protected function set_link($id):?string
    {
        return wp_get_attachment_image_url($id);
    }

    public function get_link()
    {
        return $this->link;
    }

    private function delete()
    {
        wp_delete_attachment( $this->id);
    }

    public function get_filesize()
    {
        return $this->filesize;
    }
    public function compare_image_sizes(string $file):bool
    {

        return (int)self::get_filesize_from_url($file) === (int) $this->filesize;
    }



    public static function get_filesize_from_url($url)
    {
        $fp = fopen($url,"r");
        $inf = stream_get_meta_data($fp);
        fclose($fp);
        foreach($inf["wrapper_data"] as $v) {
            if (stristr($v, "content-length")) {
                $v = explode(":", $v);
                return trim($v[1]);
            }
        }
        return null;
    }

    public static function delete_all(int|null $numberposts=-1)
    {
        $allposts= get_posts( array('post_type'=>self::$postType,'numberposts'=>-$numberposts) );
        foreach ($allposts as $eachpost)
        {
            wp_delete_post( $eachpost->ID, true );
        }
    }
}