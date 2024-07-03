<?php

class ShapeSns_Command
{
	protected $app = null;

	public function __construct(array $argument = array())
	{
		require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "bootstrap.php";
		$this->app = ClassLoader::app_instance('\\ShapeSns\\App');
	}

	public function summarize_post($args, $assoc_args)
	{
		list($post_id) = $args;
		$result = $this->app->summarize_post($post_id);
		WP_CLI::line($result);
	}
}

WP_CLI::add_command('shapesns', 'ShapeSns_Command');
