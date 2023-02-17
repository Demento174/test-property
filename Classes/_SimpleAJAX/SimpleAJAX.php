<?php
namespace Controllers\SimpleAJAX;
use Controllers\Form\FormController;
use Controllers\Import\Suppliers\Matrix;
use Controllers\PostsAndTax\PostProduct;
use Controllers\WC\SearchForm;
use Controllers\WC\WCController;


use Controllers\XML\XmlStringStreamerWrapper;
use \Prewk\XmlStringStreamer;
use \Prewk\XmlStringStreamer\Stream\File;
use \Prewk\XmlStringStreamer\Parser\UniqueNode;

class SimpleAJAX {
	public $action=[];

	public $functionWC;

	public $wpdb;

	public function __construct($action) {

	    global $wpdb;

		$this->wpdb=$wpdb;

		$this->action=$action;

		foreach ($this->action as $item){

			add_action(
				'wp_ajax_'.$item,
				array( $this, $item)
			);

			add_action(
				'wp_ajax_nopriv_'.$item,
				array( $this, $item )
			);
		}
	}

    function search_form()
    {
        if(!$_POST)
        {
            return;
        }
        $class = new SearchForm($_POST['type'],$_POST['manufacturer'],$_POST['text']);


        echo json_encode($class->get_data());
        wp_die();
    }
    
    function load_products()
    {

        if(!$_POST && !$_POST['ids'])
        {
            echo json_encode([]);
            wp_die();
        }

        $args = ['post_type'=>'product','numberposts'=>-1];

        if($_POST['args'])
        {

            if($_POST['args']['firstCat'] || $_POST['args']['secondCat'] || $_POST['args']['thirdCat'])
            {
                $args['tax_query'] =
                    [
                        [
                            'taxonomy' => 'product_cat',
                            'field' => 'id',
                            'relation'=>'OR',
                            'terms' => [$_POST['args']['firstCat'],$_POST['args']['secondCat'],$_POST['args']['thirdCat']],
                            'include_child'=>true
                        ]
                    ];
            }

            if($_POST['args']['minPrice'])
            {
                $args['meta_query'][] =
                    [
                        'meta_key'     => '_price',
                        'meta_value'   => $_POST['args']['minPrice'],
                        'meta_type'    => 'INT',
                        'meta_compare' => '>=',
                    ];
            }
            if($_POST['args']['maxPrice'])
            {
                $args['meta_query'][] =
                    [
                        'key'     => '_price',
                        'value'   => $_POST['args']['maxPrice'],
                        'type'    => 'INT',
                        'compare' => '<=',
                    ];
            }

            if($_POST['args']['maxPrice'] && $_POST['args']['minPrice'])
            {
                $args['meta_query']['relation'] = 'AND';
            }
        }

        if($_POST['args']['manufacturers'])
        {
            
            $args['tax_query'][] =
                [
                    'taxonomy'=> 'pa_izgotovitel',
                    'field'=>'id',
                    'terms'=>$_POST['args']['manufacturers'],
                ];
        }
        
        $products = get_posts($args);
        

        
        $count = get_field('shop','options')['count_display_products'];


        $result = [];

        foreach (WCController::load_more_products($count,$products,$_POST['args']['ids']) as $key=>$item)
        {
            $product = new PostProduct($item->ID);

            $result[$key]=
                [
                    'id'=>$product->get_id(),
                    'title'=>$product->title,
                    'link'=>$product->link,
                    'img'=>$product->img,
                    'compatibility'=>$product->get_compatibility(),
                    'article'=>$product->get_article(),
                    'price'=>$product->get_price(),
                    'dimensions'=>$product->get_dimensions()
                ];
        }


        echo json_encode($result);
        wp_die();

    }

    function add_to_basket()
    {
        if(!$_POST['id'])
        {
            wp_die("request don't have ID");
        }

        WCController::add_to_basket($_POST['id']);

        $result=
            [
                'count'=>WCController::get_countItemInTheBasket(),
                'price'=>WCController::get_totalBasketPrice()
            ];

        echo json_encode($result);
        wp_die();
    }

    function remove_from_basket()
    {
        if(!$_POST['id'])
        {
            return;
        }
        WCController::removeFromBasket($_POST['id']);

        wp_die(json_encode(['count'=>WCController::get_countItemInTheBasket(),'price'=>WCController::get_totalBasketPrice()]));
    }
    
    function change_quantity_in_basket()
    {
        if(!$_POST['id'] || !$_POST['quantity'] )
        {
            wp_die(json_encode([]));
        }
       WCController::changeQuantityInCart($_POST['id'],$_POST['quantity']);

        wp_die(json_encode(
            [
                'count'=>WCController::get_countItemInTheBasket(),
                'price'=>WCController::get_totalBasketPrice(),
                'line_total'=>WCController::get_line_total_in_cart($_POST['id'])
            ]));
    }
    
    function order()
    {
        if(!$_POST['phone'])
            wp_die('Не пришёл номер телефона');

        WCController::createOrder($_POST['phone'],$_POST['name'],$_POST['email'],$_POST['address']);

    }

    function send_form()
    {
        if(!$_POST['type'])
            die('Не пришли данные о форме ($_POST[\'type\']) ');

        new FormController($_POST);
    }

    function update_category()
    {
        set_time_limit(0);
        $mat = new \Controllers\Import\Suppliers\Matrix($_POST['url'],$_POST);

        $mat->handler_categories();

        wp_die();
    }

    function update_products()
    {
        set_time_limit(0);
        $m = new \Controllers\Import\Suppliers\Matrix();
        $m->handler_products();
        wp_die();
    }

    function update_products_image()
    {
        set_time_limit(0);
        $m = new \Controllers\Import\Suppliers\Matrix();
        $m->handler_products_images();
        wp_die();
    }
}