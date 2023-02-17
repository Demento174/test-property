<?php
namespace Controllers\Widgets;

use \Controllers\Translite\Translate;
use \Controllers\Widgets\Controllers\AcfRegisterBlock;
use Controllers\Widgets\Widgets\WidgetAbstract;

class Widgets
{
    const OPTIONS =
        [
            'name',
            'title',
            'namespace',

            'selectors',

            'description',
            'icon',
            'keywords',
            'post_types',
            'mode',
            'align',
            'align_text',
            'align_content',
            'supports',

            'controller'


        ];

    private array|null $templates             = null;
    private array|bool $allowed_default_block = true;

    public function __construct(string $widgets_template_path)
    {
        $this->templates = $this->set_templates($widgets_template_path);
    }

    /**
     * @param $path
     * @return array|null
     * Возвращает все шаблоны указанной папки, вложеностью максимум 2,
     * рекурсивный поиск
     */
    private function set_templates($path):?array
    {

        if(false === is_dir($path)) Errors::init(Errors::ERROR['template_directory']);

        $result = [];

        foreach (scandir($path) as $template)
        {

            if(false !== array_search($template,['.','..'])) continue;

            if(true===is_dir($path.'/'.$template))
            {
                $result = array_merge($result,$this->set_templates($path.'/'.$template));
                continue;
            }
            if(!empty($options = $this->get_options_from_comments($path.'/'.$template)))
            {

                if(key_exists('controller',$options))
                {

                    $controller =  new $options['controller']($this->set_template($path.'/'.$template));

                    $result[] =
                        [
                            'title'=>$controller->get_title(),
                            'name'=>$controller->get_name(),
                            'namespace'=>$controller->get_namespace(),
                            'template'=>$this->set_template($path.'/'.$template),
                            'selectors'=>$controller->get_selectors(),
                            'render_callback'=>[$controller,'render']
                        ];

                    continue;
                }

                if(!key_exists('title',$options))Errors::init(Errors::ERROR['template_title']);

//                if(!key_exists('namespace',$options))Errors::init(Errors::ERROR['template_namespace']);

                $name = key_exists('name',$options)?
                    $options['name']:
                    Translate::convertor($options['title']);
                $options['name'] = $name;
                $options['template']= $this->set_template($path.'/'.$template);


                $result[] = $options;
            }

        }

        return $result;
    }

    /**
     * @param string $path
     * @return string|null
     * Возвращает название файла шаблона без основной папки с шаблонами
     */
    private function set_template(string $path):?string
    {
        return explode('blocks/',$path)[1];
    }

    /**
     * @param $file
     * @return array|null
     * Получает первые комментарии в шаблоне в виде массива key : value
     */
    private function get_options_from_comments($file):?array
    {
        $result= [];

        preg_match_all("/\{\#(.*)\#\}/s",file_get_contents($file),$matches);

        if(false === key_exists(0,$matches[1]))
        {
            return $result;
        }

        foreach (explode("\n",$matches[1][0]) as $line)
        {

            if(false === array_search(mb_strtolower(trim(explode(':',$line)[0])),self::OPTIONS))continue;
            $result[mb_strtolower(trim(explode(':',$line)[0]))] = trim(explode(':',$line)[1]);
        }

        return $result;
    }

    /**
     * @param array|bool $exception_default_blocks
     * @return void
     * Отключает все блоки кроме указанных в массиве передаваемом парамметре +
     * зарегестрированные блоки
     */
    public function disable_default_blocks(array|bool $exception_default_blocks=[])
    {
        $this->allowed_default_block = $exception_default_blocks;

        add_filter( 'allowed_block_types_all',function ($allowed_block_types , $block_editor_context)
        {
            $_templates = [];

            foreach ($this->templates as $template)
            {
                if(key_exists('controller',$template))$_templates[]='acf/'.$template['controller']::NAME;
                $_templates[]='acf/'.str_replace('_','-',$template['name']);

            }


            return array_merge($_templates,$this->allowed_default_block);
        } ,  10 ,  2 );
    }

    /**
     * @return void
     * Метод инициализатор, регестрирует блоки через API Advanced custom fields
     */
    public function handler_acfRegisterBlock()
    {


        foreach ($this->templates as $template)
        {
            $controller = new AcfRegisterBlock(
                $template['name'],
                $template['title'],
                $template['namespace'],
                $template['template']
            );

            foreach (['name','title','namespace','template'] as $key)
            {
                unset($template[$key]);
            }
            if(!empty($template))
            {
                foreach ($template as $key=>$attribute)
                {

                    if(
                        'selectors'=== $key
                            &&
                        gettype($attribute)==='string'
                            &&
                        'integer' === gettype(strripos($attribute,','))
                    )
                    {
                        foreach ($attribute=explode(',',$attribute) as $_key=>$item)
                        {
                            if(empty($item))unset($attribute[$_key]);
                        }
                    }


                    $controller->$key = $attribute;
                }
            }

            $controller->init();

        }
    }


}