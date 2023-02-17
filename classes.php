<?php

if((float) phpversion()<8.1)
    wp_die("min version php 8.1, current version ".phpversion());
/**
 * Автозагрузчик классов
 */
require_once (get_template_directory().'/autoload.php');

/**
 * Подключение модулей Composer
 */
require_once (get_template_directory().'/vendor/autoload.php');

/**
 * Класс для дебагинга
 * и
 * Вывода ошибок
 */
new \Classes\Debugger\Debugger();
new \Classes\Debugger\ErrorHandler();

/**
 * Стандартные настройки шаблона
 */
new Classes\TemplateSetup\TemplateSetup();

/***
 * Отключение стандартного поля ввода контента
 */
new Classes\DisableContentEditor\DisableContentEditor();



/***
 * Класс который должен отвечать за
 * импорт товаров из фида
 * Настройка через wp-cron
 */
new Classes\Import\Init();

/**
 * Настройки шаблонизатора TWIG
 */
new Classes\TwigSettings\TwigSettings();


/**
 * Подключение стилей и скриптов
 */
new Classes\ScriptsAndStyles\RegisterScriptsAndStyle();


/**
 * Отключение пунктов меню
 */
new Classes\DisableAdminMenu\DisableAdminMenu();

/**
 * Кастомные типы записей и таксономий
 */
new \Classes\CustomTypes\CustomPostType();
new \Classes\CustomTypes\CustomTaxonomy();



