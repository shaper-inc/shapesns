<?php

namespace ShapeSns;

abstract class Base
{

	private static array $instances = [];

	abstract protected function __construct(array $argument = []);

	final public static function get_instance(array $argument = [])
	{
		$class_name = get_called_class();
		if (!isset(self::$instances[$class_name])) {
			self::$instances[$class_name] = new $class_name($argument);
		}
		return self::$instances[$class_name];
	}
}
