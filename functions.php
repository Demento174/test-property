<?php
/**
 * Template functions and definitions
 *
 * @link https:developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Template
 * @since 1.0
 */

/**
 * ID Администратора, с правами суперпользователя
 */
const ADMINITSTRATOR = 2;




/**
 * Подключение все кастомных классов в данном файле
 */
require_once (get_template_directory().'/classes.php');

/**
 * @param string $template
 * @param array|null $input
 * @param bool $debug
 * @param callable|null $debug_function
 * @return void
 * Функция обертка для класс \Classes\Blocks\BlockTwig
 */
function renderBlock(string $template,array|null $input = null,bool $debug=false,callable|null $debug_function=null)
{

    $class = new \Classes\Blocks\BlockTwig($template,$input,$debug,$debug_function);
    $class->render();
}


//add_action( 'created_taxonomy_property', 'wp_kama_created_taxonomy_action', 10, 3 );


