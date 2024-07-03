<?php

namespace ShapeSns;

use OpenAI;

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

	public function preprocess_text($text): string
	{
		$text = preg_replace('/[\x00-\x1F\x7F]/', '', $text);
		$text = preg_replace('/\\\r\\\n|\\\r|\\\n/', ' ', $text);
		$text = preg_replace('/\s+/', ' ', $text);
		return strip_tags($text);
	}

	public function summarize_text($title, $content): string
	{
		$processedContent = $this->preprocess_text($content);

		$option = Option::get_instance();
		$apiKey = $option->openai_api_key;
		$promptTemplate = $option->prompt;
		$model = $option->openai_model;
		$lang = $option->language;

		$prompt = str_replace('{Title}', $title, $promptTemplate);
		$prompt = str_replace('{Article}', $processedContent, $prompt);
		$prompt = str_replace('{Language}', $lang, $prompt);

		# debug 出来上がったpromptを出力
		error_log($prompt);

		$client = OpenAI::client($apiKey);

		$result = $client->chat()->create([
			'model' => $model,
			'messages' => [
				['role' => 'user', 'content' => $prompt],
			],
		]);

		return $result->choices[0]->message->content;
	}

	public function summarize_post($post_id): string
	{
		$wp_post = get_post($post_id);

		if ($wp_post) {
			$title = $wp_post->post_title;
			$content = $wp_post->post_content;
			$summary = $this->summarize_text($title, $content);
			if ($summary) {
				update_post_meta($post_id, $this->meta_key, $summary);
			}

			return $summary;
		}

		return '';
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
		<meta name="<?php echo $this->meta_key ?>" content="<?php echo $postmeta_value ?>" />
<?php
	}

	public function action_save_post($post_id)
	{
		# wp_postが保存された時の処理
		$this->summarize_post($post_id);
	}
}
