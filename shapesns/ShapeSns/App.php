<?php

namespace ShapeSns;

class App extends AppBase
{
    protected $admin = null;
    protected $option = null;

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

    # TOOD:
    # wordpress hook を実装する
}
