<?php
namespace Classes\PostsAndTax\Taxonomy;



use Classes\PostsAndTax\Taxonomy\Query;

abstract class ATaxonomy{

    static private $required_variables = [ 'taxonomyType','postClass' ];

    use \Classes\Traits\CheckStaticVariables;

    protected                    $type;
    protected                    $term;
    protected                    $id;
    public           string|null $title         = null;
    public           string|null $slug          = null;
    public           string|null $description   = null;
    public           string|null $link          = null;
    public        ATaxonomy|null $parent        = null;
    public        ATaxonomy|null $children      = null;

    public static \Classes\PostsAndTax\Taxonomy\Query $query;

    public function __construct(string$type, int $id)
    {


        $this->type = $type;

        if(!$this->term = get_term_by( 'id', $id, $this->type))
            throw new \Exception("term for ID ".$id." from class name ".get_called_class()." does not exist");


        $this->id           = $this->term->term_id;

        $this->title        = $this->term->name;

        $this->slug         = $this->term->slug;

        $this->description  = $this->term->description;

        $this->link         = get_term_link((int)$this->id, $this->type );





    }


    protected function set_parent():null|\TaxonomyAbstract
    {
        $className = get_called_class();
        return [] === get_ancestors( $this->id, $this->type, 'taxonomy' )?
                            null:
                            new $className(get_ancestors( $this->id, $this->type, 'taxonomy' )[0]);

    }

    protected function set_children(bool $hide_empty=true):null|array
    {
        $result = [];
        $className = get_called_class();
        foreach (get_terms( $this->type,['hide_empty' => $hide_empty,'parent'=>$this->id]) as $item)
            $result[] = new $className($item->term_id);

        return []===$result?null:$result;
    }


    /**
     * @return array
     * @throws \Exception
     * Получает первый уровень таксономии
     */
    protected static function set_mainLevelTaxonomy():null|ATaxonomy
    {
        if(!self::check_static_variables(get_called_class()))
            throw new \Exception(get_called_class()." don't have required static variable");

        $type=get_called_class()::$taxonomyType;

        $className=get_called_class();

        $result = [];

        foreach (get_terms( $type,['hide_empty' => true,'parent'=>0]) as $item)
            $result[] = new $className($item->term_id);

        return $result;
    }


    protected static function query()
    {
        return new Query();
    }


}