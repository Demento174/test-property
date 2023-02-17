<?php
namespace Controllers\MetaBox\Fields;
use Controllers\MetaBox\MetaBox;

class ACFRepeater extends MetaBox
{

    private $acf = null;
    private $value = null;


    public function __construct($selectorACF,$posts,$titleBox,$group=null)
    {
        $this->set_acf($selectorACF,$group);
        parent::__construct($selectorACF, $posts,$titleBox);

    }

    private function set_acf($selector,$group=null)
    {

        $this->acf = get_field($selector,!$group?get_the_ID():$group);
    }

    private function set_value()
    {

        $this->value = explode(",", get_post_meta( get_the_ID(), $this->field_name, 1 ));

        foreach ($this->value as $key=>$item)
        {
            if(array_search($item, array_column($this->acf, 'name')) === false)
            {
                unset($this->value[$key]);
            }
        }
    }

    public function callback($post, $meta)
    {

        if(!$this->acf)
        {
            echo  'Поле Advanced Custom Field пустое, заполните его данными';
            return;
        }

        $this->set_value();
        $this->render();



    }

    static function get_field($post_id,$params=[])
    {
        $result= [];

        if(!get_post_meta($post_id,'field_'.$params['selector']))
        {
            return [];
        }
        foreach (get_field($params['selector'],$params['group']?$params['group']:$post_id) as $item)
        {

            if(array_search($item['name'],explode(',',get_post_meta($post_id,'field_'.$params['selector'])[0])) !== false)
            {
                $result[]=$item;
            }
        }
        return $result;
    }

    private function render()
    {

    ?>
        <div  class="conformity_icons"
              style="
    display: flex;
    justify-content: space-between;
    padding: 10px;"
        >

            <?php foreach ($this->acf as  $key=>$icon): ?>
                <lable class="item"
                       style="
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;

            height: 138px;"
                >
                    <div class="title" style="
                font-size: 16px;
                font-weight: bold;
                padding-bottom: 10px;">
                        <?=$icon['name']?>
                    </div>
                    <img src="<?=$icon['img']['url']?>" alt=""
                         style="padding-bottom: 10px;max-width: 100%;max-height: 60%"
                    >

                    <input type="checkbox"
                        <?if(array_search($icon['name'], $this->value) !== false):?> checked <?endif;?>
                           value="<?=$icon['name']?>" onchange="this.checked?add_conformity_icons(this.value):remove_conformity_icons(this.value)"
                           style="
                width: 20px;
                height: 20px;
                border: 2px solid #007cba;"
                    >
                </lable>
            <?php endforeach; ?>
            <input type="hidden" name="<?=$this->field_name?>" id="field_conformity_icons" value="<?=implode(",", $this->value);?>">

        </div>
        <script>
            function add_conformity_icons(value)
            {

                let values = document.querySelector('#field_conformity_icons').value?document.querySelector('#field_conformity_icons').value.split(','):[];
                values.push(value)
                console.log(values,values.join());
                document.querySelector('#field_conformity_icons').setAttribute('value',values.join());
                // document.querySelector('#field_conformity_icons').textContent = values.join();
            }

            function remove_conformity_icons(value)
            {
                let values = document.querySelector('#field_conformity_icons').value?document.querySelector('#field_conformity_icons').value.split(','):[];
                let index = values.indexOf(value)
                if(index === -1)
                {
                    return ;
                }
                values.splice(index,1);
                console.log(values,values.join());
                document.querySelector('#field_conformity_icons').value = values.join();
                // document.querySelector('#field_conformity_icons').textContent = values.join();
            }
        </script>
        <?php
    }


}