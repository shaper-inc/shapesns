<?php

namespace ShapeSns;

abstract class Base
{

    private static $instances = array();
    abstract protected function __construct(array $argument = array());
    final public static function get_instance(array $argument = array())
    {
        $class_name = get_called_class();
        if (!isset(self::$instances[$class_name])) {
            self::$instances[$class_name] = new $class_name($argument);
        }
        return self::$instances[$class_name];
    }
}
