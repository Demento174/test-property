<?php

namespace Controllers\MicroMarkup\Types\Product;

use Controllers\MicroMarkup\Types\MicroMarkupAbstract;
use Controllers\MicroMarkup\Types\Product\ProductData;

class Concrete
{
    private static $CHARACTERISTICS =
        [
            'Класс прочности',
            'Расчетная прочность',
            'Уровень морозостойкости',
            'Уровень водонепроницаемости',
            'Уровень подвижности',
            'Время полного затвердевания смеси',
            'Плотность',
            'Соотношение песка и камня',
            'Состав',
            'Тип покрытия',
            'Прочность',
            'Лещадность (норма фракции)',
            'Уровень морозостойкости',
            'Радиоактивность',
            'Насыпная плотность',
            'Уровень водопоглощения'

        ];

    private $post = null;
    private $module_block = null;
    private $description = null;
    private $characteristics = null;
    private $other = null;

    public function __construct(int $ID)
    {
        $this->post = get_post($ID);

        if(!$this->post || !$this->module_block = $this->set_module_block($this->post) )
        {
            return;
        }

        $this->description = $this->set_description($this->module_block);

        $this->characteristics = $this->set_characteristics($this->module_block);

        $this->other = $this->set_others($ID);
    }

    private function set_module_block(\WP_Post $post):?array
    {
        return !get_field('модульный_блок',$post)?null:get_field('модульный_блок',$post);
    }

    /**
     * @param int $ID
     * @return string (text WYISWIG)
     */
    private function set_description(array $module_block):?string
    {
        $key = array_search('component-text-media',array_column($module_block, 'модуль'));

        return $key===false?null:strip_tags(replaceCity($module_block[$key]['текст']));
    }


    /**
     * @param int $ID
     * @return array
     */
    private function set_characteristics(array $module_block):?array
    {
     
        $key = array_search('component-text',array_column($module_block, 'модуль'));
        if($key===false)
        {
            return null;
        }

        $result = [];
        $arr = explode('<tr>',$module_block[$key]['текст']);
        array_shift($arr);

        foreach ($arr as $row)
        {
            if(empty($row) or strripos($row,'Свойство') !== false)
            {
                continue;
            }

            foreach (self::$CHARACTERISTICS as $characteristic)
            {
                if(!empty($this->get_characteristic_from_table($characteristic,$row)))
                {
                    $result[$characteristic]= $this->get_characteristic_from_table($characteristic,$row);
                }
            }

            

        }


        return $result;

    }

    private function get_characteristic_from_table(string $characteristic,string $row):?string
    {
        if(strripos($row,$characteristic) === false)
        {
            return null;
        }

        return mb_strimwidth(strip_tags(trim(str_replace($characteristic,'',$row))),0,120);

    }

    /**
     * @param int $ID
     * @return array
     */
    private function set_others(int $ID):array
    {
        $result = null;
        foreach (get_field('похожие_товары') as $item)
        {
            $result[]=
                [
                    "@type"=>"offer",
                    "name"=>get_the_title($item->ID),
                    "availability"=>"http://schema.org/InStock",
                    "price"=>getMinPrice($item->ID),
                    "priceCurrency"=>"RUB"
                ];
        }
        return $result;
    }


    public function get_description():?string
    {
        return $this->description;
    }

    public function get_characteristics():?array
    {
        return $this->characteristics;
    }

    public function get_others():?array
    {
        return $this->other;
    }


    public static function index($queryObject)
    {

        $data = new ProductData();
        $concrete = new self($queryObject->ID);
        $data->name=$data->set_name($queryObject->ID);
        $data->category=$data->set_category(\BreadcrumbsYoast::DATA());
        $data->logo=$data->set_logo();
        $data->image = $data->set_image($queryObject->ID);
        $data->description = $concrete->get_description();

        $data->additionalProperty = !$concrete->get_characteristics()?null:$data->set_additionalProperty($concrete->get_characteristics());
        $data->offers = $concrete->get_others();
        $controller  = new Product($data);
        return $controller;
    }
}