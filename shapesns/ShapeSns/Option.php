<?php

namespace ShapeSns;

class Option extends Base
{
	protected string $key = "ShapeSns";
	protected array $option = [
		'chat_gpt_key' => '',
	];

	public function __construct(array $argument = [])
	{
		$opt = get_option($this->key, []);
		if ($opt != null) {
			$this->option = array_merge($this->option, $opt);
		}
	}

	public function __get($name)
	{
		if (array_key_exists($name, $this->option)) {
			return $this->option[$name];
		}
	}

	public function __set($name, $value)
	{
		error_log("__set: $name = $value ");
		if (array_key_exists($name, $this->option)) {
			$this->option[$name] = $value;
		} else {
			error_log("__set: $name not found");
		}
	}

	public function get()
	{
		return $this->option;
	}

	public function update($values = null): void
	{
		if ($values != null) {
			foreach ($values as $k => $v) {
				$this->option[$k] = $v;
			}
		}
		update_option($this->key, $this->option);
	}
}
