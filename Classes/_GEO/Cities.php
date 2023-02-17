<?php

namespace Controllers\GEO;

use Controllers\Interfaces\InitInterface;

class Cities implements InitInterface
{
    static $acf_slug = 'cities';
    static $acf_default_contacts = 'contacts';

    private array|null $cities              = null;
    private array|null $default_city        = null;
    private array|null $default_contacts    = null;
    private array|null $current_city        = null;
    private array|null $current_contacts    = null;


    public function __construct(string|null $domain)
    {
        $this->cities = $this->set_cities();
        $this->default_city = $this->set_default_city($this->cities);
        $this->default_contacts = $this->set_default_contacts();
        $this->current_city = $this->set_current_city($domain,$this->cities);
        $this->current_contacts =$this->set_current_contacts();
    }

    private function set_cities():array
    {
        return get_field(self::$acf_slug,ACF_PAGE_SLUG_OPTIONS)['items'];
    }

    private function set_default_city(array $cities):array
    {
        return $cities[0];
    }

    private function set_default_contacts():array
    {
        $contacts = get_field(self::$acf_default_contacts,ACF_PAGE_SLUG_OPTIONS);
        $contacts['email'] = $contacts['emails']['out'];
        unset($contacts['emails']);
        return $contacts;
    }

    private function set_current_city(string $domain,array $cities):array
    {

        $key = false;
        foreach ($cities as $_key=>$city)
        {
            $url = str_replace('www.','',
                str_replace('http://','',
                    str_replace('https://','',$city['link'])
                )
            );
            $url = substr($url, -1) === '/'? mb_substr($url, 0, -1):$url;


            if($url === $domain)
            {
                $key = $_key;
                break;
            }
        }


        return false === $key?
            $this->default_city:
            $cities[$key];
    }

    private function set_current_contacts():?array
    {
        return $this->current_city['switch_contacts']?$this->default_contacts:$this->current_city;
    }

    public function get_sort_cities()
    {
        $result = [];
        foreach ($this->cities as $item)
        {
            $firstLetter = mb_strtoupper(mb_substr(trim($item['title']), 0, 1));
            $result[$firstLetter][] = $item;
        }
        krsort($result);

        return array_reverse($result);
    }

    public function get_current_city():array
    {
        return $this->current_city;
    }

    public function get_current_contacts():array
    {
        return $this->current_contacts;
    }

    public static function init(string $domain=null):self
    {
        if(null===$domain)
        {
            throw new \Exception('need domain');
        }

        $classObject = new self($domain);
        return $classObject;
        // TODO: Implement init() method.

    }

}