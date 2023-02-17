<?php
namespace Classes\TwigSettings;

class TwigSettings{
    private $settings;

    public function __construct($settings=null)
    {
        $this->settings = !$settings?require_once('settings.php'):$settings;
        $this->handler();
    }

    private function setDirname($dirname)
    {
        \Timber::$dirname =$dirname;
    }

    private function addFunction()
    {
        add_filter( 'timber/twig',
            function( \Twig_Environment $twig )
            {
                foreach ($this->settings['functions'] as $key=>$item)
                {
                    $twig->addFunction( new \Timber\Twig_Function( $key, $item ) );
                }
                return $twig;
            }
            );
    }

    private function handler()
    {
        $this->setDirname($this->settings['dirname']);
        $this->addFunction();
    }
}