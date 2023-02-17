<?php

namespace Controllers\Widgets\Controllers;

class AcfRegisterBlock
{
    /**
     * (Строка) Уникальное имя, идентифицирующее блок (без пространства имен).
     * Например, «свидетельство». Примечание. Имя блока может содержать
     * только строчные буквы, цифры и дефисы и должно начинаться с буквы.
     */
    private string|null $name = null;

    /**
     * (Строка) Отображаемый заголовок для вашего блока. Например, «Отзыв».
     */
    private string|null $title = null;

    /**
     * (Строка) (Необязательно) Это краткое описание вашего блока.
     */
    public string|null $description = null;

    /**
     * (Строка) Блоки сгруппированы по категориям, чтобы помочь пользователям
     * просматривать и находить их. Основные предоставляемые категории:
     * [ common | formatting | layout | widgets | embed ].
     * Плагины и темы также могут регистрировать пользовательские категории блоков.
     */
    private string|null $category = null;

    /**
     * (String|Array) (Необязательно)
     * Можно указать свойство icon, чтобы упростить идентификацию блока.
     * Это может быть любой из Dashicons WordPress или пользовательский элемент svg.
     * 'icon' => 'book-alt',
     * 'icon' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0V0z" /><path d="M19 13H5v-2h14v2z" /></svg>',
     * 'icon' => array(
     *       // Specifying a background color to appear with the icon e.g.: in the inserter.
     *       'background' => '#7e70af',
     *       // Specifying a color for the icon (optional: if not set, a readable color will be automatically defined)
     *       'foreground' => '#fff',
     *       // Specifying a dashicon for the block
     *       'src' => 'book-alt',
     *   ),
     */
    public string|array|null $icon = null;

    /**
     * (Массив) (Необязательно) Массив условий поиска,
     * чтобы помочь пользователю обнаружить блок во время поиска.
     */
    public array|null $keywords = null;

    /**
     * (Массив) (Необязательно) Массив типов записей
     * для ограничения этого типа блока.
     */
    public $post_types = null;

    /**
     * (Строка) (Необязательно) Режим отображения для вашего блока.
     * Доступные настройки: «авто», «предварительный просмотр»
     * и «редактирование». По умолчанию «предварительный просмотр».
     * auto: предварительный просмотр отображается по умолчанию, но
     *      изменяется на форму редактирования при выборе блока.
     * preview: Предварительный просмотр отображается всегда.
     *      Форма редактирования появляется на боковой панели при выборе блока.
     * edit: Форма редактирования отображается всегда.
     */
    public string|null $mode = null;

    /**
     * (Строка) (Необязательно) Выравнивание блоков по умолчанию.
     * Доступные настройки:  “left”, “center”, “right”, “wide” и “full”. По умолчанию пустая строка.
     */
    public string|null $align = null;

    /**
     * (Строка) (Необязательно) Выравнивание блочного текста по умолчанию
     * (дополнительную информацию см. в настройках поддержки).
     * Доступные настройки: “left”, “center” and “right”.
     * По умолчанию используется выравнивание текста текущего языка.
     */
    public string|null $align_text = null;

    /**
     * (Строка) (Необязательно) Выравнивание содержимого блока по умолчанию
     * (дополнительную информацию см. в настройках поддержки).
     * Доступные настройки: “top”, “center” and “bottom”.
     * При использовании типа управления «Матрица» доступны
     * дополнительные настройки для указания всех 9 позиций от “top left” до “bottom right”.
     * По умолчанию «сверху».
     */
    public string|null $align_content = null;

    /**
     * (Строка) Путь к файлу шаблона, используемому для отображения блочного HTML.
     * Это может быть либо относительный путь к файлу в активной теме, либо полный путь к любому файлу.
     */
    private string|null $render_template = null;

    /**
     * (Вызываемый) (Необязательно) Вместо предоставления
     * render_template можно указать имя функции обратного вызова для вывода HTML-кода блока.
     */
    public  $render_callback = null;

    /**
     * (Строка) (Необязательно) URL-адрес файла .css,
     * который будет ставиться в очередь всякий раз,
     * когда отображается ваш блок (внешний и внутренний).
     */
    public string|null $enqueue_style = null;

    /**
     * (Строка) (Необязательно) URL-адрес файла .js,
     * который будет ставиться в очередь всякий раз,
     * когда отображается ваш блок (внешний и внутренний).
     */
    public string|null $enqueue_script = null;

    /**
     * (Вызываемый) (Необязательно) Функция обратного вызова, которая запускается всякий раз,
     * когда отображается ваш блок (интерфейсный и внутренний) и ставит в очередь сценарии и/или стили.
     */
    private $enqueue_assets = null;

    /**
     *(Массив) (Необязательно) Массив поддерживаемых функций. Можно использовать
     * все свойства из документации по поддержке блоков JavaScript.
     * Поддерживаются следующие параметры:
     *  align
     *      Это свойство позволяет кнопке панели инструментов управлять выравниванием блока.
     *      По умолчанию истинно. Установите значение false, чтобы скрыть панель инструментов
     *      выравнивания. Задайте массив конкретных имен трасс, чтобы настроить панель инструментов.
     * align_text
     *      Это свойство позволяет кнопке панели инструментов управлять выравниванием текста блока.
     *      По умолчанию ложно. Установите значение true, чтобы отобразить кнопку панели инструментов
     *      выравнивания. Текущее выбранное значение выравнивания будет доступно в обратном вызове рендеринга.
     *align_content
     *      Это свойство позволяет кнопке панели инструментов управлять внутренним выравниванием содержимого блока.
     *      По умолчанию ложно. Установите значение true, чтобы отобразить кнопку панели инструментов выравнивания,
     *      или установите значение «матрица», чтобы включить полную матрицу выравнивания на панели инструментов.
     *      Текущее выбранное значение выравнивания будет доступно в обратном вызове/шаблоне
     *      рендеринга через $block['align_content'].
     * full_height
     *      Это свойство включает кнопку полной высоты на панели инструментов блока и добавляет свойство
     *      $block[‘full_height’] внутри шаблона рендеринга/обратного вызова.
     *      $block[‘full_height’] будет иметь значение true, только если в блоке в редакторе включена кнопка полной высоты.
     *      По умолчанию false.
     * mode
     *      Это свойство позволяет пользователю переключаться между режимами редактирования и
     *      предварительного просмотра с помощью кнопки. По умолчанию истинно.
     *
     * multiple
     *      Это свойство позволяет добавлять блок несколько раз. По умолчанию истинно.
     *
     */
    private array|null $supports = null;

    /**
     * (Массив) (Необязательно) Массив структурированных данных,
     * используемый для создания предварительного просмотра, отображаемого в блоке вставки.
     * Все значения, введенные в массив атрибутов «данные», станут доступны в
     * шаблоне/обратном вызове рендеринга блока через $block['data'] или get_field().
     */
    private array|null $example = null;

    public array|string|null $selectors = null;

    /**
     * @return void
     * 1
     */
    public function __construct($name,$title,$category,$template)
    {
        $this->name = $this->set_name($name);
        $this->title =$this->set_title($title);
        $this->category = $this->set_category($category);
        $this->render_template = $this->set_template_path($template);

    }

    /**
     * @param string $name
     * @return string|null
     */
    protected function set_name(string $name):?string
    {
        if(null === $name) throw Errors::init(Errors::ERROR['name']);
        return $name;
    }

    protected function set_title(string $title):?string
    {
        if(null === $title) throw Errors::init(Errors::ERROR['title']);
        return $title;
    }

    /**
     * @param string $category
     * @return string [ common | formatting | layout | widgets | embed ]
     */
    protected function set_category(string|null $category):?string
    {
//        if(null === $category) throw Errors::init(Errors::ERROR['category']);
        return  $category;
    }

    /**
     * @param string $template_path
     * @return string|null
     */
    protected function set_template_path(string $template_path):?string
    {
        if(null === $template_path) throw Errors::init(Errors::ERROR['template']);
        return $template_path;
    }

    /**
     * Testimonial Block Callback Function.
     *
     * @param   array $block The block settings and attributes.
     * @param   string $content The block inner HTML (empty).
     * @param   bool $is_preview True during AJAX preview.
     * @param   (int|string) $post_id The post ID this block is saved to.
     */
    public function handler():?array
    {

        return acf_register_block_type(
            [
                'name'              =>  mb_strtolower($this->name),

                'title'             =>  $this->title,

                'description'       =>  $this->description,

                'category'          =>  mb_strtolower($this->category),

                'icon'              =>  $this->icon,

                'keywords'          =>  $this->keywords,

                'post_types'        =>  $this->post_types,

                'mode'              =>  $this->mode,

                'align'             =>  $this->align,

                'align_text'        =>  $this->align_text,

                'align_content'     =>  $this->align_content,

                'render_template'   =>  $this->render_template,

//                'render_callback'   =>  $this->render_callback,
                'render_callback'   =>  null=== $this->render_callback?
                    fn()=>renderAcfBlock($this->render_template,$this->selectors,false,$debug=false):
                    $this->render_callback,

                'enqueue_style'     =>  $this->enqueue_style,

                'enqueue_script'    =>  $this->enqueue_script,

                'enqueue_assets'    =>  $this->enqueue_assets,

                'supports'          =>  $this->supports,

                'example'           =>  $this->example,
            ]
        );



    }

    /**
     * @return void
     * 3
     */
    public function init()
    {

        add_action('acf/init', [$this,'handler']);
    }
}