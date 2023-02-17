<?php

namespace Controllers\Widgets\Widgets;

class Map_widget
    extends WidgetAbstract
    implements WidgetInterface
{
    const TITLE       = 'Блок с картой';
    const NAME        = 'map';
    const NAMESPACE   = 'Blocks';

    private array|null $fields = null;

    private function set_fields()
    {

        $contacts = get_field('contacts',ACF_PAGE_SLUG_OPTIONS);

        $data =
            [
                'phones'=>$contacts['phones'],
                'address'=>$contacts['address'],
                'email'=>$contacts['email'],
                'icons'=>
                    [
                        'phone'=>get_icon('phone'),
                        'email'=>get_icon('email'),
                        'address'=>get_icon('address'),
                    ],
            ];
        return array_merge(get_field('map',ACF_PAGE_SLUG_COMMON),$data);
    }


    public function get_title():string
    {
        return self::TITLE;
    }
    public function get_name():string
    {
        return self::NAME;
    }
    public function get_namespace():?string
    {
        return self::NAMESPACE;
    }
    public function get_selectors():null|array
    {
        return $this->fields;
    }

    public function render()
    {
        $this->fields = $this->set_fields();

        parent::render();
    }

}