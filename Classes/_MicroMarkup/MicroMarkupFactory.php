<?php

namespace Controllers\MicroMarkup;
use Controllers\MicroMarkup\Types\Breadcrumbs;
use Controllers\MicroMarkup\Types\MicroMarkupAbstract;
use Controllers\MicroMarkup\Types\Organization;

class MicroMarkupFactory
{
    private $controller = null;
    public function __construct($queryObject)
    {
        $this->controller = $this->set_controller($queryObject);
    }

    private function set_controller($queryObject)
    {
        $result = null;

        
        if(property_exists($queryObject,'ID'))
        {
//            get_category_parents(get_the_category($queryObject->ID)[0]->term_id)
            $parents = [];
            if(get_the_category($queryObject->ID)[0] and property_exists(get_the_category($queryObject->ID)[0],'term_id'))
            {

                foreach(explode(',',get_category_parents(get_the_category($queryObject->ID)[0]->term_id , false, ',')) as $parent){
                    $parents[] = get_term_by('name', $parent, 'category')->term_id;
                }
            }

            if(array_search(3, $parents) !== false)
            {

                $result = new \Controllers\MicroMarkup\Types\MicroMarkupMixinTypes([Breadcrumbs::index(),\Controllers\MicroMarkup\Types\Product\Concrete::index($queryObject)]);
            }else
                {
                    switch ($queryObject->ID)
                    {
                        case 1074:
                        case 177:
                        case 105:
                            $result = new \Controllers\MicroMarkup\Types\MicroMarkupMixinTypes([Breadcrumbs::index(),Organization::index()]);
                            break;
                        default:
                            $result = Breadcrumbs::index();
                            break;
                    }    
                }

            
            

        }else
                {
                    $result = Breadcrumbs::index();
                }
        return $result;
    }

    public function get_controller():?MicroMarkupAbstract
    {

        return $this->controller;
    }

    public static function index($queryObject):?MicroMarkupAbstract
    {
        $classSelf = new self($queryObject);
        return $classSelf->get_controller();
    }
}