<?php

namespace Controllers\MicroMarkup\Types\Product;

use Controllers\MicroMarkup\Types\MicroMarkupAbstract;
use Controllers\MicroMarkup\Types\Product\ProductData;

class Product extends MicroMarkupAbstract
{
    private $data_class;

    public function __construct(ProductData $data_class)
    {
        $this->data_class = $data_class;
        parent::__construct();
    }

    protected  function set_type():string
    {
        return $this->data_class::$TYPE;
    }

    protected  function add_data()
    {

        foreach ($this->data_class as $key=>$item)
        {
            if(empty($item) || $key==='TYPE')
            {
                continue;
            }
            $this->data[$key] = $item;
        }
    }
    public function get_data():array
    {
        return $this->data;
    }

}