<?php


namespace Classes\Blocks;




use Classes\Interfaces\RenderInterface;

/**
 * Класс Адаптер для рендера шаблонов Twig
 */
class BlockTwig implements RenderInterface
{

    static private $VIEW_FOLDER = 'Views';

    private   string        $template;
    protected array|null    $data;

    public function __construct(string $template,array|null $input = null,bool $debug=false,callable|null $debug_function=null)
    {

        $this->template  = $this->set_template($template);
        $this->data      = $this->set_data($input);

        if(true === $debug)
            $this->debug($debug_function);

    }

    private function set_template($template):string
    {

        $arr = explode('.',$template);

        $template =
            'twig'===end($arr)?
                $template: $template.'.twig';

        if(!file_exists(self::get_view_path().$template))
            wp_die("Template $template does not exist");

        return $template;
    }

    protected function set_data(array|null $input):null|array
    {
        return $input;
    }

    private function debug(callable|null $debug_function)
    {
        if($debug_function)
            $debug_function($this->data);
        else
            \Classes\Debugger\Debugger::debug($this->data);
    }

    public function render():void
    {
        if(false === class_exists('\Timber'))
            wp_die('Класс Timber не найден');

        \Timber::render($this->template,$this->data);
    }

    private static function get_view_path():string
    {
        return get_template_directory().'/'.self::$VIEW_FOLDER.'/';
    }
}