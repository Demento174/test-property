<?php
namespace Classes\PostsAndTax\Posts;




abstract class APost{


    public static \Classes\PostsAndTax\Posts\Query $query;

    protected           string $type;
    protected           int $id;
    protected           string|null $title      = null;
    protected           string|null $link       = null;
    protected            array|null $image      = null;
    protected            array|null $taxonomies   = null;



    protected function __construct(string $type,$id=null)
    {


        $this->query = new Query();

        $this->type     = $type;
        $this->id       = $this->query->id($id);

        $this->title    = $this->set_title($this->id);
        $this->link     = $this->set_link($this->id);
    }



    protected function set_title($id):?string
    {
        return get_the_title($id);
    }

    protected function set_link($id):?string
    {
        return get_permalink($id);
    }

    protected function set_taxonomy($taxonomy,string $className):?array
    {

        if(!get_the_terms( $this->id, $taxonomy ))
            return null;

        $result = [];

        foreach (get_the_terms( $this->id, $taxonomy ) as $item)
        {
            $result[] = new $className($item->term_id);
        }

        return $result;
    }

    public static function convert_post(int|array $input,string $className):array
    {

        if(gettype($input) === 'array')
        {
            $result = [];
            foreach ($input as $item)
            {
                if('integer'===gettype($item))
                {
                    $result[] = new $className($item);

                    continue;
                }
                $result[] = property_exists($item,'ID')?new $className($item->ID):new $className($item->id);
            }
        }else {
            $result = property_exists($input, 'ID') ? new $className($input->ID) : new $className($input->id);;
        }

        return $result;
    }

    public static function query():Query
    {
        return new Query();
    }
}