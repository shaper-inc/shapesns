<?php

namespace ShapeSns;

class App extends AppBase
{
    protected $admin = null;
    protected $option = null;
    protected $meta_key = "記事要約";   // TODO 設定

	function __construct(array $argument = array())
	{
		parent::__construct($argument);
		$this->admin = Admin::get_instance();
		$this->option = Option::get_instance();
	}

	# 管理画面
	function action_admin_menu()
	{
		add_options_page(
			'ShapeSns options',
			'ShapeSns',
			'manage_options',
			'ShapeSns',
			array($this->admin, 'options_page')
		);
	}

    function summarize_text($text)
    {
        return strip_tags($text);   // 不要文字を抜く
    }

    function summarize_post($post_id)
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

    function action_wp_enqueue_scripts()
    {
        # Javvascriptを配信
        # https://developer.wordpress.org/reference/functions/wp_enqueue_script/
        # https://developer.wordpress.org/reference/functions/plugins_url/
        wp_enqueue_script('shapesns', plugins_url('/js/shapesns.js', dirname(__FILE__)));
    }

    function action_wp_head()
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
        <meta name="<?php echo $this->meta_key ?>" content="<?php echo  $postmeta_value ?>" />
<?php

    }

    # TOOD:
    # wordpress hook を実装する
}
