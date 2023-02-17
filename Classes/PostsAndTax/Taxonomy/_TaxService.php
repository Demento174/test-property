<?php
namespace Controllers\PostsAndTax;

class TaxService extends TaxonomyAbstract {

    static string $taxType ='tax_service';
    static string $postClass='\Controllers\PostsAndTax\PostService';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public function get_id():int
    {
        return (int) $this->id;
    }

    public function get_title():string
    {
        return $this->title;
    }

    public function get_link():string
    {
        return $this->link;
    }


    public function is_first_level():bool
    {
        return !$this->parent;
    }

    public function is_second_level():bool
    {
        if(!$this->parent)
            return false;

        if($this->parent->is_first_level())
            return true;

        return false;
    }

    public function is_third_level():bool
    {
        if(!$this->parent)
            return false;

        if($this->parent->is_second_level())
            return true;

        return false;
    }

    private function get_main_level():null|TaxonomyAbstract
    {
        if($this->is_first_level())
        {
            return null;
        }elseif($this->is_second_level())
        {
            return $this->parent;
        }

        return $this->parent->parent;

    }

    public function get_other_cats():?array
    {

        if(!$this->get_main_level())
            return null;

        $result= [];
        $called_class = get_called_class();
        $parents = get_terms( $this->type,['hide_empty' => true,'parent'=>0]);


        foreach ($parents as $parent)
        {
            if($parent->term_id == $this->get_main_level()->get_id())
            {
                foreach (get_terms($this->type,['hide_empty' => true,'parent'=>$parent->term_id]) as $children)
                {
                    if($children->term_id !== $this->id)
                        $result[] = new $called_class($children->term_id);
                    continue;
                }
            }
        }
        return  $result;
    }


    public static function get_termFirstLevel()
    {
        if(!self::check_static_variables(get_called_class()))
            throw new \Exception(get_called_class()." don't have required static variable");
        $result = [];
        $className=get_called_class();
        foreach (get_terms( $className::$taxType,['hide_empty' => true,'parent'=>0,]) as $key=>$item)
        {
            $result[] = new $className($item->term_id);
        }
        return $result;
    }

    public static function get_termSecondLevel()
    {
        if(!self::check_static_variables(get_called_class()))
            throw new \Exception(get_called_class()." don't have required static variable");
        $result = [];
        $className=get_called_class();
        foreach (get_terms( self::$options['type'],['hide_empty' => false,'parent'=>0]) as $key=>$item)
        {

            foreach (get_terms( self::$options['type'],['hide_empty' => false,'parent'=>$item->term_id]) as $keyKey=>$tax)
            {
                $children = new $className($tax->term_id);
                $result[] = $children;
            }

        }
        return $result;
    }

    public static function get_termThirdLevel()
    {
        if(!self::check_static_variables(get_called_class()))
            throw new \Exception(get_called_class()." don't have required static variable");
        $result = [];
        $className=get_called_class();
        foreach (get_terms( self::$options['type'],['hide_empty' => true,'parent'=>0]) as $key=>$item)
        {

            foreach (get_terms( self::$options['type'],['hide_empty' => false,'parent'=>$item->term_id]) as $keyKey=>$tax)
            {

                foreach (get_terms( self::$options['type'],['hide_empty' => false,'parent'=>$tax->term_id]) as $taxTax)
                {

                    $childrenChildren = new $className($taxTax->term_id);

                    $result[$key]=$childrenChildren;
                }
            }
        }

        return $result;
    }

}