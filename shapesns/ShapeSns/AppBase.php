<?php

namespace ShapeSns;

class AppBase extends Base
{

	protected function __construct(array $argument = [])
	{
		$this->_install_hook();
	}

	protected function _install_hook(): void
	{
		foreach (get_class_methods($this) as $fn) {
			preg_match("/^(?P<hook>[^_]+)_(?P<name>.+)$/", $fn, $match);
			if (
				$match != null &&
				in_array($match['hook'], ['action', 'filter'])
			) {
				$hook = "add_" . $match['hook'];
				$hook($match['name'], array($this, $fn));
			}
		}
	}
}
