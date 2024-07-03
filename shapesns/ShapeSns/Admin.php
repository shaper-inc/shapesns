<?php

namespace ShapeSns;

class Admin extends Base
{

	public function __construct(array $argument = [])
	{
	}

	public function options_page(): void
	{
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		$ctx = \Timber::context();

		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$ctx['form'] = Option::get_instance()->get();
		} else {
			$ctx['form'] = $_POST;
			Option::get_instance()->update($_POST);
		}

		\Timber::render('Admin.html', $ctx);
	}
}
