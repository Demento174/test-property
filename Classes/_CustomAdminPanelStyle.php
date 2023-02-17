<?php

namespace Controllers;

class CustomAdminPanelStyle
{
    public $login_styles = [];
    public $login_scripts = [];
    public $login_content = null;
    public $logo_url = null;

    public function add_to_login_page()
    {
        if([] !== $this->login_styles and null !== $this->login_styles)
        {
            echo $this->add_style($this->login_styles);
        }
        if([] !== $this->login_scripts and null !== $this->login_scripts)
        {
            echo $this->add_scripts($this->login_scripts);
        }

        echo $this->login_content;


    }

    public function custom_logo($wp_admin_bar)
    {
        if(null === $this->logo_url)
        {
            return;
        }

        $this->add_admin_bar(
                $wp_admin_bar,
                'wp-logo',
                `<img style="width:30px;height:30px;" src="$this->logo_url" alt="" >`,
                home_url('/'),
                ['title' => 'О моем сайте',]
        );
    }

    private function add_style(array $styles):?string
    {
        $result = '';
        foreach ($styles as $style)
        {
            $result .= `<link rel="stylesheet" type="text/css" href="$style" />`;
        }

        return $result;
    }

    private function add_scripts(array $scripts):?string
    {
        $result = '';
        foreach ($scripts as $script)
        {
            $result .= `<scrip src="$script"></scrip>`;
        }

        return $result;
    }

    private function add_admin_bar($wp_admin_bar,string $id,string $title,string $link,array $meta=null)
    {
        $wp_admin_bar->add_menu( array(

            'id'    => $id,

            'title' => $title, // иконка dashicon

            'href'  => $link,

            'meta'  => $meta,
        ) );
    }



    public static function login_head(array $login_styles=[],
                                      ?array $login_scripts=[],
                                      string $login_content='')
    {
        $controller = new self();
        $controller->login_styles = $login_styles;
        $controller->login_scripts = $login_scripts;
        $controller->login_content = $login_content;

        add_action('login_head', [$controller,'add_to_login_page']);
    }

    public static function login_header(string $login_content)
    {
        $controller = new self();

        $controller->login_content = $login_content;

        add_action('login_header', [$controller,'add_to_login_page']);
    }

    public static function logo_in_admin_bar(string $logo_url=null)
    {
        $controller = new self();
        $controller->logo_url = $logo_url;

        remove_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 ); // удаляем стандартную панель (логотип)

        add_action( 'admin_bar_menu', [$controller,'custom_logo'], 10 ); // добавляем свою
    }
}



//add_action('login_head', 'custom_login_page');
//
//add_action('login_head', 'my_custom_login_logo');
//
//add_action('add_admin_bar_menus', 'reset_admin_wplogo');



function custom_login_page()
{

    echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo( 'template_directory' ) . '/public/css/admin.css" />';
}

function my_custom_login_logo()
{

    echo '<style type="text/css">
	
	h1 a { background-image:url('.get_field('common','options')['faveicon']['url'].') !important; }
	
	</style>';
}

function reset_admin_wplogo(  ){

    remove_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 ); // удаляем стандартную панель (логотип)

    add_action( 'admin_bar_menu', 'my_admin_bar_wp_menu', 10 ); // добавляем свою
}

function my_admin_bar_wp_menu( $wp_admin_bar )
{

    $wp_admin_bar->add_menu( array(

        'id'    => 'wp-logo',

        'title' => '<img style="width:30px;height:30px;" src="'. get_field('common','options')['faveicon']['url'].'" alt="" >', // иконка dashicon

        'href'  => home_url('/'),

        'meta'  => array(

            'title' => 'О моем сайте',
        ),
    ) );
}

//add_action( 'login_header', 'wpse330527_add_html_content' );

function wpse330527_add_html_content()
{
    ?>
    <div class="video_wrapper">
        <video preload="auto" autoplay="autoplay" loop="loop" muted="true" poster="" class="video_background">
            <source src="<?=get_template_directory_uri()?>/public/video/GENERAL-ELEVATOR-GE-promotional-video_3-1.mp4" type="video/mp4">
        </video>
    </div>


    <?php
}