<?php
namespace Classes\Traits;

trait CheckPlugin
{
    private string|null $plugin = null;

    private function set_plugin(string $plugin)
    {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        $this->plugin = $plugin;
    }

    private  function checkPlugin()
    {
        if(null === $this->plugin)
            throw new \Exception('Please set plugin before check him');
        if(!function_exists('is_plugin_active'))
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        return is_plugin_active($this->plugin);
    }
}