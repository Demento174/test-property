<?php


namespace Controllers\WC;
use Controllers\PostsAndTax\PostProduct;

class SearchForm
{
    private $type;
    private $manufacturer;
    private $text;

    private  $data= [];

    public function __construct($type='',$manufacturer='',$text='')
    {

        if(!$this->check_input_values($type,$manufacturer,$text))
        {

            return;
        }

        
        $this->type = $this->setter($type);
        $this->manufacturer = $this->setter($manufacturer);
        $this->text = $this->setter($text);

        $this->handler();
    }

    private function setter ($value)
    {
        $value = trim($value);
        
        
        
        if($value == '0' || $value == '')
        {
            return false;
        }
        return $value;
    }


    public function get_data()
    {
        $result = [];

        foreach ($this->data as $item)
        {
            $product = new PostProduct($item);
            $result[] =
                [
                    'title'=>$product->title,
                    'link'=>$product->link,
                    'img'=>$product->img,
                ];
        }
        return $result;
    }

    private function check_input_values($type='',$manufacturer='',$text='')
    {
        if($type == ''&&
            $manufacturer == '' &&
            $text == ''
        ) {
            return false;
        }
        return true;

    }

    private function search_by_type()
    {

        $data = new \WP_Query(
            [
                'post_type'=>'product',
                'numberposts'=>-1,
                'tax_query' => array(
                    array (
                        'taxonomy' => 'product_cat',
                        'field' => 'slug',
                        'terms' => $this->type,
                    )
                ),
            ]);

        if($this->data)
        {
            $this->data = array_intersect($this->data,wp_list_pluck($data->posts,'ID'));
        }else
            {
                $this->data = wp_list_pluck($data->posts,'ID');
            }

//        $this->data = array_merge($this->data,wp_list_pluck($data->posts,'ID'));

    }

    private function search_by_manufacturer()
    {
        $data = new \WP_Query(
            [
                'post_type'=>'product',
                'numberposts'=>-1,
                'tax_query' => array(
                    array (
                        'taxonomy' => 'pa_manufacturer',
                        'field' => 'slug',
                        'terms' => $this->manufacturer,
                    )
                ),
            ]);

        if($this->data)
        {
            $this->data = array_intersect($this->data,wp_list_pluck($data->posts,'ID'));
        }else
        {
            $this->data = wp_list_pluck($data->posts,'ID');
        }

//        $this->data = array_merge($this->data,wp_list_pluck($data->posts,'ID'));
    }

    private function search_by_text()
    {
        $query1 = new \WP_Query(
            [
                'post_type'=>'product',
                'numberposts'=>-1,
                's'=>$this->text,
            ]);

//        $this->data = array_merge($this->data,wp_list_pluck($data->posts,'ID'));

        $query2 = new \WP_Query(
            [
                'post_type'=>'product',
                'numberposts'=>-1,
                'meta_query' => array(
                    array (
                        'key' => '_sku',
                        'value' => $this->text,
                        'compare' => 'LIKE'
                    )
                ),
            ]);

        $data = array_merge($query1->posts,$query2->posts);

        if($this->data)
        {
            $this->data = array_intersect($this->data,wp_list_pluck($data,'ID'));
        }else
        {
            $this->data = wp_list_pluck($data,'ID');
        }

        $this->data = array_merge($this->data,wp_list_pluck($data->posts,'ID'));
    }

    private function handler()
    {
  
        if($this->type)
        {
            $this->search_by_type();
        }
        if($this->manufacturer)
        {
            $this->search_by_manufacturer();
        }
        if($this->text)
        {
            $this->search_by_text();
        }


    }
}