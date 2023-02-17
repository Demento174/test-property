<?php

namespace Controllers\FrontMenu;



use Controllers\PostsAndTax\TaxCat;

class FrontMenu
{
    public  array|null $acf_menu    = null;
    public  array|null $menu        = null;
    private int $shop_page_id;

    public function __construct()
    {
        $this->shop_page_id = $this->set_shop_id();
    }

    private function set_shop_id()
    {
        return get_option( 'woocommerce_shop_page_id' );
    }

    public function set_header_menu()
    {
        return get_field('header',ACF_PAGE_SLUG_OPTIONS)['menu']['items'];
    }

    public function set_footer_menu()
    {
        return get_field('footer',ACF_PAGE_SLUG_OPTIONS)['menu']['items'];
    }


    public function set_menu(array $menu):array
    {
       $result = [];

       foreach ($menu as $key=>$item)
       {
           $result[$key]=
               [
                   'title'=>$item['item']['title'],
                   'link'=>$item['item']['url'],
                   'target'=>$item['item']['target'],
               ];

           if($this->shop_page_id === url_to_postid( $item['item']['url'] ))
               $result[$key]['items']=TaxCat::get_termHierarchical()[0]['children'][0]['children'];
       }
       return $result;
    }

    private function set_menu_first_level($menu)
    {
        foreach ($menu as $key => $item)
        {
            if ($item->menu_item_parent)
            {
                unset($menu[$key]);
            }
        }
        return $menu;
    }

    public static function header()
    {
        $self           = new self();
        $self->acf_menu = $self->set_header_menu();
        $self->menu     = $self->set_menu($self->acf_menu);
        return $self->menu;
    }

    public static function footer()
    {
        $self           = new self();
        $self->acf_menu = $self->set_footer_menu();
        $self->menu     = $self->set_menu($self->acf_menu);
        return $self->menu;
    }

}