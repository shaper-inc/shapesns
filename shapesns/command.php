<?php

class ShapeSns_Command
{
    protected $app = null;

    function __construct(array $argument = array())
    {
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "bootstrap.php";
        $this->app = ClassLoader::app_instance('\\ShapeSns\\App');
    }

    function summarize_post($args, $assoc_args)
    {
        list($post_id) = $args;
        $this->app->summarize_post($post_id);
    }
}

WP_CLI::add_command('shapesns', 'ShapeSns_Command');
