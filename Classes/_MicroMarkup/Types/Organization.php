<?php

namespace Controllers\MicroMarkup\Types;

class Organization extends MicroMarkupAbstract
{
    static $TYPE = 'Organization';

    protected  function set_type():string
    {
        return self::$TYPE;
    }

    protected  function add_data()
    {

        $this->data['@address']=
            [
                "@type"=> "PostalAddress",
                "addressLocality"=> explode(',',replaceCity('[address]'))[1],
                "postalCode"=> explode(',',replaceCity('[address]'))[0],
                "streetAddress"=> explode(',',replaceCity('[address]'))[2].', '.explode(',',replaceCity('[address]'))[3]
            ];
        $this->data['name']='РСУ4';
        $this->data['telephone']=replaceCity('[phone]');
        $this->data['email']=replaceCity('[email]');
        $this->data['logo']=get_template_directory_uri()."/img/rsu_logo.svg";

    }
    public function get_data():array
    {
        return $this->data;
    }
    public static function index()
    {
        $controller  = new self();
        return $controller;
    }
}