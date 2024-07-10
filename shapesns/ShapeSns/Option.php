<?php

namespace ShapeSns;

class Option extends Base
{
	protected string $key = "ShapeSns";
	protected array $option = [
		'openai_api_key' => '',
		'openai_model' => 'gpt-3.5-turbo',
		'prompt' => "Please summarize the following article in a concise manner in {Language}. The summary should highlight the main points and key details while being under 100 words. Write in a formal tone suitable for a general audience, making the content informative and easy to understand. Ensure the summary is in {Language} and maintains the same meaning as the original article.

# Title: {Title}

# Article: {Article}

# Summary:",
		"language" => 'en',
		"post_types" => [],
	];

	protected array $openai_models = [
		'gpt-3.5-turbo',
		'gpt-4',
		'gpt-4-turbo',
		'gpt-4o',
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
		} else {
			error_log("__get: $name not found");
			return null;
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

	public function get_models(): array
	{
		return $this->openai_models;
	}

	public function get_post_types(): array
	{
		# 定義されている投稿タイプ
		return get_post_types();
	}
}
