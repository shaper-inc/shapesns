<?php

namespace ShapeSns;

class App extends AppBase
{
	protected mixed $admin = null;
	protected mixed $option = null;
	protected mixed $meta_key = "記事要約";   // TODO 設定

	public function __construct(array $argument = [])
	{
		parent::__construct($argument);
		$this->admin = Admin::get_instance();
		$this->option = Option::get_instance();
	}

	# 管理画面
	public function action_admin_menu()
	{
		add_options_page(
			'ShapeSns options',
			'ShapeSns',
			'manage_options',
			'ShapeSns',
			array($this->admin, 'options_page')
		);
	}

	public function summarize_text($text): string
	{
		return strip_tags($text);   // 不要文字を抜く
	}

	public function summarize_post($post_id): void
	{
		$wp_post = get_post($post_id);

		if ($wp_post) {
			$content = $wp_post->post_content;
			$title = $wp_post->post_title;
			$summary = $this->summarize_text($content);
			if ($summary) {
				update_post_meta($post_id, $this->meta_key, $summary);
			}
		}
	}

	public function action_wp_enqueue_scripts(): void
	{
		# Javascriptを配信
		# https://developer.wordpress.org/reference/functions/wp_enqueue_script/
		# https://developer.wordpress.org/reference/functions/plugins_url/
		wp_enqueue_script('shapesns', plugins_url('/js/shapesns.js', dirname(__FILE__)));
	}

	public function action_wp_head(): void
	{
		# ヘッダーに要約を入れる
		$post = get_post();
		if (!$post) {
			return;
		}
		$postmeta_value = get_post_meta($post->ID, $this->meta_key, true);
		if (!$postmeta_value) {
			return;
		}
		?>
		<meta name="<?php echo $this->meta_key ?>" content="<?php echo $postmeta_value ?>"/>
		<?php
	}

	# TODO: wordpress hook を実装する
}
