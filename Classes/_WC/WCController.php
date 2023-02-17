<?php
namespace Controllers\WC;

use Controllers\PostsAndTax\PostProduct;

class WCController{
    /**
     * Возвращает текущую корзину пользователя
     */
    static function get_cart()
    {

        $data = [];
        $data['basket']= [];

        foreach (WC()->cart->get_cart() as $key=>$item)
        {

            $product = new PostProduct($item['product_id']);
            $data['basket'][]=
                [
                    'key'           => $key,
                    'article'       => $product->get_article(),
                    'title'         => $product->get_title(),
                    'link'          => $product->get_link(),
                    'quantity'      => $item['quantity'],
                    'line_total'    => $item['line_total'].' '.get_field('shop','options')['units']['currency'],
                    'price'         => $product->get_price(),
                    'regular_price' => $product->get_regular_price(),
                    'sale_price'    => $product->get_sale_price(),
                    'discount'      => $product->get_discount(),
                    'image'         => $product->get_image(),
                    'currency'      => $product->get_currency(),
                ];
        }
        $price = (int) self::get_totalBasketPrice();
        $data['price']=$price .' '.get_field('shop','options')['units']['currency'];
        $data['quantity']= self::get_countItemInTheBasket();

        return $data;
    }

    /**
     * Суммарная стоимость товара в корзине
     */
    static function get_line_total_in_cart($key)
    {
        $line = WC()->cart->get_cart_item( $key );
        return $line['line_total'].' '.get_field('shop','options')['units']['currency'];
    }

    /**
     * Количество товаров в корзине
     */
    static function get_countItemInTheBasket()
    {
        return WC()->cart->get_cart_contents_count();
    }

    /**
     * Общая цена товаров в корзине
     */
    static function  get_totalBasketPrice()
    {
        return WC()->cart->cart_contents_total;
    }


    static function removeFromBasket($key)
    {
        $result = 0;
        if($key)
        {
            if(WC()->cart->remove_cart_item( $key ))
            {
                $result = $key;
            }

        }
        return $result;
    }

    static function add_to_basket($id)
    {
        WC()->cart->add_to_cart( $id );
    }

    static function changeQuantityInCart($key,$quantity)
    {
        $cart_item_key = $key;

        // Get the array of values owned by the product we're updating
        $threeball_product_values = WC()->cart->get_cart_item( $cart_item_key );

        // Get the quantity of the item in the cart
        $threeball_product_quantity = apply_filters( 'woocommerce_stock_amount_cart_item', apply_filters( 'woocommerce_stock_amount', preg_replace( "/[^0-9\.]/", '', filter_var($quantity, FILTER_SANITIZE_NUMBER_INT)) ), $cart_item_key );

        // Update cart validation
        $passed_validation  = apply_filters( 'woocommerce_update_cart_validation', true, $cart_item_key, $threeball_product_values, $threeball_product_quantity );

        // Update the quantity of the item in the cart
        if ( $passed_validation ) {
            WC()->cart->set_quantity( $cart_item_key, $threeball_product_quantity, true );
        }

    }

    static function createOrder($phoneInput,$nameInput='',$emailInput='',$addressInput='')
    {

        if((int) self::get_countItemInTheBasket() == 0)
            return;

        // Получить корзину
        $cart = WC()->cart;

        $phone = esc_attr(trim(stripslashes($phoneInput)));

        $name = esc_attr(trim(stripslashes($nameInput)));
        $email = esc_attr(trim(stripslashes($emailInput)));
        $nonce = esc_attr(trim(stripslashes($addressInput)));

        $address = [
            'first_name' => $name,
            'email'      => $email,
            'phone'      => $phone,
            'address_1'  => $nonce,
            'country'    => 'RU',
        ];

        $order = wc_create_order();

        // Информация о покупателе
        $order->set_address( $address, 'billing' );
        $order->set_address( $address, 'shipping' );

        // Товары из корзины
        foreach( $cart->get_cart() as $cart_item_key => $cart_item ) {

            $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

            $order->add_product( $_product, $cart_item['quantity'], [
                'variation' => $cart_item['variation'],
                'totals'    => [
                    'subtotal'     => $cart_item['line_subtotal'],
                    'subtotal_tax' => $cart_item['line_subtotal_tax'],
                    'total'        => $cart_item['line_total'],
                    'tax'          => $cart_item['line_tax'],
                    'tax_data'     => $cart_item['line_tax_data']
                ]
            ]);
        }


        $order->calculate_totals();

        $mailer = WC()->mailer();


        // Отправить письмо админу
        $email = $mailer->emails['WC_Email_New_Order'];
        $email->trigger( $order->id );


        // Очистить корзину
        $cart->empty_cart();

//        wp_send_json_success( $order->id );
    }

    static function item_in_the_cart()
    {
        if((int)self::get_totalBasketPrice() === 0 && (int)self::get_countItemInTheBasket() === 0)
        {
            return false;
        }
        return true;
    }

    static  function get_all_values_attribute($attribute)
    {
        $terms = get_terms( 'pa_'.$attribute,['hide_empty'=>false] );

        if(isset($terms['errors']) || !$terms)
        {
            return [];
        }

        $result = [];
        foreach ($terms as $item)
        {
            $result[]=
                [
                    'title'=>$item->name,
                    'slug'=>$item->slug,
                    'id'=>$item->term_id,
                ];
        }
        return $result;

    }

    /**
     * Получает экстремальнве значения ценв в категории товара
     * @param $term_id - id категории товара
     */
    static function get_min_max_price( $term_id=null ) {

        global $wpdb;
        if($term_id)
        {
            $sql = "
      SELECT  MIN( meta_value  ) as min_price , MAX( meta_value  ) as max_price
      FROM {$wpdb->posts} 
      INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)
      INNER JOIN {$wpdb->postmeta} ON ({$wpdb->posts}.ID = {$wpdb->postmeta}.post_id) 
      WHERE  
      ( {$wpdb->term_relationships}.term_taxonomy_id IN (%d) ) 
      AND {$wpdb->posts}.post_type = 'product' 
      AND {$wpdb->posts}.post_status = 'publish' 
      AND {$wpdb->postmeta}.meta_key = '_price'
      ";

            $result = $wpdb->get_results( $wpdb->prepare( $sql, $term_id ) );
        }else
            {
                $sql = "
      SELECT  MIN( meta_value  ) as min_price , MAX( meta_value  ) as max_price
      FROM {$wpdb->posts} 
      INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)
      INNER JOIN {$wpdb->postmeta} ON ({$wpdb->posts}.ID = {$wpdb->postmeta}.post_id) 
      WHERE  {$wpdb->posts}.post_type = 'product' 
      AND {$wpdb->posts}.post_status = 'publish' 
      AND {$wpdb->postmeta}.meta_key = '_price'
      ";

                $result = $wpdb->get_results( $sql);
            }



        return $result[ 0 ];

    }

    static function load_more_products($count,$products,$id=[])
    {

        $result = [];

        if($id)
        {
            foreach ($products as $product)
            {
               
                if(array_search($product->ID,$id) === false)
                {

                    $result[]=$product;
                }
            }
        }else
            {
                $result=$products;
            }


        return array_slice($result,0,$count);

    }

}