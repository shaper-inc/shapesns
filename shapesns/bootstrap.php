<?php

class ClassLoader
{
    public static function loadClass($class)
    {
        foreach (self::directories() as $directory) {
            $file_name = str_replace(
                "\\",
                "/",
                $directory . DIRECTORY_SEPARATOR . $class . ".php"
            );

            if (is_file($file_name)) {
                require $file_name;
                return true;
            }
        }
    }

    private static $dirs;

    private static function directories()
    {
        if (empty(self::$dirs)) {
            $base = __DIR__;
            self::$dirs = array(
                $base,
            );
        }

        return self::$dirs;
    }

    public static function app_instance($appclass_name)
    {
        require_once __DIR__ . '/vendor/autoload.php';
        Timber\Timber::init();

        spl_autoload_register(array('ClassLoader', 'loadClass'));
        return call_user_func(array($appclass_name, 'get_instance'));
    }
}
